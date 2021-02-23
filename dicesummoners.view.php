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
 * dicesummoners.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in dicesummoners_dicesummoners.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */
  
  require_once( APP_BASE_PATH."view/common/game.view.php" );
  
  class view_dicesummoners_dicesummoners extends game_view
  {
    function getGameName() {
        return "dicesummoners";
    }    
  	function build_page( $viewArgs )
  	{		
  	    // Get players
        $players = $this->game->loadPlayersBasicInfos();

        /*********** Place your code below:  ************/

        $template = self::getGameName() . "_" . self::getGameName();

        global $g_user;
        $current_player_id = $g_user->get_id(); 
        $isSpectator = isset( $players[ $current_player_id ]['player_color'] ) ? false : true;

        $this->page->begin_block( $template, "player" );

        if ( !$isSpectator ) {
          $this->page->insert_block( "player", array( "PLAYER_ID" => $current_player_id,
                                                      "PLAYER_NAME" => self::_("My"),
                                                      "PLAYER_COLOR" => $players[ $current_player_id ]['player_color'] ) );
        }

        foreach( $players as $player_id => $player )
        {
          if($player_id != $current_player_id)
          {
            $this->page->insert_block( "player", array( "PLAYER_ID" => $player_id,
                                                        "PLAYER_NAME" => ($player['player_name']."'s"),
                                                        "PLAYER_COLOR" => $player['player_color'] ) );
          }
        }

        $this->tpl['DICE'] = self::_("Dice");
        $this->tpl['SPELLS'] = self::_("Spells");
        $this->tpl['CREATURES'] = self::_("Creatures");
        $this->tpl['AURA_AND_CURSE'] = self::_("Aura and Curse");
        $this->tpl['BASIC_CREATURES'] = self::_("Basic Creatures");
        $this->tpl['ADVANCED_CREATURES'] = self::_("Advanced Creatures");
        $this->tpl['MYTHIC_CREATURES'] = self::_("Mythic Creatures");
        $this->tpl['COMBAT_SPELLS'] = self::_("Combat Spells");
        $this->tpl['AURAS'] = self::_("Auras");
        $this->tpl['CURSES'] = self::_("Curses");

        /*********** Do not change anything below this line  ************/
  	}
  }
  

