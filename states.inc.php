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
 * states.inc.php
 *
 * DiceSummoners game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!

$machinestates = array(

    // The initial state. Please do not modify.
    1 => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array( "" => 10 )
    ),

    10 => array(
        "name" => "newTurn",
        "description" => '',
        "type" => "game",
        "action" => "stNewTurn",
        "updateGameProgression" => true, 
        "transitions" => array( "playerTurn" => 11, "selectDefenseTokens" => 16, "gameEnd" => 99 )
    ),

    11 => array(
        "name" => "playerTurn",
        "description" => clienttranslate('${actplayer} must select a card or pass'),
        "descriptionmyturn" => clienttranslate('${you} must select a card or pass'),
        "type" => "activeplayer",
        "updateGameProgression" => true, 
        "possibleactions" => array( "selectCard", "pass" ),
        "transitions" => array( "pass" => 10, "selectCard" => 12, "gameEnd" => 99 )
    ),

    12 => array(
        "name" => "selectAction",
        "description" => clienttranslate('${actplayer} must select an action or another card'),
        "descriptionmyturn" => clienttranslate('${you} must select an action or another card'),
        "type" => "activeplayer",
        "args" => "argSelectAction",
        "updateGameProgression" => true, 
        "possibleactions" => array( "selectCard", "pass", "selectAction" ),
        "transitions" => array( "pass" => 10, "selectCard" => 12, "selectDice" => 13, "selectSacrifice" => 18, "gameEnd" => 99 )
    ),

    13 => array(
        "name" => "selectDice",
        "description" => clienttranslate('${actplayer} must select dice or another card (Mercury: ${mercury}) (Salt: ${salt}) (Sulphur: ${sulphur})'),
        "descriptionmyturn" => clienttranslate('${you} must select dice or another card (Mercury: ${mercury}) (Salt: ${salt}) (Sulphur: ${sulphur})'),
        "type" => "activeplayer",
        "args" => "argSelectDice",
        "updateGameProgression" => true, 
        "possibleactions" => array( "selectCard", "selectDice" ),
        "transitions" => array( "pass" => 10, "selectCard" => 12, "playerTurn" => 11, "selectCommunityCards" => 14, "selectDefenseTokensOpponent" => 15, "selectSacrifice" => 18, "gameEnd" => 99 )
    ),

    14 => array(
        "name" => "selectCommunityCards",
        "description" => clienttranslate('${actplayer} must select a community card of type: ${card_type}'),
        "descriptionmyturn" => clienttranslate('${you} must select a community card of type: ${card_type}'),
        "type" => "activeplayer",
        "args" => "argSelectCommunityCards",
        "updateGameProgression" => true, 
        "possibleactions" => array( "selectCommunityCard" ),
        "transitions" => array( "pass" => 10, "playerTurn" => 11, "gameEnd" => 99 )
    ),

    15 => array(
        "name" => "selectDefenseTokensOpponent",
        "description" => '',
        "type" => "game",
        "action" => "stSelectDefenseTokensOpponent",
        "updateGameProgression" => true, 
        "transitions" => array( "selectDefenseTokens" => 16 )
    ),

    16 => array(
        "name" => "selectDefenseTokens",
        "description" => clienttranslate('${actplayer} must select defense tokens or take damage'),
        "descriptionmyturn" => clienttranslate('${you} must select defense tokens or take damage'),
        "type" => "activeplayer",
        "args" => "argSelectDefenseTokens",
        "updateGameProgression" => true, 
        "possibleactions" => array( "selectDefenseToken", "takeDamage" ),
        "transitions" => array( "playerTurn" => 11, "selectDefenseTokens" => 16, "selectDefenseTokensPlayer" => 17, "gameEnd" => 99 )
    ),

    17 => array(
        "name" => "selectDefenseTokensPlayer",
        "description" => '',
        "type" => "game",
        "action" => "stSelectDefenseTokensPlayer",
        "updateGameProgression" => true, 
        "transitions" => array( "pass" => 10, "playerTurn" => 11, "gameEnd" => 99 )
    ),

    18 => array(
        "name" => "selectSacrifice",
        "description" => clienttranslate('${actplayer} must select any ${type_name} to sacrifice'),
        "descriptionmyturn" => clienttranslate('${you} must select any ${type_name} to sacrifice'),
        "type" => "activeplayer",
        "args" => "argSelectSacrifice",
        "updateGameProgression" => true, 
        "possibleactions" => array( "selectSacrifice" ),
        "transitions" => array( "pass" => 10, "playerTurn" => 11, "selectCommunityCards" => 14, "selectDefenseTokensOpponent" => 15, "gameEnd" => 99 )
    ),

    // Final state.
    // Please do not modify (and do not overload action/args methods).
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd",
        "updateGameProgression" => true, 
    )

);



