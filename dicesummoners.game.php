<?php
 /**
  *------
  * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
  * DiceSummoners implementation : © Eoin Costelloe <eoin@dag.irish>
  * 
  * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
  * See http://en.boardgamearena.com/#!doc/Studio for more information.
  * -----
  * 
  * dicesummoners.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */


require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );


class DiceSummoners extends Table
{
    const MAX_HEALTH = 30;
    const MAX_CREATURES = 6;
    const MAX_SPELLS = 6;
    const CURSE_TIMER = 3;

	function __construct( )
	{
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();
        
        self::initGameStateLabels( array( 
            "firstPlayerId" => 10,
            "firstTurn" => 11,
            "selectedCardId" => 12,
            "selectedActionId" => 13,
            "selectedCommunityCardType" => 14,
            "selectedCommunityCard" => 15,
            "switchPlayer" => 16,
            "selectedDamageType" => 17,
            "selectedDamage" => 18,
            "deck_set" => 100,
        ) );        
        
        $this->cards = self::getNew( "module.common.deck" );
        $this->cards->init( "card" );
	}
	
    protected function getGameName( )
    {
		// Used for translations and stuff. Please do not modify.
        return "dicesummoners";
    }	

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {    
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];
 
        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar, health, player_score, curse_timer) VALUES ";
        $values = array();
        $defaultHealth = self::MAX_HEALTH;
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."','".$defaultHealth."','".$defaultHealth."','-1')";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();
        
        /************ Start the game initialization *****/
        $players_turn = $this->getNextPlayerTable(); 
        self::setGameStateInitialValue( "firstPlayerId", $players_turn["0"] );
        self::setGameStateInitialValue( 'firstTurn', 2 );
        self::setGameStateInitialValue( 'selectedCardId', 0 );
        self::setGameStateInitialValue( 'selectedActionId', 0 );
        self::setGameStateInitialValue( 'selectedCommunityCardType', 0 );
        self::setGameStateInitialValue( 'selectedCommunityCard', 0 );
        self::setGameStateInitialValue( 'switchPlayer', 0 );
        self::setGameStateInitialValue( 'selectedDamageType', 0 );
        self::setGameStateInitialValue( 'selectedDamage', 0 );
        
        // Create cards
        $cards = array ();

        foreach ( $this->decks[self::getGameStateValue( "deck_set" )] as $deckCard ) {
            $card = $this->deck_cards[$deckCard];
            $cards [] = array ('type' => $card["name"],'type_arg' => $card["slot"],'nbr' => $card["quantity"] );
        }

        foreach ( $this->setup_cards as $setup_card ) {
            $card = $this->deck_cards[$setup_card];
            $cards [] = array ('type' => $card["name"],'type_arg' => $card["slot"],'nbr' => 2 );
        }

        $this->cards->createCards( $cards, 'deck' );
        
        $players = self::loadPlayersBasicInfos();
        $players_turn = $this->getNextPlayerTable(); 

        foreach ( $players as $player_id => $player ) {
            self::setupPlayerCards($player_id);
        } 

        /************ End of the game initialization *****/
        
        self::initStat( 'player', 'diceRolled', 0 );
        self::initStat( 'player', 'damage', 0 );
        self::initStat( 'player', 'healing', 0 );
        self::initStat( 'player', 'summonedCreatures', 0 );
        self::initStat( 'player', 'learnedSpells', 0 );
        self::initStat( 'player', 'castCurses', 0 );
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array();
    
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );
        
        // load up all the card descriptions first for tooltips
        $result['card_descriptions'] = $this->card_descriptions;

        // Cards in players hand
        $result['creatures'] = array();
        $result['spells'] = array();
        $result['curse'] = array();
        $result['aura'] = array();

        $players = self::getPlayersAdditionnalInfo();
        foreach( $players as $player_id => $player )
        {
            $result['creatures'][$player_id] = $this->cards->getCardsInLocation( 'creatures', $player_id );
            $result['spells'][$player_id] = $this->cards->getCardsInLocation( 'spells', $player_id );

            $result['curse'][$player_id] = $this->cards->getCardsInLocation( 'curse', $player_id );
            $result['curse_timer'][$player_id] = $player['curse_timer'];

            $result['aura'][$player_id] = $this->cards->getCardsInLocation( 'aura', $player_id );
            
            $result['defense_token'][$player_id] = self::getDefenseTokens( $player_id );
            $result['disabled_action'][$player_id] = self::getUsedCardActions( $player_id );

            $result['dice'][$player_id] = self::getDice( $player_id );
        }

        // Cards in community
        $result['community'] = $this->cards->getCardsInLocation( 'deck' );
        $result['community_layout'] = $this->decks[self::getGameStateValue( "deck_set" )] ;

        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        $lowestHealth = self::MAX_HEALTH;
        $players = self::getPlayersAdditionnalInfo();
        
        foreach( $players as $player_id => $player ) {
            if($player['health'] < $lowestHealth) {
                $lowestHealth = $player['health'];
            }
        }
        
        return round((self::MAX_HEALTH-$lowestHealth)/self::MAX_HEALTH*100);
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    function setupPlayerCards($player_id)
    {
        foreach ( $this->setup_cards as $setup_card ) {
            self::moveCardTypeToPlayer($setup_card, $player_id);
        }
    }

    function moveCardTypeToPlayer($card_type, $player_id)
    {
        $card_types = $this->cards->getCardsOfTypeInLocation($card_type, null, 'deck', null);

        if(!empty($card_types))
        {
            $first_card = array_values($card_types)[0];
            self::moveCardToPlayer($first_card, $player_id);
        }
    }

    function moveCardToPlayer($card, $player_id)
    {
        $deck_card = $this->deck_cards[$card['type']];
        $type_string = $this->card_types[$deck_card['card_type']];

        $card_location = "";

        // if it's a spell or ritual
        if($type_string == "spell" ||
            $type_string == "ritual") {
            $card_location = "spells";
        }
        // if it's an curse
        else if($type_string == "curse") {
            $card_location = "curse";
        }
        // if it's an aura
        else if($type_string == "aura") {
            $card_location = "aura";
        }
        // else it's a creature
        else {
            $card_location = "creatures";
        }

        $this->cards->moveCard($card['id'], $card_location, $player_id);

        if(!empty($deck_card["abilties"])) {
            foreach ($deck_card["abilties"] as $ability_id => $ability) {
                $ability_string = $this->card_types[$ability['type']];
    
                if($ability_string == "defend") {
                    self::addDefenseToken($card['id'], $ability_id, $player_id, $deck_card['card_type']);
                }
            }
        }
    }

    function discardCard($card, $player_id) {
        $deck_card = $this->deck_cards[$card['type']];
        
        // discard the card
        $this->cards->moveCard( $card['id'], "discard" );
        
        self::notifyAllPlayers( "sacrifice", clienttranslate( '${player_name} has sacrificed ${card_name}' ), array(
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card['type'],
            'player_id' => $player_id,
            'card_type' => $deck_card['card_type'],
            'card_id' => $card['id']
        ) );
    }

    function setupPlayerDice($player_id, $blue_count, $red_count)
    {
        // first add in blue dice
        for($i = 0; $i < $blue_count; $i++) {
            self::addDice(0, $player_id);
        }
        
        // second add in red dice
        for($i = 0; $i < $red_count; $i++) {
            self::addDice(1, $player_id);
        }

        // then go through all the creatures to find what dice they give
        $creature_cards = $this->cards->getCardsInLocation( 'creatures', $player_id );
        foreach ( $creature_cards as $creature_card ) {
            $deck_card = $this->deck_cards[$creature_card['type']];

            for($i = 0; $i < $deck_card['gain_blue']; $i++) {
                self::addDice(0, $player_id);
            }

            for($i = 0; $i < $deck_card['gain_red']; $i++) {
                self::addDice(1, $player_id);
            }
        }
    }

    // get every info added to player TABLE
    function getPlayersAdditionnalInfo()
    {
        $sql = "SELECT player_id AS id, player.* FROM player";
        $players_info = self::getCollectionFromDb( $sql );
        
        return $players_info;
    }

    function checkFirstTurn(&$deck_card)
    {
        if( self::getGameStateValue( "firstTurn" ) == 1 ) {
            foreach( $deck_card['abilties'] as $ability_id => &$ability ) {
                if( $ability["type"] == 0 ||
                $ability["type"] == 3 ) {
                    unset($deck_card['abilties'][$ability_id]);
                }
            }
        }
    }

    function damageHealth($damage_player, $value, $type)
    {
        // reduce damage down to player's health if it's more
        if($damage_player['health'] < $value) {
            $value = $damage_player['health'];
        }

        self::updatePlayerHealth($damage_player['id'], ($damage_player['health'] - $value));
        
        self::notifyAllPlayers( "damage", clienttranslate( '${player_name} has been damaged by ${value}' ), array(
            'player_name' => $damage_player['player_name'],
            'value' => $value
        ) );

        $newScores = self::getCollectionFromDb( "SELECT player_id, player_score FROM player", true );
        self::notifyAllPlayers( "newScores", "", array(
            "scores" => $newScores
        ) );

        // increase the stats of the other player
		$players = self::loadPlayersBasicInfos();
        foreach( $players as $player_id => $player ) {
            if($damage_player['player_id'] != $player_id) {
                self::incStat( $value, 'damage', $player_id);

                // if it's steal damage then increase health
                if($type == 3) {
                    self::heal($player_id, $value);
                }
            }
        }
    }

    function heal($player_id, $value)
    {
        $players = self::getPlayersAdditionnalInfo();
        $player = $players[$player_id];

        $new_health = $player['health'] + $value;
        if($new_health > self::MAX_HEALTH) {
            $new_health = self::MAX_HEALTH;
        }

        $value_applied = $new_health - $player['health'];

        self::updatePlayerHealth($player_id, $new_health);
        
        self::notifyAllPlayers( "heal", clienttranslate( '${player_name} has been healed by ${value}' ), array(
            'player_name' => $player['player_name'],
            'value' => $value_applied
        ) );

        $newScores = self::getCollectionFromDb( "SELECT player_id, player_score FROM player", true );
        self::notifyAllPlayers( "newScores", "", array(
            "scores" => $newScores
        ) );

        self::incStat( $value_applied, 'healing', $player_id);
    }

    function updatePlayerHealth($player_id, $health)
    {
        $sql = "UPDATE player SET health = $health, player_score = $health WHERE player_id = $player_id";
        self::dbQuery($sql);
    }

    function startCurseTimer($player_id)
    {
        $value = self::CURSE_TIMER;

        self::updateCurseTimer($player_id, $value);
    }

    function updateCurseTimer($player_id, $value)
    {
        $sql = "UPDATE player SET curse_timer = $value WHERE player_id = $player_id";
        self::dbQuery($sql);
    }

    function addDefenseToken($card_id, $action_id, $player_id, $card_type)
    {
        $sql = "INSERT INTO defense_token (card_id, action_id, value, player_id, card_type) VALUES ($card_id, $action_id, 0, $player_id, $card_type)";
        self::dbQuery($sql);
    }

    function getDefenseTokens($player_id)
    {
        return self::getObjectListFromDB( "SELECT * FROM defense_token WHERE value!='0' and player_id='$player_id'" );
    }

    function getDefenseTokensForCard($card_id)
    {
        return self::getObjectListFromDB( "SELECT * FROM defense_token WHERE value!='0' and card_id='$card_id'" );
    }

    function getDefenseTokenValue($card_id, $action_id)
    {
        return self::getUniqueValueFromDB( "SELECT value FROM defense_token WHERE card_id='$card_id' and action_id='$action_id'" );
    }

    function updateDefenseToken($card_id, $action_id, $value)
    {
        $sql = "UPDATE defense_token SET value = $value WHERE card_id = $card_id and action_id = $action_id";
        self::dbQuery($sql);
    }

    function nextDefenseTokenState()
    {
        $switch_player = self::getGameStateValue( "switchPlayer" );

        if($switch_player) {
            $this->gamestate->nextState( "selectDefenseTokensPlayer" );
        } else {
            self::nextTurn();
        }
    }

    function addDice($dice_colour, $player_id)
    {
        $random_roll = bga_rand(0, 5);
        $dice_value = 0;

        $dice_colour_string = $this->dice_colours[$dice_colour];

        if($dice_colour_string === "blue") {
            $dice_value = $this->blue_dice[$random_roll];
        } else if($dice_colour_string === "red") {
            $dice_value = $this->red_dice[$random_roll];
        }

        //TODO there's gotta be a better way of doing this other than 3 arrays in materials.inc.php
        $dice_value_string = $this->action_cost_types[$dice_value];
        $dice_slot_string = $dice_colour_string."_".$dice_value_string;
        $dice_slot = $this->dice_options[$dice_slot_string];

        $sql = "INSERT INTO dice (dice_value, dice_colour, dice_slot, player_id) VALUES ($dice_value, $dice_colour, $dice_slot, $player_id)";
        self::dbQuery($sql);

        self::incStat( 1, 'diceRolled', $player_id);
    }

    function getDice($player_id)
    {
        return self::getObjectListFromDB( "SELECT * FROM dice WHERE player_id='$player_id'" );
    }

    function getDiceFromList($dice_list_string)
    {
        return self::getObjectListFromDB( "SELECT * FROM dice WHERE dice_id in ($dice_list_string)" );
    }

    function countDice($player_id)
    {
        return self::getUniqueValueFromDB( "SELECT COUNT(*) FROM dice WHERE player_id='$player_id'" );
    }

    function countDiceType($dice_colour, $player_id)
    {
        return self::getUniqueValueFromDB( "SELECT COUNT(*) FROM dice WHERE dice_colour='$dice_colour' and player_id='$player_id'" );
    }

    function deleteDice($player_id)
    {
        self::DbQuery( "DELETE FROM dice WHERE player_id='$player_id'" );
    }

    function deleteDiceFromList($dice_list_string)
    {
        self::DbQuery( "DELETE FROM dice WHERE dice_id in ($dice_list_string)" );
    }

    function deleteDiceFirstBlue($player_id)
    {
        $dice_list = self::getObjectListFromDB( "SELECT * FROM dice WHERE player_id='$player_id' and dice_colour='0'" );
        if(!empty($dice_list)) {
            $first_dice_id = array_values($dice_list)[0]['dice_id'];
            self::DbQuery( "DELETE FROM dice WHERE dice_id='$first_dice_id'" );
        }
    }

    function doDiceMatch($dice_list, $abilty_costs, $exact_match)
    {
        // calculate up the totals of the dice
        $dice_totals = array(0, 0, 0, 0);
        foreach ( $dice_list as $dice ) {
            $dice_totals[$dice["dice_value"]] += 1;
        }
        
        $dice_match = true;
        $dice_difference_total = 0;
        foreach ( $abilty_costs as $abilty_cost_id => $abilty_cost ) {
            $dice_difference = $abilty_cost - $dice_totals[$abilty_cost_id];

            // You have too many of a specific dice selected
            if($exact_match && $dice_difference < 0) {
                $dice_match = false;
            }

            // we only care about when the difference is positive
            // showing we don't have enough dice of this type
            if($dice_difference > 0)
                $dice_difference_total += $dice_difference;
        }

        // can the star dice resolve the difference?
        // if we want an exact match, they need to be equal
        if($exact_match && $dice_difference_total != $dice_totals[3]) {
            $dice_match = false;
        }
        // if we want a match, we need to have the same or more star dice
        else if(!$exact_match && $dice_difference_total > $dice_totals[3]) {
            $dice_match = false;
        }

        return $dice_match;
    }

    function cardActionUsed($card_id, $action_id, $player_id, $card_type)
    {
        $sql = "INSERT INTO used_card_action (card_id, action_id, player_id, card_type) VALUES ($card_id, $action_id, $player_id, $card_type)";
        self::dbQuery($sql);
    }

    function getUsedCardActions($player_id)
    {
        return self::getObjectListFromDB( "SELECT * FROM used_card_action WHERE player_id='$player_id'" );
    }

    function getUsedCardActionsForCard($card_id)
    {
        return self::getObjectListFromDB( "SELECT * FROM used_card_action WHERE card_id='$card_id'" );
    }

    function countCardActionUsed($card_id, $action_id, $player_id)
    {
        return self::getUniqueValueFromDB( "SELECT COUNT(*) FROM used_card_action WHERE card_id='$card_id' and action_id='$action_id' and player_id='$player_id'" );
    }

    function countCardUsed($card_id, $player_id)
    {
        return self::getUniqueValueFromDB( "SELECT COUNT(*) FROM used_card_action WHERE card_id='$card_id' and player_id='$player_id'" );
    }

    function countActivatedCardActions($card_id)
    {
        return self::getUniqueValueFromDB( "SELECT COUNT(*) FROM ( SELECT u.action_id FROM `used_card_action` u WHERE u.card_id='$card_id' UNION SELECT d.action_id FROM `defense_token` d WHERE d.value!='0' and d.card_id='$card_id') as o" );
        
    }

    function deleteCardActionsUsed()
    {
        $sql = "DELETE FROM used_card_action";
        self::dbQuery($sql);
    }

    function deleteCardActionUsed($card_id, $action_id)
    {
        $sql = "DELETE FROM used_card_action WHERE card_id = $card_id and action_id = $action_id";
        self::dbQuery($sql);
    }

    function useCardAction( $card_id, $action_id )
    {
        $current_player_id = self::getActivePlayerId();

        $card = $this->cards->getCard($card_id);
        $deck_card = $this->deck_cards[$card['type']];
        self::checkAura($deck_card, $card_id);

        self::cardActionUsed($card_id, $action_id, $current_player_id, $deck_card['card_type']);
        
        self::notifyAllPlayers( "disabled_action", "", array(
            'action_id' => $action_id,
            'player_id' => $current_player_id,
            'card_id' => $card_id,
            'card_type' => $deck_card["card_type"],
        ) );
        
        $ability = $deck_card["abilties"][$action_id];
        $type = $ability["type"];
        $type_string = $this->card_types[$type];

        if($type_string == "attack" ||
            $type_string == "steal") {
            $value = $ability["value"];
            $select_defense_tokens = false;
        
            self::notifyAllPlayers( "attack", clienttranslate( '${player_name} selected ${type_string} for ${value}' ), array(
                'player_name' => self::getActivePlayerName(),
                'type_string' => $type_string,
                'type' => $type,
                'value' => $value
            ) );
            
            $players = self::getPlayersAdditionnalInfo();
            foreach( $players as $player_id => $player ) {
                if($current_player_id != $player_id) {
                    $defense_tokens = self::getDefenseTokens( $player_id );
                    if(empty($defense_tokens)) {
                        self::damageHealth($player, $value, $type);
                    } else {
                        $select_defense_tokens = true;
                        self::setGameStateValue( 'switchPlayer', 1 );
                        self::setGameStateValue( 'selectedDamageType', $type );
                        self::setGameStateValue( 'selectedDamage', $value );
                    }
                }
            }
    
            if($select_defense_tokens) {
                $this->gamestate->nextState( "selectDefenseTokensOpponent" );
            } else {
                self::nextTurn();
            }
        } else if($type_string == "defend") {
            $value = $ability["value"];

            self::updateDefenseToken($card_id, $action_id, $value);
            
            self::notifyAllPlayers( "defend", clienttranslate( '${player_name} has defended for ${value}' ), array(
                'player_name' => self::getActivePlayerName(),
                'value' => $value,
                'action_id' => $action_id,
                'player_id' => $current_player_id,
                'card_id' => $card_id,
                'card_type' => $deck_card["card_type"],
            ) );

            self::nextTurn();
        } else if($type_string == "heal") {
            $value = $ability["value"];
            self::heal($current_player_id, $value);

            self::nextTurn();
        }else if($type_string == "basic" ||
            $type_string == "advanced" ||
            $type_string == "spell" ||
            $type_string == "ritual" ||
            $type_string == "curse" ||
            $type_string == "aura") {
            self::setGameStateValue( 'selectedCommunityCardType', $type );
            $sacrifice_needed = false;

            // if it's a creature
            if($type_string == "basic" ||
                $type_string == "advanced") {
                self::incStat( 1, 'summonedCreatures', $current_player_id);

                if ($this->cards->countCardInLocation( 'creatures', $current_player_id ) >= self::MAX_CREATURES) {
                    $sacrifice_needed = true;
                }
            }
            // if it's a spell
            else if($type_string == "spell" ||
                $type_string == "ritual") {
                self::incStat( 1, 'learnedSpells', $current_player_id);

                if ($this->cards->countCardInLocation( 'spells', $current_player_id ) >= self::MAX_SPELLS) {
                    $sacrifice_needed = true;
                }
            }
            // if it's a curse
            else if ($type_string == "curse") {
                self::incStat( 1, 'castCurses', $current_player_id);

                // immediately sacrifice the curse
                // this needs to check the opponent's curse not the current player
                $players = self::loadPlayersBasicInfos();
                foreach( $players as $player_id => $player ) {
                    if($current_player_id != $player_id) {
                        $curses = $this->cards->getCardsInLocation( 'curse', $player_id );
                        if(!empty($curses)) {
                            $curse = array_values($curses)[0];
                            self::discardCard($curse, $player_id);
                        }
                    }
                }
            }
            // if it's an aura
            else if ($type_string == "aura") {
                // immediately sacrifice the aura
                $auras = $this->cards->getCardsInLocation( 'aura', $current_player_id );
                if(!empty($auras)) {
                    $aura = array_values($auras)[0];
                    self::discardCard($aura, $current_player_id);
                }
            }

            if ($sacrifice_needed) {
                $this->gamestate->nextState( "selectSacrifice" );
            }
            else {
                $this->gamestate->nextState( "selectCommunityCards" );
            }
        } else if($type_string == "mythic") {
            $value = $ability["value"];

            $mythic_cards = $this->cards->getCardsOfType($value);
            $mythic_card = array_values($mythic_cards)[0];
            $mythic_deck_card = $this->deck_cards[$mythic_card['type']];
            
            self::incStat( 1, 'summonedCreatures', $current_player_id);

            $community_id = array_search($mythic_card['type'], $this->decks[self::getGameStateValue( "deck_set" )]);
            self::moveCardToPlayer($mythic_card, $current_player_id);
    
            self::notifyAllPlayers( "community_card", clienttranslate( '${player_name} has selected ${card_name}' ), array(
                'player_name' => self::getActivePlayerName(),
                'card_name' => $mythic_card['type'],
                'player_id' => $current_player_id,
                'card_id' => $mythic_card["id"],
                'community_id' => $community_id,
                'slot' => $mythic_deck_card['slot'],
                'type' => $mythic_deck_card['card_type']
            ) );
    
            // don't need to worry about sacrifice in this case
            // you always need to sacrifice a creature to gain a mythic
            self::nextTurn();
        } else {
            self::nextTurn();
        }
    }

    function isGameEnd()
    {
        $players = self::getPlayersAdditionnalInfo();
        foreach( $players as $player_id => $player ) {
            if($player['health'] <= 0) {
                return true;
            }
        }

        return false;
    }

    function checkCommunityCardExists( $type, $value )
    {
        foreach ( $this->cards->getCardsInLocation( 'deck' ) as $deck_card ) {

            if(!empty($value)) {
                if($deck_card['type'] == $value) {
                    return true;
                }
            } else {
                $card = $this->deck_cards[$deck_card['type']];
                if($card['card_type'] == $type) {
                    return true;
                }
            }
        }

        return false;
    }

    function checkCurseNewTurn()
    {
        $current_player_id = self::getActivePlayerId();
        $curse = $this->cards->getCardsInLocation( 'curse', $current_player_id );

        // TODO I'm not sure how but could you convert this into a material.inc.php properties?
        if(!empty($curse)) {
            $curse_type = array_values($curse)[0]['type'];

            if( $curse_type == "Hex" ) {
                self::deleteDiceFirstBlue($current_player_id);
            } else if( $curse_type == "Achlys' Mist" ||
                $curse_type == "Swarm" ) {
                $deck_card = $this->deck_cards[$curse_type];
                $type = $deck_card['value_type'];
                $value = $deck_card['value'];
                
                $defense_tokens = self::getDefenseTokens( $current_player_id );
                if(empty($defense_tokens)) {
                    $players = self::getPlayersAdditionnalInfo();
                    self::damageHealth($players[$current_player_id], $value, $type);
                } else {
                    self::setGameStateValue( 'switchPlayer', 0 );
                    self::setGameStateValue( 'selectedDamageType', $type );
                    self::setGameStateValue( 'selectedDamage', $value );
                    
                    return true;
                }
            }
        }

        return false;
    }

    function checkCurseAction(&$deck_card)
    {
        $current_player_id = self::getActivePlayerId();
        $curse = $this->cards->getCardsInLocation( 'curse', $current_player_id );

        // TODO I'm not sure how but could you convert this into a material.inc.php properties?
        if(!empty($curse)) {
            $curse_type = array_values($curse)[0]['type'];

            if( $curse_type == "Arachne's Web" &&
                $deck_card[ 'card_type' ] == 8 ) {
                foreach( $deck_card['abilties'] as $ability_id => &$ability ) {
                    if( $ability["type"] == 0  ||
                        $ability["type"] == 1  ||
                        $ability["type"] == 2  ||
                        $ability["type"] == 3 ) {
                        unset($deck_card['abilties'][$ability_id]);
                    }
                }
            } else if( $curse_type == "Gleipnir" ) {
                foreach( $deck_card['abilties'] as $ability_id => &$ability ) {
                    if( $ability["type"] == 2 ) {
                        unset($deck_card['abilties'][$ability_id]);
                    }
                }
            }
        }
    }

    function checkCurseFinished()
    {
        $current_player_id = self::getActivePlayerId();
        $curses = $this->cards->getCardsInLocation( 'curse', $current_player_id );

        // a curse exists in the player's hand
        if(!empty($curses))
        {
            $card = array_values($curses)[0];
            $deck_card = $this->deck_cards[$card['type']];

            $players = self::getPlayersAdditionnalInfo();
            $player = $players[$current_player_id];

            $value = $player['curse_timer'];

            // is this curse finished?
            if($value == 0) {
                // discard the card
                $this->cards->moveCard( $card['id'], "discard" );

                // update curse timer
                self::updateCurseTimer($current_player_id, $value - 1);
                
                self::notifyAllPlayers( "sacrifice", clienttranslate( '${player_name} is no longer cursed' ), array(
                    'player_name' => self::getActivePlayerName(),
                    'card_name' => $card['type'],
                    'player_id' => $current_player_id,
                    'card_type' => $deck_card['card_type'],
                    'card_id' => $card['id']
                ) );
            }
        }
    }

    function checkCurseTimer()
    {
        $current_player_id = self::getActivePlayerId();
        $curses = $this->cards->getCardsInLocation( 'curse', $current_player_id );

        // a curse exists in the player's hand
        if(!empty($curses))
        {
            $card = array_values($curses)[0];

            $players = self::getPlayersAdditionnalInfo();
            $player = $players[$current_player_id];

            $value = $player['curse_timer'] - 1;
            
            self::updateCurseTimer($current_player_id, $value);

            self::notifyAllPlayers( "curse_timer", "", array(
                'card_id' => $card['id'],
                "value" => $value
            ) );
        }
    }

    function checkAura(&$deck_card, $selectedCardId)
    {
        $current_player_id = self::getActivePlayerId();
        $aura = $this->cards->getCardsInLocation( 'aura', $current_player_id );

        // TODO I'm not sure how but could you convert this into a material.inc.php properties?
        if(!empty($aura)) {
            $aura_type = array_values($aura)[0]['type'];

            if( $aura_type == "Armour" &&
                $deck_card[ 'card_type' ] == 5 ) {
                foreach( $deck_card['abilties'] as &$ability ) {
                    if( $ability["type"] == 1 ) {
                        $ability["value"] += 1;
                    }
                }
            } else if( $aura_type == "Bloodthirsty" &&
                ( $deck_card[ 'card_type' ] == 4 ||
                    $deck_card[ 'card_type' ] == 5 ||
                    $deck_card[ 'card_type' ] == 6 ) ) {
                foreach( $deck_card['abilties'] as &$ability ) {
                    if( $ability["type"] == 3 ) {
                        $ability["value"] += 1;
                    }
                }
            } else if( $aura_type == "Divine" &&
                ( $deck_card[ 'card_type' ] == 4 ||
                    $deck_card[ 'card_type' ] == 5 ||
                    $deck_card[ 'card_type' ] == 6 ) ) {
                foreach( $deck_card['abilties'] as &$ability ) {
                    if( $ability["type"] == 2 ) {
                        $ability["value"] += 1;
                    }
                }
            } else if( $aura_type == "Fury" &&
                $deck_card[ 'card_type' ] == 5 ) {
                foreach( $deck_card['abilties'] as &$ability ) {
                    if( $ability["type"] == 0 ) {
                        $ability["value"] += 1;
                    }
                }
            } else if( $aura_type == "Insight" &&
                $deck_card[ 'card_type' ] == 8 ) {
                foreach( $deck_card['abilties'] as &$ability ) {
                    if( $ability["type"] == 0 ||
                        $ability["type"] == 1 ||
                        $ability["type"] == 2 ||
                        $ability["type"] == 3 ) {
                        // check if an ability of this card has already been used
                        // don't add the bonus as this aura only applies to one ability per card
                        if( self::countCardUsed($selectedCardId, $current_player_id) == 0 ) {
                            $ability["value"] += 1;
                        }
                    }
                }
            }
        }
    }
    
    function checkDefenseTokens(&$deck_card, $selectedCardId)
    {
        foreach( $deck_card['abilties'] as $ability_id => &$ability ) {
            if( self::getDefenseTokenValue($selectedCardId, $ability_id) > 0 ) {
                unset($deck_card['abilties'][$ability_id]);
            }
        }
    }

    function checkDice(&$deck_card)
    {
        $current_player_id = self::getActivePlayerId();
        $dice_list = self::getDice($current_player_id);

        foreach( $deck_card['abilties'] as $ability_id => &$ability ) {
            if( !(self::doDiceMatch($dice_list, $ability['dice'], false)) ) {
                unset($deck_card['abilties'][$ability_id]);
            }
        }
    }

    function checkCreatureSacrifice(&$deck_card)
    {
        $current_player_id = self::getActivePlayerId();

        foreach( $deck_card['abilties'] as $ability_id => &$ability ) {
            if(!empty($ability['sacrifice_creature'])) {
                $creatures = $this->cards->getCardsInLocation( 'creatures', $current_player_id );

                if(empty($creatures)) {
                    unset($deck_card['abilties'][$ability_id]);
                }
            }
        }
    }

    function checkCardActionUsed(&$deck_card, $selectedCardId)
    {
        $current_player_id = self::getActivePlayerId();

        foreach( $deck_card['abilties'] as $ability_id => &$ability ) {
            if( self::countCardActionUsed($selectedCardId, $ability_id, $current_player_id) > 0 ) {
                unset($deck_card['abilties'][$ability_id]);
            }
        }
    }

    function checkCardActionAvailable(&$deck_card)
    {
        foreach( $deck_card['abilties'] as $ability_id => &$ability ) {
            // if it's any summon
            // check if the card exists in the coomunity
            if( $ability["type"] == 4  ||
            $ability["type"] == 5  ||
            $ability["type"] == 6  ||
            $ability["type"] == 7  ||
            $ability["type"] == 8  ||
            $ability["type"] == 9  ||
            $ability["type"] == 10 ) {
                $value = null;
                if(!empty($ability["value"])) {
                    $value = $ability["value"];
                }
                if(!self::checkCommunityCardExists( $ability["type"], $value )) {
                    unset($deck_card['abilties'][$ability_id]);
                }
            }
        }
    }

    function checkFreeCardsUsed()
    {
        $current_player_id = self::getActivePlayerId();

        // currently the only free card is the Sacrifice Set
        $free_cards = $this->cards->getCardsOfTypeInLocation('Sacrifice Set', null, 'spells', $current_player_id);

        if( !empty($free_cards) )
        {
            foreach( $free_cards as $free_card )
            {
                if( self::countActivatedCardActions($free_card['id']) != 2 )
                {
                    return false;
                }
            }
        }

        return true;
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in dicesummoners.action.php)
    */

    function pass()
    {
        self::checkAction( 'pass' ); 
        
        self::notifyAllPlayers( "pass", clienttranslate( '${player_name} has passed' ), array(
            'player_name' => self::getActivePlayerName()
        ) );
        
        self::setGameStateValue( 'selectedCardId', 0 );

        $this->gamestate->nextState( "pass" );
    }

    function selectCard( $card_id )
    {
        self::checkAction( 'selectCard' ); 
        
        self::setGameStateValue( 'selectedCardId', $card_id );

        $this->gamestate->nextState( "selectCard" );
    }
    
    function selectAction( $card_id, $action_id )
    {
        self::checkAction( 'selectAction' ); 

        self::setGameStateValue( 'selectedActionId', $action_id );
        
        $card = $this->cards->getCard($card_id);
        $abilty = $this->deck_cards[$card['type']]["abilties"][$action_id];
        $abilty_costs = $abilty["dice"];
        
        // if this ability requires no cost
        $dice_cost = false;
        foreach ( $abilty_costs as $abilty_cost_id => $abilty_cost ) {
            if($abilty_cost > 0) {
                $dice_cost = true;
            }
        }
        
        if(!$dice_cost && !empty($abilty['sacrifice_creature'])) {
            $this->gamestate->nextState( "selectSacrifice" );
        } else {
            $this->gamestate->nextState( "selectDice" );
        }
    }
    
    function selectDice( $dice_list_string )
    {
        self::checkAction( 'selectDice' );

        $dice_list_string = str_replace("_", ", ", $dice_list_string);
        $dice_list = self::getDiceFromList($dice_list_string);

        $selectedCardId = self::getGameStateValue( "selectedCardId" );
        $selectedActionId = self::getGameStateValue( "selectedActionId" );

        $card = $this->cards->getCard($selectedCardId);
        $abilty = $this->deck_cards[$card['type']]["abilties"][$selectedActionId];

        $dice_match = self::doDiceMatch($dice_list, $abilty["dice"], true);

        if($dice_match) {
            $current_player_id = self::getActivePlayerId();

            self::deleteDiceFromList($dice_list_string);
            
            self::notifyAllPlayers( "delete_dice", "", array(
                'player_name' => self::getActivePlayerName(),
                'player_id' => $current_player_id,
                'dice_list' => $dice_list
            ) );

            if (!empty($abilty['sacrifice_creature'])) {
                $this->gamestate->nextState( "selectSacrifice" );
            } else {
                self::useCardAction($selectedCardId, $selectedActionId);
            }
        }
    }

    function selectSacrifice( $card_id )
    {
        self::checkAction( 'selectSacrifice' ); 

        $current_player_id = self::getActivePlayerId();
        $card = $this->cards->getCard($card_id);

        // remove any defense tokens associated with this card
        $defense_tokens = self::getDefenseTokensForCard($card_id);
        
        if(!empty($defense_tokens)) {
            foreach ( $defense_tokens as $defense_token ) {
                self::updateDefenseToken($card_id, $defense_token['action_id'], 0);
                
                self::notifyAllPlayers( "defense_token", "", array(
                    'player_name' => self::getActivePlayerName(),
                    'player_id' => $current_player_id,
                    'card_id' => $card_id,
                    'action_id' => $defense_token['action_id'],
                    'value' => 0
                ) );
            }
        }
        
        // remove any disabled action tokens associated with this card
        $used_card_actions = self::getUsedCardActionsForCard($card_id);
        
        if(!empty($used_card_actions)) {
            foreach ( $used_card_actions as $used_card_action ) {
                self::deleteCardActionUsed($card_id, $used_card_action['action_id']);
                
                self::notifyAllPlayers( "remove_disabled_action", "", array(
                    'card_id' => $card_id,
                    'action_id' => $used_card_action['action_id'],
                ) );
            }
        }

        self::discardCard($card, $current_player_id);
        
        $selectedCommunityCardType = self::getGameStateValue( "selectedCommunityCardType" );
        if($selectedCommunityCardType != 0) {
            $this->gamestate->nextState( "selectCommunityCards" );
        } else {
            $selectedCardId = self::getGameStateValue( "selectedCardId" );
            $selectedActionId = self::getGameStateValue( "selectedActionId" );
    
            self::useCardAction($selectedCardId, $selectedActionId);
        }
    }

    function selectCommunityCard( $card_id )
    {
        self::checkAction( 'selectCommunityCard' ); 
        
        $current_player_id = self::getActivePlayerId();
        $card = $this->cards->getCard($card_id);
        $deck_card = $this->deck_cards[$card['type']];

        $move_card_to_player_id = $current_player_id;

        // if it's a curse, send it to the other player and start the timer
        if($deck_card['card_type'] == 9) {
            $players = self::loadPlayersBasicInfos();
            foreach( $players as $player_id => $player ) {
                if($current_player_id != $player_id) {
                    $move_card_to_player_id = $player_id;

                    self::startCurseTimer($player_id);
                }
            }
        }

        $community_id = array_search($card['type'], $this->decks[self::getGameStateValue( "deck_set" )]);
        self::moveCardToPlayer($card, $move_card_to_player_id);

        self::notifyAllPlayers( "community_card", clienttranslate( '${player_name} has selected ${card_name}' ), array(
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card['type'],
            'player_id' => $move_card_to_player_id,
            'card_id' => $card_id,
            'community_id' => $community_id,
            'slot' => $deck_card['slot'],
            'type' => $deck_card['card_type']
        ) );

        self::nextTurn();
    }

    function takeDamage()
    {
        self::checkAction( 'takeDamage' ); 
        
        $selectedDamageType = self::getGameStateValue( "selectedDamageType" );
        $selectedDamage = self::getGameStateValue( "selectedDamage" );
        if($selectedDamage > 0) {
            $current_player_id = self::getActivePlayerId();
            $players = self::getPlayersAdditionnalInfo();
            self::damageHealth($players[$current_player_id], $selectedDamage, $selectedDamageType);
        }

        self::nextDefenseTokenState();
    }

    function selectDefenseToken( $card_id, $action_id )
    {
        self::checkAction( 'selectDefenseToken' ); 

        $selected_damage_type = self::getGameStateValue( "selectedDamageType" );
        $selected_damage_old = self::getGameStateValue( "selectedDamage" );
        
        $current_player_id = self::getActivePlayerId();
        $defense_token_value_old = self::getDefenseTokenValue($card_id, $action_id);

        $selected_damage_new = $selected_damage_old - $defense_token_value_old;
        $defense_token_value_new = $defense_token_value_old - $selected_damage_old;

        if($selected_damage_new < 0) {
            $selected_damage_new = 0;
        }
        
        if($defense_token_value_new < 0) {
            $defense_token_value_new = 0;
        }

        $defense_token_value_difference = $defense_token_value_old - $defense_token_value_new;

        self::updateDefenseToken($card_id, $action_id, $defense_token_value_new);

        self::notifyAllPlayers( "defense_token", clienttranslate( '${player_name} has removed ${difference} defense token(s)' ), array(
            'player_name' => self::getActivePlayerName(),
            'player_id' => $current_player_id,
            'card_id' => $card_id,
            'action_id' => $action_id,
            'difference' => $defense_token_value_difference,
            'value' => $defense_token_value_new
        ) );
        
        $defense_tokens = self::getDefenseTokens( $current_player_id );
        // if there's no more damage to take
        if($selected_damage_new == 0) {
            self::nextDefenseTokenState();
        }
        // if the player has run out of defense tokens then take the rest of the damage
        else if(empty($defense_tokens)) {
            $players = self::getPlayersAdditionnalInfo();
            self::damageHealth($players[$current_player_id], $selected_damage_new, $selected_damage_type);

            self::nextDefenseTokenState();
        }
        // else continue to selet more defense tokens
        else {
            self::setGameStateValue( 'selectedDamage', $selected_damage_new );

            $this->gamestate->nextState( "selectDefenseTokens" );
        }
    }

    function nextTurn()
    {
        // reset some globals
        self::setGameStateValue( 'selectedCardId', 0 );
        self::setGameStateValue( 'selectedActionId', 0 );
        self::setGameStateValue( 'selectedCommunityCardType', 0 );
        self::setGameStateValue( 'selectedCommunityCard', 0 );
        self::setGameStateValue( 'switchPlayer', 0 );
        self::setGameStateValue( 'selectedDamageType', 0 );
        self::setGameStateValue( 'selectedDamage', 0 );

        $current_player_id = self::getActivePlayerId();
        $dice_amount = self::countDice($current_player_id);

        // is the game over?
        if(self::isGameEnd()) {
            $this->gamestate->nextState( "gameEnd" );
        }
        // if there's no more dice
        // and free cards are used
        // pass to the next player
        else if($dice_amount == 0 &&
            self::checkFreeCardsUsed()) {

            self::notifyAllPlayers( "pass", clienttranslate( '${player_name} has passed with no more dice to use' ), array(
                'player_name' => self::getActivePlayerName()
            ) );

            $this->gamestate->nextState( "pass" );
        } else {
            $this->gamestate->nextState( "playerTurn" );
        }
    }
    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    function argSelectAction()
    {
        $selectedCardId = self::getGameStateValue( "selectedCardId" );
        $card = $this->cards->getCard($selectedCardId);
        $deck_card = $this->deck_cards[$card['type']];

        self::checkFirstTurn($deck_card);
        self::checkCurseAction($deck_card);
        self::checkDefenseTokens($deck_card, $selectedCardId);
        self::checkDice($deck_card);
        self::checkCreatureSacrifice($deck_card);
        self::checkCardActionUsed($deck_card, $selectedCardId);
        self::checkCardActionAvailable($deck_card);

        self::checkAura($deck_card, $selectedCardId);

        return array(
            'selectedDeckCard' => $deck_card,
            'selectedCard' => $card
        );
    }

    function argSelectDice()
    {
        $selectedCardId = self::getGameStateValue( "selectedCardId" );
        $selectedActionId = self::getGameStateValue( "selectedActionId" );
        $card = $this->cards->getCard($selectedCardId);
        $deck_card = $this->deck_cards[$card['type']];
        $ability = $deck_card["abilties"][$selectedActionId];

        return array(
            'selectedDeckCard' => $deck_card,
            'selectedCard' => $card,
            'selectedActionId' => $selectedActionId,
            'mercury' => $ability['dice'][0],
            'salt' => $ability['dice'][1],
            'sulphur' => $ability['dice'][2]
        );
    }
    
    function argSelectSacrifice()
    {
        $selectedCardId = self::getGameStateValue( "selectedCardId" );
        $selectedActionId = self::getGameStateValue( "selectedActionId" );
        
        $card = $this->cards->getCard($selectedCardId);
        $deck_card = $this->deck_cards[$card['type']];

        $ability = $deck_card["abilties"][$selectedActionId];
        $type = $ability["type"];

        $type_name = "";
        if($type == 8 || $type == 7) {
            $type_name = "spell";
        } else {
            $type_name = "creature";
        }

        return array(
            'selectedCardType' => $type,
            'type_name' => $type_name
        );
    }

    function argSelectCommunityCards()
    {
        $selectedCommunityCardType = self::getGameStateValue( "selectedCommunityCardType" );
        
        $selectedCommunityCards = array();
        foreach ( $this->decks[self::getGameStateValue( "deck_set" )] as $deck_id => $deckCard ) {
            $card = $this->deck_cards[$deckCard];
            if($card["card_type"] == $selectedCommunityCardType) {
                $selectedCommunityCards [] = $deck_id;
            }
        }

        $card_type = $this->card_types[$selectedCommunityCardType];

        return array(
            'selectedCommunityCards' => $selectedCommunityCards,
            'card_type' => $card_type
        );
    }

    function argSelectDefenseTokens()
    {
        $selected_damage_type = self::getGameStateValue( "selectedDamageType" );
        $selected_damage = self::getGameStateValue( "selectedDamage" );

        return array(
            'selectedDamage' => $selected_damage,
            'selectedDamageType' => $selected_damage_type
        );
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */
    
    function stNewTurn()
    {
        // is any curse finished?
        self::checkCurseFinished();

        // Active next player
        $player_id = self::activeNextPlayer();
        self::giveExtraTime( $player_id );

        // if the first turn is over
        if(self::getGameStateValue( "firstTurn" ) != 0 &&
            self::getGameStateValue( "firstPlayerId" ) == $player_id) {
            self::incGameStateValue( 'firstTurn', -1 );
        }

        // clear any previous used actions
        self::deleteCardActionsUsed();
        
        self::notifyAllPlayers( "remove_disabled_actions", "", array( ) );

        // update any curse timers
        self::checkCurseTimer();

        $blue_count = self::countDiceType( 0, $player_id );
        $red_count = self::countDiceType( 1, $player_id );

        // you can only carry over a max of 2 blue and 2 red dice from previous turn
        if($blue_count > 2) {
            $blue_count = 2;
        }
        if($red_count > 2) {
            $red_count = 2;
        }
        
        self::deleteDice( $player_id );
        
        // if it's the first turn and the second player
        // they get an extra blue dice
        if(self::getGameStateValue( "firstTurn" ) == 1 &&
            self::getGameStateValue( "firstPlayerId" ) != $player_id) {
                $blue_count += 1;
        }

        self::setupPlayerDice( $player_id, $blue_count, $red_count );

        $select_defense_tokens = self::checkCurseNewTurn();

        $new_dice = self::getDice( $player_id );
        self::notifyAllPlayers( "begin_turn", clienttranslate( '${player_name} has begun their turn' ), array(
            'player_name' => self::getActivePlayerName(),
            'player_id' => $player_id,
            'new_dice' => $new_dice
        ) );
        
        // is the game over?
        if(self::isGameEnd()) {
            $this->gamestate->nextState( "gameEnd" );
        } else if($select_defense_tokens) {
            $this->gamestate->nextState( "selectDefenseTokens" );
        } else {
            $this->gamestate->nextState( "playerTurn" );
        }
    }

    function stSelectDefenseTokensOpponent()
    {
        // Active next player
        $player_id = self::activeNextPlayer();
        self::giveExtraTime( $player_id );

        $this->gamestate->nextState( "selectDefenseTokens" );
    }

    function stSelectDefenseTokensPlayer()
    {
        // Active next player
        $player_id = self::activeNextPlayer();
        self::giveExtraTime( $player_id );

        self::nextTurn();
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn( $state, $active_player )
    {
        if( $state['name'] == 'playerTurn' ||
            $state['name'] == 'selectAction' ||
            $state['name'] == 'selectDice' ||
            $state['name'] == 'selectCommunityCards' ||
            $state['name'] == 'selectDefenseTokens' ||
            $state['name'] == 'selectSacrifice' )
        {
            $this->gamestate->nextState( "gameEnd" );
        }
        else
            throw new feException( "Zombie mode not supported at this game state:".$state['name'] );
    }
    
///////////////////////////////////////////////////////////////////////////////////:
////////// DB upgrade
//////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */
    
    function upgradeTableDb( $from_version )
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345
        
        // Example:
//        if( $from_version <= 1404301345 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        if( $from_version <= 1405061421 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        // Please add your future database scheme changes here
//
//


    }    
}
