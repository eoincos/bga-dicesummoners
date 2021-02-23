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
 * material.inc.php
 *
 * DiceSummoners game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */

 // these ids match the slot number on the creatures
 // these are put separately so it's less content sent to the front end JS
$this->card_descriptions = array(
    0 => clienttranslate("<h2>Ankylosaurus</h2><hr /><h3>Ability 1</h3><p>Attack your opponent and deal them 1 damage</p><h3>Ability 2</h3><p>Add 3 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Ankylosaurus
    1 => clienttranslate("<h2>Arel</h2><hr /><h3>Ability 1</h3><p>Regain 3 health points to your life force up to the maximum of 30</p><h3>Ability 2</h3><p>Add 1 defensive shield to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Arel
    2 => clienttranslate("<h2>Griffin</h2><hr /><h3>Ability 1</h3><p>Attack your opponent and deal them 2 damage</p><h3>Ability 2</h3><p>Add 2 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Griffin
    3 => clienttranslate("<h2>Succubus</h2><hr /><h3>Ability 1</h3><p>Attack your opponent and deal them 3 damage</p><h3>Ability 2</h3><p>Add 1 defensive shield to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Succubus
    4 => clienttranslate("<h2>Zombie</h2><hr /><h3>Ability 1</h3><p>Steal 2 health points from your opponent and gain them to your own life force up to a maximum of 30, this is negated by sacrificing an equivalent amount defense tokens</p><h3>Ability 2</h3><p>Add 1 defensive shield to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Zombie
    5 => clienttranslate("<h2>Wraith</h2><hr /><h3>Ability 1</h3><p>Attack your opponent and deal them 4 damage</p><h3>Ability 2</h3><p>Add 3 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Wraith
    6 => clienttranslate("<h2>Cerberus</h2><hr /><h3>Ability 1</h3><p>Attack your opponent and deal them 6 damage</p><h3>Ability 2</h3><p>Add 3 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Cerberus
    7 => clienttranslate("<h2>Triceratops</h2><hr /><h3>Ability 1</h3><p>Attack your opponent and deal them 5 damage</p><h3>Ability 2</h3><p>Add 4 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Triceratops
    8 => clienttranslate("<h2>Seraph</h2><hr /><h3>Ability 1</h3><p>Regain 4 health points to your life force up to the maximum of 30</p><h3>Ability 2</h3><p>Add 3 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Seraph
    9 => clienttranslate("<h2>Vampire</h2><hr /><h3>Ability 1</h3><p>Steal 3 health points from your opponent and gain them to your own life force up to a maximum of 30, this is negated by sacrificing an equivalent amount defense tokens</p><h3>Ability 2</h3><p>Add 3 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Vampire
    10 => clienttranslate("<h2>Leviathan</h2><hr /><h3>Ability 1</h3><p>Attack your opponent and deal them 3 damage</p><h3>Ability 2</h3><p>Add 4 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Leviathan
    11 => clienttranslate("<h2>Manticore</h2><hr /><h3>Ability 1</h3><p>Attack your opponent and deal them 5 damage</p><h3>Ability 2</h3><p>Add 4 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Manticore
    12 => clienttranslate("<h2>Stegosaurus</h2><hr /><h3>Ability 1</h3><p>Attack your opponent and deal them 4 damage</p><h3>Ability 2</h3><p>Add 5 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Stegosaurus
    13 => clienttranslate("<h2>Hashmal</h2><hr /><h3>Ability 1</h3><p>Regain 3 health points to your life force up to the maximum of 30</p><h3>Ability 2</h3><p>Add 4 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Hashmal
    14 => clienttranslate("<h2>Lich</h2><hr /><h3>Ability 1</h3><p>Steal 2 health points from your opponent and gain them to your own life force up to a maximum of 30, this is negated by sacrificing an equivalent amount defense tokens</p><h3>Ability 2</h3><p>Add 4 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Lich
    15 => clienttranslate("<h2>Werewolf</h2><hr /><h3>Ability 1</h3><p>Steal 2 health points from your opponent and gain them to your own life force up to a maximum of 30, this is negated by sacrificing an equivalent amount defense tokens</p><h3>Ability 2</h3><p>Attack your opponent and deal them 3 damage</p>"),//Werewolf
    16 => clienttranslate("<h2>Velociraptor</h2><hr /><h3>Ability 1</h3><p>Attack your opponent and deal them 6 damage</p><h3>Ability 2</h3><p>Add 2 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Velociraptor
    17 => clienttranslate("<h2>Baphomet</h2><hr /><h3>Ability 1</h3><p>Attack your opponent and deal them 7 damage</p><h3>Ability 2</h3><p>Add 5 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Baphomet
    18 => clienttranslate("<h2>Galvah</h2><hr /><h3>Ability 1</h3><p>Regain 7 health points to your life force up to the maximum of 30</p><h3>Ability 2</h3><p>Add 5 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Galvah
    19 => clienttranslate("<h2>Legba</h2><hr /><h3>Ability 1</h3><p>Steal 5 health points from your opponent and gain them to your own life force up to a maximum of 30, this is negated by sacrificing an equivalent amount defense tokens</p><h3>Ability 2</h3><p>Add 5 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Legba
    20 => clienttranslate("<h2>Chimera</h2><hr /><h3>Ability 1</h3><p>Attack your opponent and deal them 10 damage</p><h3>Ability 2</h3><p>Add 4 defensive shields to this creature, you may not add shields again until all current shields on this creature have been destroyed</p>"),//Chimera
    21 => clienttranslate("<h2>Start Set 1</h2><hr /><h3>Ability 1</h3><p>Summon a basic creature to your army from the community cards</p><h3>Ability 2</h3><p>Learn a new spell for your spellbook from the community cards</p>"),//Start Set 1
    22 => clienttranslate("<h2>Start Set 2</h2><hr /><h3>Ability 1</h3><p>Summon an advanced creature into your army from the community cards by sacrificing a creature</p><h3>Ability 2</h3><p>Cast a curse from the community cards on your opponent, this lasts 3 turns and only 1 can be active at a time</p>"),//Start Set 2
    23 => clienttranslate("<h2>Start Set 3</h2><hr /><h3>Ability 1</h3><p>Gain the summoning ritual for the mythic creature of your choice, it must then be cast to get the creature</p><h3>Ability 2</h3><p>Learn to aura to permanently enhance your army or spellbook, only 1 can be active at a time</p>"),//Start Set 3
    24 => clienttranslate("<h2>Attack Set</h2><hr /><h3>Ability 1</h3><p>Attack your opponent and deal them 4 damage</p><h3>Ability 2</h3><p>Attack your opponent and deal them 3 damage</p>"),//Attack Set
    25 => clienttranslate("<h2>Defend Set</h2><hr /><h3>Ability 1</h3><p>Add 4 defensive shields to this spell slot, you may not add shields again until all current shields on this spell slot have been destroyed</p><h3>Ability 2</h3><p>Add 3 defensive shields to this spell slot, you may not add shields again until all current shields on this spell slot have been destroyed</p>"),//Defend Set
    26 => clienttranslate("<h2>Combat Set</h2><hr /><h3>Ability 1</h3><p>Attack your opponent and deal them 6 damage</p><h3>Ability 2</h3><p>Add 6 defensive shields to this spell slot, you may not add shields again until all current shields on this spell slot have been destroyed</p>"),//Combat Set
    27 => clienttranslate("<h2>Heal Set</h2><hr /><h3>Ability 1</h3><p>Regain 4 health points to your life force up to the maximum of 30</p><h3>Ability 2</h3><p>Regain 3 health points to your life force up to the maximum of 30</p>"),//Heal Set
    28 => clienttranslate("<h2>Steal Set</h2><hr /><h3>Ability 1</h3><p>Steal 3 health points from your opponent and gain them to your own life force up to a maximum of 30, this is negated by sacrificing an equivalent amount defense tokens</p><h3>Ability 2</h3><p>Steal 2 health points from your opponent and gain them to your own life force up to a maximum of 30, this is negated by sacrificing an equivalent amount defense tokens</p>"),//Steal Set
    29 => clienttranslate("<h2>Skill Set</h2><hr /><h3>Ability 1</h3><p>Regain 6 health points to your life force up to the maximum of 30</p><h3>Ability 2</h3><p>Steal 4 health points from your opponent and gain them to your own life force up to a maximum of 30, this is negated by sacrificing an equivalent amount defense tokens</p>"),//Skill Set
    30 => clienttranslate("<h2>Sacrifice Set</h2><hr /><h3>Ability 1</h3><p>Add 6 defensive shields to this spell slot by sacrificing a creature, you may not add shields again until all current shields on this spell slot have been destroyed</p><h3>Ability 2</h3><p>Regain 6 health points to your life force up to the maximum of 30 by sacrificing a creature</p>"),//Sacrifice Set
    31 => clienttranslate("<h2>Hex Curse</h2><hr /><h3>This applies for 3 rounds</h3><p>Your opponent will gain 1 less blue dice than they should at the start of their turn</p>"),//Hex
    32 => clienttranslate("<h2>Achlys' Mist Curse</h2><hr /><h3>This applies for 3 rounds</h3><p>Your opponent will take 3 damage at the start of their turn</p>"),//Achlys' Mist
    33 => clienttranslate("<h2>Swarm Curse</h2><hr /><h3>This applies for 3 rounds</h3><p>Steal 2 health points from your opponent and gain them to your own life force up to a maximum of 30 at the start of their turn, this is negated by them sacrificing an equivalent amount defense tokens</p>"),//Swarm
    34 => clienttranslate("<h2>Arachne's Web Curse</h2><hr /><h3>This applies for 3 rounds</h3><p>Your opponent cannot use combat spell cards on their turn</p>"),//Arachne's Web
    35 => clienttranslate("<h2>Gleipnir Curse</h2><hr /><h3>This applies for 3 rounds</h3><p>Your opponent cannot use the heal ability to regain health points</p>"),//Gleipnir
    36 => clienttranslate("<h2>Armour Aura</h2><hr /><h3>Bonus Advantage</h3><p>Add 1 extra defence token every time you use the defence token ability with an advanced creature</p>"),//Armour
    37 => clienttranslate("<h2>Bloodthirsty Aura</h2><hr /><h3>Bonus Advantage</h3><p>Steal 1 extra health point every time you use the steal health points ability with a creature</p>"),//Bloodthirsty
    38 => clienttranslate("<h2>Divine Aura</h2><hr /><h3>Bonus Advantage</h3><p>Add 1 extra health point every time you use the regain health points ability with a creature</p>"),//Divine
    39 => clienttranslate("<h2>Fury Aura</h2><hr /><h3>Bonus Advantage</h3><p>Deal 1 extra damage every time you use the attack ability with an advanced creature</p>"),//Fury
    40 => clienttranslate("<h2>Insight Aura</h2><hr /><h3>Bonus Advantage</h3><p>Add 1 to the value of a single combat spell per spell set</p>"),//Insight
    41 => clienttranslate("<h2>Baphomet Ritual</h2><hr /><h3>Summoning Ritual</h3><p>Summon Baphomet to your army from the community cards by sacrificing a creature</p>"),//Baphomet Ritual
    42 => clienttranslate("<h2>Galvah Ritual</h2><hr /><h3>Summoning Ritual</h3><p>Summon Galvah to your army from the community cards by sacrificing a creature</p>"),//Galvah Ritual
    43 => clienttranslate("<h2>Legba Ritual</h2><hr /><h3>Summoning Ritual</h3><p>Summon Legba to your army from the community cards by sacrificing a creature</p>"),//Legba Ritual
    44 => clienttranslate("<h2>Chimera Ritual</h2><hr /><h3>Summoning Ritual</h3><p>Summon Chimera to your army from the community cards by sacrificing a creature</p>"),//Chimera Ritual
);

$this->decks = array(
    1 => array ("Griffin", "Succubus", "Leviathan", "Hashmal", "Cerberus", "Triceratops",
    "Baphomet", "Galvah", "Chimera", "Baphomet Ritual", "Galvah Ritual", "Chimera Ritual",
    "Attack Set", "Defend Set", "Combat Set", "Fury", "Insight", "Hex", "Achlys' Mist"),
    2 => array ("Zombie", "Succubus", "Manticore", "Lich", "Werewolf", "Wraith",
    "Baphomet", "Legba", "Chimera", "Baphomet Ritual", "Legba Ritual", "Chimera Ritual",
    "Attack Set", "Steal Set", "Skill Set", "Fury", "Bloodthirsty", "Swarm", "Arachne's Web"),
    3 => array ("Griffin", "Ankylosaurus", "Stegosaurus", "Hashmal", "Cerberus", "Wraith",
    "Baphomet", "Galvah", "Chimera", "Baphomet Ritual", "Galvah Ritual", "Chimera Ritual",
    "Defend Set", "Heal Set", "Combat Set", "Armour", "Insight", "Achlys' Mist", "Arachne's Web"),
    4 => array ("Arel", "Ankylosaurus", "Manticore", "Velociraptor", "Vampire", "Seraph",
    "Galvah", "Legba", "Chimera", "Galvah Ritual", "Legba Ritual", "Chimera Ritual",
    "Steal Set", "Heal Set", "Sacrifice Set", "Bloodthirsty", "Divine", "Swarm", "Gleipnir"),
);

$this->card_types = array(
    0 => "attack",
    1 => "defend",
    2 => "heal",
    3 => "steal",
    4 => "basic",
    5 => "advanced",
    6 => "mythic",
    7 => "ritual",
    8 => "spell",
    9 => "curse",
    10 => "aura",
);

$this->action_cost_types = array(
    0 => "mercury",
    1 => "salt",
    2 => "sulphur",
    3 => "star",
    4 => "creature",
);

$this->dice_colours = array(
    0 => "blue",
    1 => "red",
);

$this->dice_options = array(
    "blue_mercury" => 0,
    "blue_salt" => 1,
    "blue_star" => 2,
    "red_salt" => 3,
    "red_sulphur" => 4,
    "red_star" => 5,
);

$this->blue_dice = array(
    0 => 0,
    1 => 0,
    2 => 0,
    3 => 1,
    4 => 1,
    5 => 3,
);

$this->red_dice = array(
    0 => 1,
    1 => 1,
    2 => 2,
    3 => 2,
    4 => 3,
    5 => 3,
);

$this->setup_cards = array("Ankylosaurus", "Griffin", "Succubus", "Start Set 1", "Start Set 2", "Start Set 3");

//TODO abilties is spelt wrong

$this->deck_cards = array(
    "Ankylosaurus" => array( 
        "name" => "Ankylosaurus",
        "card_type" => 4,
        "quantity" => 4,
        "slot" => 0,
        "gain_blue" => 1,
        "gain_red" => 0,
        "abilties" => array(
            0 => array(
                "type" => 0,
                "value" => 1,
                "dice" => array(
                    0 => 1,
                    1 => 0,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 3,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            )
        )
    ),
    "Arel" => array( 
        "name" => "Arel",
        "card_type" => 4,
        "quantity" => 4,
        "slot" => 1,
        "gain_blue" => 1,
        "gain_red" => 0,
        "abilties" => array(
            0 => array(
                "type" => 2,
                "value" => 3,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 1,
                "dice" => array(
                    0 => 1,
                    1 => 0,
                    2 => 0
                )
            )
        )
    ),
    "Griffin" => array( 
        "name" => "Griffin",
        "card_type" => 4,
        "quantity" => 4,
        "slot" => 2,
        "gain_blue" => 1,
        "gain_red" => 0,
        "abilties" => array(
            0 => array(
                "type" => 0,
                "value" => 2,
                "dice" => array(
                    0 => 1,
                    1 => 0,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 2,
                "dice" => array(
                    0 => 1,
                    1 => 0,
                    2 => 0
                )
            )
        )
    ),
    "Succubus" => array( 
        "name" => "Succubus",
        "card_type" => 4,
        "quantity" => 4,
        "slot" => 3,
        "gain_blue" => 1,
        "gain_red" => 0,
        "abilties" => array(
            0 => array(
                "type" => 0,
                "value" => 3,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 1,
                "dice" => array(
                    0 => 1,
                    1 => 0,
                    2 => 0
                )
            )
        )
    ),
    "Zombie" => array( 
        "name" => "Zombie",
        "card_type" => 4,
        "quantity" => 4,
        "slot" => 4,
        "gain_blue" => 1,
        "gain_red" => 0,
        "abilties" => array(
            0 => array(
                "type" => 3,
                "value" => 2,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 1,
                "dice" => array(
                    0 => 1,
                    1 => 0,
                    2 => 0
                )
            )
        )
    ),
    "Wraith" => array( 
        "name" => "Wraith",
        "card_type" => 5,
        "quantity" => 3,
        "slot" => 5,
        "gain_blue" => 2,
        "gain_red" => 0,
        "abilties" => array(
            0 => array(
                "type" => 0,
                "value" => 4,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 1
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 3,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            )
        )
    ),
    "Cerberus" => array( 
        "name" => "Cerberus",
        "card_type" => 5,
        "quantity" => 3,
        "slot" => 6,
        "gain_blue" => 2,
        "gain_red" => 0,
        "abilties" => array(
            0 => array(
                "type" => 0,
                "value" => 6,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 1
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 3,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            )
        )
    ),
    "Triceratops" => array( 
        "name" => "Triceratops",
        "card_type" => 5,
        "quantity" => 3,
        "slot" => 7,
        "gain_blue" => 2,
        "gain_red" => 0,
        "abilties" => array(
            0 => array(
                "type" => 0,
                "value" => 5,
                "dice" => array(
                    0 => 0,
                    1 => 2,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 4,
                "dice" => array(
                    0 => 2,
                    1 => 0,
                    2 => 0
                )
            )
        )
    ),
    "Seraph" => array( 
        "name" => "Seraph",
        "card_type" => 5,
        "quantity" => 3,
        "slot" => 8,
        "gain_blue" => 2,
        "gain_red" => 0,
        "abilties" => array(
            0 => array(
                "type" => 2,
                "value" => 4,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 1
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 3,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            )
        )
    ),
    "Vampire" => array( 
        "name" => "Vampire",
        "card_type" => 5,
        "quantity" => 3,
        "slot" => 9,
        "gain_blue" => 2,
        "gain_red" => 0,
        "abilties" => array(
            0 => array(
                "type" => 3,
                "value" => 3,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 1
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 3,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            )
        )
    ),
    "Leviathan" => array( 
        "name" => "Leviathan",
        "card_type" => 5,
        "quantity" => 3,
        "slot" => 10,
        "gain_blue" => 1,
        "gain_red" => 1,
        "abilties" => array(
            0 => array(
                "type" => 0,
                "value" => 3,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 4,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 1
                )
            )
        )
    ),
    "Manticore" => array( 
        "name" => "Manticore",
        "card_type" => 5,
        "quantity" => 3,
        "slot" => 11,
        "gain_blue" => 1,
        "gain_red" => 1,
        "abilties" => array(
            0 => array(
                "type" => 0,
                "value" => 5,
                "dice" => array(
                    0 => 0,
                    1 => 2,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 4,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 1
                )
            )
        )
    ),
    "Stegosaurus" => array( 
        "name" => "Stegosaurus",
        "card_type" => 5,
        "quantity" => 3,
        "slot" => 12,
        "gain_blue" => 1,
        "gain_red" => 1,
        "abilties" => array(
            0 => array(
                "type" => 0,
                "value" => 4,
                "dice" => array(
                    0 => 2,
                    1 => 0,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 5,
                "dice" => array(
                    0 => 0,
                    1 => 2,
                    2 => 0
                )
            )
        )
    ),
    "Hashmal" => array( 
        "name" => "Hashmal",
        "card_type" => 5,
        "quantity" => 3,
        "slot" => 13,
        "gain_blue" => 1,
        "gain_red" => 1,
        "abilties" => array(
            0 => array(
                "type" => 2,
                "value" => 3,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 4,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 1
                )
            )
        )
    ),
    "Lich" => array( 
        "name" => "Lich",
        "card_type" => 5,
        "quantity" => 3,
        "slot" => 14,
        "gain_blue" => 1,
        "gain_red" => 1,
        "abilties" => array(
            0 => array(
                "type" => 3,
                "value" => 2,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 4,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 1
                )
            )
        )
    ),
    "Werewolf" => array( 
        "name" => "Werewolf",
        "card_type" => 5,
        "quantity" => 3,
        "slot" => 15,
        "gain_blue" => 2,
        "gain_red" => 0,
        "abilties" => array(
            0 => array(
                "type" => 3,
                "value" => 2,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 0,
                "value" => 3,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            )
        )
    ),
    "Velociraptor" => array( 
        "name" => "Velociraptor",
        "card_type" => 5,
        "quantity" => 3,
        "slot" => 16,
        "gain_blue" => 1,
        "gain_red" => 1,
        "abilties" => array(
            0 => array(
                "type" => 0,
                "value" => 6,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 1
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 2,
                "dice" => array(
                    0 => 1,
                    1 => 0,
                    2 => 0
                )
            )
        )
    ),
    "Baphomet" => array( 
        "name" => "Baphomet",
        "card_type" => 6,
        "quantity" => 1,
        "slot" => 17,
        "gain_blue" => 0,
        "gain_red" => 2,
        "abilties" => array(
            0 => array(
                "type" => 0,
                "value" => 7,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 2
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 5,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 1
                )
            )
        )
    ),
    "Galvah" => array( 
        "name" => "Galvah",
        "card_type" => 6,
        "quantity" => 1,
        "slot" => 18,
        "gain_blue" => 0,
        "gain_red" => 2,
        "abilties" => array(
            0 => array(
                "type" => 2,
                "value" => 7,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 2
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 5,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 1
                )
            )
        )
    ),
    "Legba" => array( 
        "name" => "Legba",
        "card_type" => 6,
        "quantity" => 1,
        "slot" => 19,
        "gain_blue" => 0,
        "gain_red" => 2,
        "abilties" => array(
            0 => array(
                "type" => 3,
                "value" => 5,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 2
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 5,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 1
                )
            )
        )
    ),
    "Chimera" => array( 
        "name" => "Chimera",
        "card_type" => 6,
        "quantity" => 1,
        "slot" => 20,
        "gain_blue" => 2,
        "gain_red" => 1,
        "abilties" => array(
            0 => array(
                "type" => 0,
                "value" => 10,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 3
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 4,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 1
                )
            )
        )
    ),
    "Start Set 1" => array( 
        "name" => "Start Set 1",
        "card_type" => 8,
        "quantity" => 2,
        "slot" => 21,
        "abilties" => array(
            0 => array(
                "type" => 4,
                "dice" => array(
                    0 => 2,
                    1 => 0,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 8,
                "dice" => array(
                    0 => 2,
                    1 => 0,
                    2 => 0
                )
            )
        )
    ),
    "Start Set 2" => array( 
        "name" => "Start Set 2",
        "card_type" => 8,
        "quantity" => 2,
        "slot" => 22,
        "abilties" => array(
            0 => array(
                "type" => 5,
                "sacrifice_creature" => true,
                "dice" => array(
                    0 => 0,
                    1 => 2,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 9,
                "dice" => array(
                    0 => 0,
                    1 => 2,
                    2 => 0
                )
            )
        )
    ),
    "Start Set 3" => array( 
        "name" => "Start Set 3",
        "card_type" => 8,
        "quantity" => 2,
        "slot" => 23,
        "abilties" => array(
            0 => array(
                "type" => 7,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 3
                )
            ),
            1 => array(
                "type" => 10,
                "dice" => array(
                    0 => 1,
                    1 => 1,
                    2 => 1
                )
            )
        )
    ),
    "Attack Set" => array( 
        "name" => "Attack Set",
        "card_type" => 8,
        "quantity" => 3,
        "slot" => 24,
        "abilties" => array(
            0 => array(
                "type" => 0,
                "value" => 4,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 0,
                "value" => 3,
                "dice" => array(
                    0 => 1,
                    1 => 0,
                    2 => 0
                )
            )
        )
    ),
    "Defend Set" => array( 
        "name" => "Defend Set",
        "card_type" => 8,
        "quantity" => 3,
        "slot" => 25,
        "abilties" => array(
            0 => array(
                "type" => 1,
                "value" => 4,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 3,
                "dice" => array(
                    0 => 1,
                    1 => 0,
                    2 => 0
                )
            )
        )
    ),
    "Combat Set" => array( 
        "name" => "Combat Set",
        "card_type" => 8,
        "quantity" => 3,
        "slot" => 26,
        "abilties" => array(
            0 => array(
                "type" => 0,
                "value" => 6,
                "dice" => array(
                    0 => 1,
                    1 => 1,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 1,
                "value" => 6,
                "dice" => array(
                    0 => 1,
                    1 => 1,
                    2 => 0
                )
            )
        )
    ),
    "Heal Set" => array( 
        "name" => "Heal Set",
        "card_type" => 8,
        "quantity" => 3,
        "slot" => 27,
        "abilties" => array(
            0 => array(
                "type" => 2,
                "value" => 4,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 2,
                "value" => 3,
                "dice" => array(
                    0 => 1,
                    1 => 0,
                    2 => 0
                )
            )
        )
    ),
    "Steal Set" => array( 
        "name" => "Steal Set",
        "card_type" => 8,
        "quantity" => 3,
        "slot" => 28,
        "abilties" => array(
            0 => array(
                "type" => 3,
                "value" => 3,
                "dice" => array(
                    0 => 0,
                    1 => 1,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 3,
                "value" => 2,
                "dice" => array(
                    0 => 1,
                    1 => 0,
                    2 => 0
                )
            )
        )
    ),
    "Skill Set" => array( 
        "name" => "Skill Set",
        "card_type" => 8,
        "quantity" => 3,
        "slot" => 29,
        "abilties" => array(
            0 => array(
                "type" => 2,
                "value" => 6,
                "dice" => array(
                    0 => 1,
                    1 => 1,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 3,
                "value" => 4,
                "dice" => array(
                    0 => 1,
                    1 => 1,
                    2 => 0
                )
            )
        )
    ),
    "Sacrifice Set" => array( 
        "name" => "Sacrifice Set",
        "card_type" => 8,
        "quantity" => 3,
        "slot" => 30,
        "abilties" => array(
            0 => array(
                "type" => 1,
                "value" => 6,
                "sacrifice_creature" => true,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 0
                )
            ),
            1 => array(
                "type" => 2,
                "value" => 6,
                "sacrifice_creature" => true,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 0
                )
            )
        )
    ),
    "Hex" => array( 
        "name" => "Hex",
        "card_type" => 9,
        "quantity" => 4,
        "slot" => 31,
    ),
    "Achlys' Mist" => array( 
        "name" => "Achlys' Mist",
        "card_type" => 9,
        "quantity" => 4,
        "slot" => 32,
        "value_type" => 0,
        "value" => 3,
    ),
    "Swarm" => array( 
        "name" => "Swarm",
        "card_type" => 9,
        "quantity" => 4,
        "slot" => 33,
        "value_type" => 3,
        "value" => 2,
    ),
    "Arachne's Web" => array( 
        "name" => "Arachne's Web",
        "card_type" => 9,
        "quantity" => 4,
        "slot" => 34,
    ),
    "Gleipnir" => array( 
        "name" => "Gleipnir",
        "card_type" => 9,
        "quantity" => 4,
        "slot" => 35,
    ),
    "Armour" => array( 
        "name" => "Armour",
        "card_type" => 10,
        "quantity" => 2,
        "slot" => 36,
    ),
    "Bloodthirsty" => array( 
        "name" => "Bloodthirsty",
        "card_type" => 10,
        "quantity" => 2,
        "slot" => 37,
    ),
    "Divine" => array( 
        "name" => "Divine",
        "card_type" => 10,
        "quantity" => 2,
        "slot" => 38,
    ),
    "Fury" => array( 
        "name" => "Fury",
        "card_type" => 10,
        "quantity" => 2,
        "slot" => 39,
    ),
    "Insight" => array( 
        "name" => "Insight",
        "card_type" => 10,
        "quantity" => 2,
        "slot" => 40,
    ),
    "Baphomet Ritual" => array( 
        "name" => "Baphomet Ritual",
        "card_type" => 7,
        "quantity" => 1,
        "slot" => 41,
        "abilties" => array(
            0 => array(
                "type" => 6,
                "value" => "Baphomet",
                "sacrifice_creature" => true,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 2
                )
            )
        )
    ),
    "Galvah Ritual" => array( 
        "name" => "Galvah Ritual",
        "card_type" => 7,
        "quantity" => 1,
        "slot" => 42,
        "abilties" => array(
            0 => array(
                "type" => 6,
                "value" => "Galvah",
                "sacrifice_creature" => true,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 2
                )
            )
        )
    ),
    "Legba Ritual" => array( 
        "name" => "Legba Ritual",
        "card_type" => 7,
        "quantity" => 1,
        "slot" => 43,
        "abilties" => array(
            0 => array(
                "type" => 6,
                "value" => "Legba",
                "sacrifice_creature" => true,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 2
                )
            )
        )
    ),
    "Chimera Ritual" => array( 
        "name" => "Chimera Ritual",
        "card_type" => 7,
        "quantity" => 1,
        "slot" => 44,
        "abilties" => array(
            0 => array(
                "type" => 6,
                "value" => "Chimera",
                "sacrifice_creature" => true,
                "dice" => array(
                    0 => 0,
                    1 => 0,
                    2 => 2
                )
            )
        )
    ),
  );


