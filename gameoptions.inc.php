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
 * gameoptions.inc.php
 *
 * DiceSummoners game options description
 * 
 * In this file, you can define your game options (= game variants).
 *   
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in dicesummoners.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

$game_options = array(
    // note: game variant ID should start at 100 (ie: 100, 101, 102, ...). The maximum is 199.
    100 => array(
        'name' => totranslate('Deck'),    
        'values' => array(
            1 => array( 'name' => totranslate('Balanced deck set') ),
            2 => array( 'name' => totranslate('Attack deck set'), 'tmdisplay' => totranslate('Attack deck set'), 'nobeginner' => true ),
            3 => array( 'name' => totranslate('Defend deck set'), 'tmdisplay' => totranslate('Defend deck set'), 'nobeginner' => true ),
            4 => array( 'name' => totranslate('Sacrifice deck set'), 'tmdisplay' => totranslate('Sacrifice deck set'), 'nobeginner' => true ),
        ),
        'default' => 1
    ),
);

$game_preferences = array(
    100 => array(
			'name' => totranslate('Defense token selection strategy'),
			'needReload' => true, // after user changes this preference game interface would auto-reload
			'values' => array(
					1 => array( 'name' => totranslate( 'Manual' ), 'cssPref' => 'token_selection_manual' ),
					2 => array( 'name' => totranslate( 'Automatic - Equal or Lowest' ), 'cssPref' => 'token_selection_equal_lowest' ),
					3 => array( 'name' => totranslate( 'Automatic - Equal only' ), 'cssPref' => 'token_selection_equal_only' ),
					4 => array( 'name' => totranslate( 'Automatic - Lowest only' ), 'cssPref' => 'token_selection_lowest_only' )
			)
	)
);
