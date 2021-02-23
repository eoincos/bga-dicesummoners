/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DiceSummoners implementation : © Eoin Costelloe <eoin@dag.irish>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * dicesummoners.js
 *
 * DiceSummoners user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
    "ebg/stock"
],
function (dojo, declare) {
    return declare("bgagame.dicesummoners", ebg.core.gamegui, {
        constructor: function(){
            // console.log('dicesummoners constructor');
            
            this.card_descriptions = null;

            this.spells = {};
            this.creatures = {};
            this.curse = {};
            this.aura = {};

            this.dice = {};
            this.community = {};

            this.cardWidth = 163;
            this.cardHeight = 222;
            this.cardTemplateWidth = 9;
            this.cardTemplateHeight = 5;
            
            this.diceWidth = 50;
            this.diceHeight = 50;
            this.diceTemplateWidth = 6;
        },
        
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */
        
        setup: function( gamedatas )
        {
            // console.log( "Starting game setup" );

            // load up all the card descriptions first for tooltips
            this.card_descriptions = gamedatas.card_descriptions;
            
            // Setting up player boards
            for( var player_id in gamedatas.players ) {
                this.spells[ player_id ] = this.setupPlayerCards( gamedatas.spells, "spells", player_id );
                this.creatures[ player_id ] = this.setupPlayerCards( gamedatas.creatures, "creatures", player_id );

                this.curse[ player_id ] = this.setupPlayerCards( gamedatas.curse, "curse", player_id );
                if(gamedatas.curse_timer[ player_id ] >= 0) {
                    var curses = this.curse[ player_id ].getAllItems();
                    if(Array.isArray(curses) && curses.length && curses.length > 0) {
                        this.addCurseTimerToCard( curses[0].id, gamedatas.curse_timer[ player_id ], player_id );
                    }
                }

                this.aura[ player_id ] = this.setupPlayerCards( gamedatas.aura, "aura", player_id );
                
                for(var defense_token_id in gamedatas.defense_token[player_id]) {
                    var defense_token = gamedatas.defense_token[player_id][defense_token_id];

                    this.addDefenseTokenToCard( defense_token.card_id, defense_token.action_id, defense_token.value, player_id, defense_token.card_type );
                }
                
                for(var disabled_action_id in gamedatas.disabled_action[player_id]) {
                    var disabled_action = gamedatas.disabled_action[player_id][disabled_action_id];

                    this.addDisabledActionTokenToCard( disabled_action.card_id, disabled_action.action_id, player_id, disabled_action.card_type );
                }
                
                this.setupPlayerBoard( gamedatas.players[player_id] );

                this.dice[ player_id ] = this.setupDice(gamedatas.dice, player_id);
            }
            
            for( var community_id in gamedatas.community_layout ) {
                var community_card = gamedatas.community_layout[community_id];
                this.community[ community_id ] = this.setupCommunityCards(this.gamedatas.community, community_id, community_card);
            }

            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

            // console.log( "Ending game setup" );
        },
       

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            // console.log( 'Entering state: '+stateName );

            var pass_text = _("Pass to next player");
            var pass_tooltip = _("<p>You may only keep a maximum of 2 blue dice and 2 red dice for your next turn when you pass, all other dice will be discarded</p>");
            
            switch( stateName )
            {
                case 'playerTurn':
                    if( this.isCurrentPlayerActive() ) {
                        this.spells[this.player_id].setSelectionMode(1);
                        this.creatures[this.player_id].setSelectionMode(1);

                        this.addActionButton('Pass', pass_text, 'onClickPassButton');
                        this.addTooltipHtml( 'Pass', pass_tooltip );
                    }
                    break;
                case 'selectAction':
                    if( this.isCurrentPlayerActive() ) {
                        this.spells[this.player_id].setSelectionMode(1);
                        this.creatures[this.player_id].setSelectionMode(1);
                        
                        var card = args.args.selectedCard;
                        var deckCard = args.args.selectedDeckCard;

                        for ( var i in deckCard["abilties"]) {
                            var ability = deckCard["abilties"][i];
                            var action_button_text = "";

                            // list of actions is based on card_types in materials.inc.php
                            switch(ability["type"]) {
                                case 0:
                                    action_button_text = dojo.string.substitute( _("Attack for ${value}"),
                                        {value: ability["value"]});
                                    break;
                                case 1:
                                    action_button_text = dojo.string.substitute( _("Defend for ${value}"),
                                        {value: ability["value"]});
                                    break;
                                case 2:
                                    action_button_text = dojo.string.substitute( _("Heal for ${value}"),
                                        {value: ability["value"]});
                                    break;
                                case 3:
                                    action_button_text = dojo.string.substitute( _("Steal for ${value}"),
                                        {value: ability["value"]});
                                    break;
                                case 4:
                                    action_button_text = _("Summon a basic creature");
                                    break;
                                case 5:
                                    action_button_text = _("Summon an advanced creature");
                                    break;
                                case 6:
                                    action_button_text = _("Summon the mythic creature");
                                    break;
                                case 7:
                                    action_button_text = _("Learn a ritual");
                                    break;
                                case 8:
                                    action_button_text = _("Learn a spell");
                                    break;
                                case 9:
                                    action_button_text = _("Cast a curse");
                                    break;
                                case 10:
                                    action_button_text = _("Learn an aura");
                                    break;
                            }
                            
                            var action_button_id = 'Action_'+card["id"]+"_"+i+"_"+deckCard["name"];
                            var action_button_tooltip = dojo.string.substitute( _("<p>(Mercury: ${mercury}) (Salt: ${salt}) (Sulphur: ${sulphur})</p>"),
                                {
                                    mercury: ability['dice'][0],
                                    salt: ability['dice'][1],
                                    sulphur: ability['dice'][2]
                                });

                            this.addActionButton( action_button_id, action_button_text, 'onClickActionButton' );
                            this.addTooltipHtml( action_button_id, action_button_tooltip );
                        }

                        this.addActionButton('Pass', pass_text, 'onClickPassButton');
                        this.addTooltipHtml( 'Pass', pass_tooltip );
                    }
                    break;
                case 'selectDice':
                    if( this.isCurrentPlayerActive() ) {
                        this.spells[this.player_id].setSelectionMode(1);
                        this.creatures[this.player_id].setSelectionMode(1);

                        this.dice[this.player_id].setSelectionMode(2);
                    }
                    break;
                case 'selectSacrifice':
                    if( this.isCurrentPlayerActive() ) {
                        var cardType = args.args.selectedCardType;
                        
                        // if it's a spell or ritual
                        if(cardType == 8 || cardType == 7) {
                            this.spells[this.player_id].setSelectionMode(1);
                        } else {
                            this.creatures[this.player_id].setSelectionMode(1);
                        }
                    }
                    break;
                case 'selectCommunityCards':
                    if( this.isCurrentPlayerActive() ) {
                        var communityCards = args.args.selectedCommunityCards;
                        var card_type = args.args.card_type;

                        $("community_select").insertAdjacentElement("afterend", $("community_"+card_type));

                        for ( var i in communityCards ) {
                            var communityCard = communityCards[i];
                            this.community[communityCard].setSelectionMode(1);
                        }
                    }
                    break;
                case 'selectDefenseTokens':
                    if( this.isCurrentPlayerActive() ) {

                        // prefs are defined in gameoptions.inc.php
                        var match = false;
                        var equal_defense_token = null;

                        // if we want a match
                        if (this.prefs[100].value == 2 ||
                            this.prefs[100].value == 3) {
                            var defense_tokens = dojo.query(".defense_token_"+this.player_id);
                            
                            for( var i in defense_tokens ) {
                                var defense_token = defense_tokens[i];
                                var value = parseInt(defense_token.innerHTML);

                                if(value == args.args.selectedDamage) {
                                    match = true;
                                    equal_defense_token = defense_token;
                                }
                            }
                        }

                        // if we foun a match
                        if(match) {
                            this.selectDefenseToken(equal_defense_token);
                        } else {
                            // if we are looking for lowest
                            if (this.prefs[100].value == 2 ||
                                this.prefs[100].value == 4) {
                                var defense_tokens = dojo.query(".defense_token_"+this.player_id);
                                var lowest_defense_token = null;

                                for( var i in defense_tokens ) {
                                    var defense_token = defense_tokens[i];
                                    var value = parseInt(defense_token.innerHTML);

                                    if(lowest_defense_token == null ||
                                        parseInt(lowest_defense_token.innerHTML) > value) {
                                        lowest_defense_token = defense_token;
                                    }
                                }
                                
                                this.selectDefenseToken(lowest_defense_token);
                            }
                            // else do it manually
                            else {
                                var damageType = "attack";
                                if( args.args.selectedDamageType == 3 ) {
                                    damageType = "steal";
                                }
                                var defense_text = dojo.string.substitute( _("Take ${damage} ${damageType} damage"),
                                                {
                                                    damage: args.args.selectedDamage,
                                                    damageType: damageType
                                                });
                                this.addActionButton('DefensePass', defense_text, 'onClickDefensePassButton');
                            }
                        }
                    }
                    break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            // console.log( 'Leaving state: '+stateName );
            
            switch( stateName )
            {
                case 'playerTurn':
                    if( this.isCurrentPlayerActive() ) {
                        this.spells[this.player_id].setSelectionMode(0);
                        this.creatures[this.player_id].setSelectionMode(0);
                    }
                    break;
                case 'selectAction':
                    if( this.isCurrentPlayerActive() ) {
                        this.spells[this.player_id].setSelectionMode(0);
                        this.creatures[this.player_id].setSelectionMode(0);
                    }
                    break;
                case 'selectDice':
                    if( this.isCurrentPlayerActive() ) {
                        this.spells[this.player_id].setSelectionMode(0);
                        this.creatures[this.player_id].setSelectionMode(0);

                        this.dice[this.player_id].setSelectionMode(0);
                    }
                    break;
                case 'selectSacrifice':
                    if( this.isCurrentPlayerActive() ) {
                        this.spells[this.player_id].setSelectionMode(0);
                        this.creatures[this.player_id].setSelectionMode(0);
                    }
                    break;
                case 'selectCommunityCards':
                    if( this.isCurrentPlayerActive() ) {
                        for ( var i in this.community) {
                            this.community[i].setSelectionMode(0);
                        }

                        $("community").insertAdjacentElement("afterend", $("community_curse"));
                        $("community").insertAdjacentElement("afterend", $("community_aura"));
                        $("community").insertAdjacentElement("afterend", $("community_spell"));
                        $("community").insertAdjacentElement("afterend", $("community_ritual"));
                        $("community").insertAdjacentElement("afterend", $("community_advanced"));
                        $("community").insertAdjacentElement("afterend", $("community_basic"));
                    }

                    break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
            // console.log( 'onUpdateActionButtons: '+stateName );
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
                }
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
        
        setupPlayerCards: function( gamedatas, handName, player_id )
        {
            var hand = new ebg.stock(); // new stock object for hand
            hand.create( this, $(handName+'_'+player_id), this.cardWidth, this.cardHeight );
                
            if( player_id == this.player_id ) {
                dojo.connect( hand, 'onChangeSelection', this, 'onPlayerHandSelectionChanged' );
            }

            hand.image_items_per_row = this.cardTemplateWidth;
            hand.autowidth = true;
            hand.setSelectionMode(0);
            // hand.centerItems = true;
        
            hand = this.addCardTypesToHand(hand);
            
            hand.onItemCreate = dojo.hitch( this, 'addCardTooltip' );

            for ( var i in gamedatas[player_id]) {
                var card = gamedatas[player_id][i];
                hand.addToStockWithId(card.type_arg, card.id);
            }

            return hand;
        },

        setupCommunityCards: function( gamedatas, community_id, community_name )
        {
            var hand = new ebg.stock(); // new stock object for hand
            hand.create( this, $('community_'+community_id), this.cardWidth, this.cardHeight );
            
            dojo.connect( hand, 'onChangeSelection', this, 'onCommunityCardSelectionChanged' );

            hand.image_items_per_row = this.cardTemplateWidth;
            hand.autowidth = true;
            hand.setSelectionMode(0);
            // hand.centerItems = true;
            hand.setOverlap(10, 0);
        
            hand = this.addCardTypesToHand(hand);
            
            hand.onItemCreate = dojo.hitch( this, 'addCardTooltip' );

            for ( var i in gamedatas ) {
                var card = gamedatas[i];
                if(card.type === community_name) {
                    hand.addToStockWithId(card.type_arg, card.id);
                }
            }

            return hand;
        },

        addCardTypesToHand: function( hand )
        {
            // Create cards types
            for( var y=0;y<=this.cardTemplateHeight;y++ )
            {
                for( var x=0;x<=this.cardTemplateWidth;x++ )
                {
                    // Build card type id
                    var card_type_id = (y*this.cardTemplateWidth)+x;
                    hand.addItemType( card_type_id, card_type_id, g_gamethemeurl+'img/cards.jpg', card_type_id );
                }
            }

            return hand;
        },
        
        addDefenseTokenToCard: function( card_id, action_id, value, player_id, card_type )
        {
            dojo.place( this.format_block( 'jstpl_defense_token', {
                card_id: card_id,
                action_id: action_id,
                player_id: player_id,
                value: value
            } ) , 'defense_tokens' );

            var x = 0;
            var y = 0;
            var type;

            // if it's a spell
            if(card_type == 8) {
                type = "spells";
                x = 30;

                if(action_id == "0") {
                    y = -12;
                } else if(action_id == "1") {
                    y = 69;
                }
            }
            // if it's a ritual
            else if(card_type == 7) {
                type = "spells";
                x = 22;
                y = 69;
            }
            // if it's a creature
            else {
                type = "creatures";
                y = 70;

                if(action_id == "0") {
                    x = -30;
                } else if(action_id == "1") {
                    x = 30;
                }
            }

            this.placeOnObjectPos( 'defense_token_'+card_id+'_'+action_id+'_'+player_id, type+'_'+player_id+'_item_'+card_id, x, y );
            this.attachToNewParent( 'defense_token_'+card_id+'_'+action_id+'_'+player_id, type+'_'+player_id+'_item_'+card_id );
            
            dojo.query( '#defense_token_'+card_id+'_'+action_id+'_'+player_id ).connect( 'onclick', this, 'onClickDefenseToken' );
        },
        
        addDisabledActionTokenToCard: function( card_id, action_id, player_id, card_type )
        {
            dojo.place( this.format_block( 'jstpl_disabled_action', {
                card_id: card_id,
                action_id: action_id
            } ) , 'disabled_actions' );

            var x = 0;
            var y = 0;
            var type;

            // if it's a spell
            if(card_type == 8) {
                type = "spells";
                x = 30;

                if(action_id == "0") {
                    y = -12;
                } else if(action_id == "1") {
                    y = 69;
                }
            }
            // if it's a ritual
            else if(card_type == 7) {
                type = "spells";
                x = 22;
                y = 69;
            }
            // if it's a creature
            else {
                type = "creatures";
                y = 70;

                if(action_id == "0") {
                    x = -30;
                } else if(action_id == "1") {
                    x = 30;
                }
            }

            this.placeOnObjectPos( 'disabled_action_'+card_id+'_'+action_id, type+'_'+player_id+'_item_'+card_id, x, y );
            this.attachToNewParent( 'disabled_action_'+card_id+'_'+action_id, type+'_'+player_id+'_item_'+card_id );
        },
        
        updateDefenseToken: function( card_id, action_id, player_id, value )
        {
            if(value == 0) {
                dojo.disconnect($( 'defense_token_'+card_id+'_'+action_id+'_'+player_id ));
            } else {
                $( 'defense_token_'+card_id+'_'+action_id+'_'+player_id ).innerHTML = value;
            }
        },

        selectDefenseToken: function( defense_token )
        {
            var params = defense_token.id.split('_');
            var card_id = params[2];
            var action_id = params[3];
            var player_id = params[4];

            if( player_id == this.player_id ) {
                this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/selectDefenseToken.html", {
                    card_id : card_id,
                    action_id : action_id,
                    lock : true
                }, this, function(result) {
                }, function(is_error) {
                });
            }
        },
        
        removeDisabledActionToken: function( card_id, action_id )
        {
            dojo.disconnect($( 'disabled_action_'+card_id+'_'+action_id ));
        },
        
        removeDisabledActionTokens: function( )
        {
            dojo.disconnect(dojo.query(".disabled_action"));
        },

        setupPlayerBoard: function( player )
        {
            dojo.place( this.format_block( 'jstpl_player_panel', {
                player_id: player.id,
                health: player.score
            }) , 'overall_player_board_'+player.id );
        },

        updatePlayerBoardHealth: function( player_id, value )
        {
            $( 'player_health_'+player_id ).innerHTML = value;
        },

        // updatePlayerBoardDefense: function( player_id, value )
        // {
        //     $( 'player_defense_'+player_id ).innerHTML = value;
        // },
        
        setupDice: function( gamedatas, player_id )
        {
            var hand = new ebg.stock();
            hand.create( this, $('dice_'+player_id), this.diceWidth, this.diceHeight );
                
            if( player_id == this.player_id ) {
                dojo.connect( hand, 'onChangeSelection', this, 'onPlayerDiceSelectionChanged' );
            }

            hand.image_items_per_row = this.diceTemplateWidth;
            hand.autowidth = true;
            hand.setSelectionMode(0);
            // hand.centerItems = true;
        
            hand = this.addDiceTypesToHand(hand);

            hand.onItemCreate = dojo.hitch( this, 'addDiceTooltip' );

            for ( var i in gamedatas[player_id]) {
                var dice = gamedatas[player_id][i];
                hand.addToStockWithId(dice.dice_slot, dice.dice_id);
            }

            return hand;
        },
        
        updateDice: function( new_dice, player_id )
        {
            var hand = this.dice[player_id];

            for ( var i in new_dice) {
                var dice = new_dice[i];
                hand.addToStockWithId(dice.dice_slot, dice.dice_id);
            }
        },

        addDiceTypesToHand: function( hand )
        {
            // Create dice types
            for( var x=0;x<=this.diceTemplateWidth;x++ )
            {
                // Build dice type id
                var dice_type_id = x;
                hand.addItemType( dice_type_id, dice_type_id, g_gamethemeurl+'img/dice.gif', dice_type_id );
            }

            return hand;
        },
        
        addCurseTimerToCard: function( card_id, value, player_id )
        {
            dojo.place( this.format_block( 'jstpl_curse_timer', {
                card_id: card_id,
                value: value
            } ) , 'curse_timers' );

            this.placeOnObject( 'curse_timer_'+card_id, 'curse_'+player_id+'_item_'+card_id );
            this.attachToNewParent( 'curse_timer_'+card_id, 'curse_'+player_id+'_item_'+card_id );
        },
        
        updateCurseTimer: function( card_id, value )
        {
            if(value < 0) {
                dojo.disconnect($( 'curse_timer_'+card_id ));
            } else {
                $( 'curse_timer_'+card_id ).innerHTML = value;
            }
        },
        
        addCardTooltip: function( card_div, card_type_id, card_id )
        {
            //var card_descriptions_translated = dojo.string.substitute( _("${p}"), { p: this.card_descriptions[card_type_id] } );

            //this.addTooltipHtml( card_div.id, card_descriptions_translated );

            this.addTooltipHtml( card_div.id, _( this.card_descriptions[card_type_id] ) );
        },
        
        addDiceTooltip: function( dice_div, dice_type_id, dice_id )
        {
            if(dice_type_id == 0) {
                this.addTooltipHtml( dice_div.id, _("<p>Mercury</p>") );
            } else if(dice_type_id == 1 ||
                dice_type_id == 3) {
                this.addTooltipHtml( dice_div.id, _("<p>Salt</p>") );
            } else if(dice_type_id == 4) {
                this.addTooltipHtml( dice_div.id, _("<p>Sulphur</p>") );
            } else if(dice_type_id == 2 ||
                dice_type_id == 5) {
                this.addTooltipHtml( dice_div.id, _("<p>The Star symbol can be used in place of any other dice symbol</p>") );
            }
        },

        ///////////////////////////////////////////////////
        //// Player's action
        
        /*
        
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        
        */
        
        onPlayerHandSelectionChanged: function( elementId ) {
            var player_id = elementId.split('_')[1];

            if (this.checkAction('selectCard', true)) {
                var spells = this.spells[ player_id ].getSelectedItems();
                var creatures = this.creatures[ player_id ].getSelectedItems();
                var items = spells.concat(creatures);

                if (items.length > 0) {
                    var card_id = items[0].id;
        
                    this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/selectCard.html", {
                        card_id : card_id,
                        lock : true
                    }, this, function(result) {
                    }, function(is_error) {
                    });
                }
            } else if (this.checkAction('selectSacrifice', true)) {
                var spells = this.spells[ player_id ].getSelectedItems();
                var creatures = this.creatures[ player_id ].getSelectedItems();
                var items = spells.concat(creatures);

                if (items.length > 0) {
                    var card_id = items[0].id;
        
                    this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/selectSacrifice.html", {
                        card_id : card_id,
                        lock : true
                    }, this, function(result) {
                    }, function(is_error) {
                    });
                }
            }
        },
        
        onCommunityCardSelectionChanged: function( elementId ) {
            var player_id = elementId.split('_')[1];

            if (this.checkAction('selectCommunityCard', true)) {
                var community = this.community[ player_id ].getSelectedItems();

                if (community.length > 0) {
                    var card_id = community[0].id;
        
                    this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/selectCommunityCard.html", {
                        card_id : card_id,
                        lock : true
                    }, this, function(result) {
                    }, function(is_error) {
                    });
                }
            }
        },
        
        onPlayerDiceSelectionChanged: function( elementId ) {
            var player_id = elementId.split('_')[1];
            
            if (this.checkAction('selectDice', true)) {
                var dice = this.dice[ player_id ].getSelectedItems();

                if (dice.length > 0) {
                    var dice_list_string = "";
                    for ( var i in dice) {
                        if(dice_list_string == "") {
                            dice_list_string += dice[i].id;
                        } else {
                            dice_list_string += "_" + dice[i].id
                        }
                    }

                    // so why is there no "lock: true" here?
                    // I wanted the logic on whether the correct dice are selected to be done in the php backend
                    // I didn't want to change the state or block the user while the player selects dice
                    // there might be a better way of doing this using ajax however I'm not sure what it is
                    this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/selectDice.html", {
                        dice_list_string : dice_list_string
                    }, this, function(result) {
                    }, function(is_error) {
                    });
                }
            }
        },

        onClickPassButton: function() {
            if (this.checkAction('pass', true)) {
                this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/pass.html", {
                    lock : true
                }, this, function(result) {
                }, function(is_error) {
                });
            }
        },

        onClickDefensePassButton: function() {
            if (this.checkAction('takeDamage', true)) {
                this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/takeDamage.html", {
                    lock : true
                }, this, function(result) {
                }, function(is_error) {
                });
            }
        },

        onClickDefenseToken: function( event ) {
            // Stop this event propagation
            event.preventDefault();
            dojo.stopEvent( event );
            
            if (this.checkAction('selectDefenseToken', true)) {
                this.selectDefenseToken(event.currentTarget);
            }
        },

        onClickActionButton: function( event ) {
            if (this.checkAction('selectAction', true)) {
                var params = event.target.id.split('_');
                var card_id = params[1];
                var action_id = params[2];

                this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/selectAction.html", {
                    card_id : card_id,
                    action_id : action_id,
                    lock : true
                }, this, function(result) {
                }, function(is_error) {
                });
            }
        },
        
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        setupNotifications: function()
        {
            // console.log( 'notifications subscriptions setup' );
            
            dojo.subscribe( 'begin_turn', this, "notif_begin_turn" );

            dojo.subscribe( 'pass', this, "notif_pass" );

            dojo.subscribe( 'delete_dice', this, "notif_delete_dice" );
            this.notifqueue.setSynchronous( 'delete_dice', 500 );

            dojo.subscribe( 'sacrifice', this, "notif_sacrifice" );
            this.notifqueue.setSynchronous( 'sacrifice', 500 );
            
            dojo.subscribe( 'attack', this, "notif_attack" );
            this.notifqueue.setSynchronous( 'attack', 500 );
            
            dojo.subscribe( 'damage', this, "notif_damage" );
            dojo.subscribe( 'heal', this, "notif_heal" );
            
            dojo.subscribe( 'defend', this, "notif_defend" );
            this.notifqueue.setSynchronous( 'defend', 500 );
            
            dojo.subscribe( 'defense_token', this, "notif_defense_token" );
            this.notifqueue.setSynchronous( 'defense_token', 1000 );
            
            dojo.subscribe( 'disabled_action', this, "notif_disabled_action" );
            dojo.subscribe( 'remove_disabled_action', this, "notif_remove_disabled_action" );
            dojo.subscribe( 'remove_disabled_actions', this, "notif_remove_disabled_actions" );

            dojo.subscribe( 'community_card', this, "notif_community_card" );
            this.notifqueue.setSynchronous( 'community_card', 1000 );

            dojo.subscribe( 'curse_timer', this, "notif_curse_timer" );
            
            dojo.subscribe( 'newScores', this, "notif_newScores" );
        },
        
        notif_begin_turn: function( notif )
        {
            var player_id = notif.args.player_id;
            var new_dice = notif.args.new_dice;

            this.dice[player_id].removeAll();
            this.updateDice(new_dice, player_id);
        },
        
        notif_pass: function( notif )
        {
        },
        
        notif_delete_dice: function( notif )
        {
            var player_id = notif.args.player_id;
            var dice_list = notif.args.dice_list;

            for( var i in dice_list ) {
                var dice = dice_list[ i ];
                this.dice[ player_id ].removeFromStockById( dice["dice_id"] );
            }
        },
        
        notif_sacrifice: function( notif )
        {
            var player_id = notif.args.player_id;
            var card_type = notif.args.card_type;
            var card_id = notif.args.card_id;

            // if it's a spell or ritual
            if(card_type == 8 || card_type == 7) {
                this.spells[ player_id ].removeFromStockById( card_id );
            }
            // if it's a curse
            else if(card_type == 9) {
                this.curse[ player_id ].removeFromStockById( card_id );
            }
            // if it's an aura
            else if(card_type == 10) {
                this.aura[player_id].removeFromStockById( card_id);
            }
            // else it's a creature
            else {
                this.creatures[ player_id ].removeFromStockById( card_id );
            }
        },

        notif_attack: function( notif )
        {
        },
        
        notif_damage: function( notif )
        {
        },
        
        notif_heal: function( notif )
        {
        },

        notif_defend: function( notif )
        {
            var card_id = notif.args.card_id;
            var action_id = notif.args.action_id;
            var value = notif.args.value;
            var player_id = notif.args.player_id;
            var card_type = notif.args.card_type;
            
            this.addDefenseTokenToCard( card_id, action_id, value, player_id, card_type );
        },

        notif_defense_token: function( notif )
        {
            var card_id = notif.args.card_id;
            var action_id = notif.args.action_id;
            var player_id = notif.args.player_id;
            var value = notif.args.value;
            
            this.updateDefenseToken( card_id, action_id, player_id, value );
        },

        notif_disabled_action: function( notif )
        {
            var card_id = notif.args.card_id;
            var action_id = notif.args.action_id;
            var player_id = notif.args.player_id;
            var card_type = notif.args.card_type;
            
            this.addDisabledActionTokenToCard( card_id, action_id, player_id, card_type );
        },

        notif_remove_disabled_action: function( notif )
        {
            var card_id = notif.args.card_id;
            var action_id = notif.args.action_id;

            this.removeDisabledActionToken( card_id, action_id );
        },

        notif_remove_disabled_actions: function( notif )
        {
            this.removeDisabledActionTokens();
        },

        notif_community_card: function( notif )
        {
            var card_id = notif.args.card_id;
            var community_id = notif.args.community_id;
            var player_id = notif.args.player_id;
            var slot = notif.args.slot;
            var type = notif.args.type;
            
            // if it's a spell or ritual
            if(type == 8 || type == 7) {
                this.community[community_id].removeFromStockById( card_id, $('spells_'+player_id) );

                this.spells[player_id].addToStockWithId(slot, card_id);
            }
            // if it's an curse
            else if(type == 9) {
                this.community[community_id].removeFromStockById( card_id, $('curse_'+player_id) );
                
                this.curse[player_id].addToStockWithId(slot, card_id);

                this.addCurseTimerToCard( card_id, 3, player_id );
            }
            // if it's an aura
            else if(type == 10) {
                this.community[community_id].removeFromStockById( card_id, $('aura_'+player_id) );
                
                this.aura[player_id].addToStockWithId(slot, card_id);
            }
            // else it's a creature
            else {
                this.community[community_id].removeFromStockById( card_id, $('creatures_'+player_id) );
                
                this.creatures[player_id].addToStockWithId(slot, card_id);
            }
        },

        notif_curse_timer: function( notif )
        {
            var card_id = notif.args.card_id;
            var value = notif.args.value;
            
            this.updateCurseTimer( card_id, value );
        },

        notif_newScores: function( notif )
        {
            for( var player_id in notif.args.scores )
            {
                var newScore = notif.args.scores[ player_id ];
                this.scoreCtrl[ player_id ].toValue( newScore );

                this.updatePlayerBoardHealth( player_id, newScore );
            }
        },
   });             
});
