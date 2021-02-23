{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- DiceSummoners implementation : © Eoin Costelloe <eoin@dag.irish>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------
-->

<div id="community_select">
</div>

<!-- BEGIN player -->
<div class="playertable whiteblock">
    <div class="playertablename" style="color:#{PLAYER_COLOR}">{PLAYER_NAME} {DICE}</div>
    <div class="playertabledice" id="dice_{PLAYER_ID}""></div>
    <div class="clear"></div>

    <div class="playertablename" style="color:#{PLAYER_COLOR}">{PLAYER_NAME} {SPELLS}</div>
    <div class="playertablecard" id="spells_{PLAYER_ID}"></div>
    <div class="clear"></div>

    <div class="playertablename" style="color:#{PLAYER_COLOR}">{PLAYER_NAME} {CREATURES}</div>
    <div class="playertablecard" id="creatures_{PLAYER_ID}"></div>
    <div class="clear"></div>

    <div class="playertablename" style="color:#{PLAYER_COLOR}">{PLAYER_NAME} {AURA_AND_CURSE}</div>
    <div class="playertablecard" id="aura_{PLAYER_ID}"></div>
    <div class="playertablecard" id="curse_{PLAYER_ID}"></div>
</div>
<!-- END player -->

<div id="community">
</div>

<div class="community whiteblock" id="community_basic">
    <div class="communityname">{BASIC_CREATURES}</div>
    <div class="communitycard" id="community_0"></div>
    <div class="communitycard" id="community_1"></div>
</div>

<div class="community whiteblock" id="community_advanced">
    <div class="communityname">{ADVANCED_CREATURES}</div>
    <div class="communitycard" id="community_2"></div>
    <div class="communitycard" id="community_3"></div>
    <div class="communitycard" id="community_4"></div>
    <div class="communitycard" id="community_5"></div>
</div>

<div class="community whiteblock" id="community_ritual">
    <div class="communityname">{MYTHIC_CREATURES}</div>
    <div class="communitycard" id="community_6"></div>
    <div class="communitycard" id="community_7"></div>
    <div class="communitycard" id="community_8"></div>
    <div class="clear"></div>

    <div class="communitycard" id="community_9"></div>
    <div class="communitycard" id="community_10"></div>
    <div class="communitycard" id="community_11"></div>
</div>

<div class="community whiteblock" id="community_spell">
    <div class="communityname">{COMBAT_SPELLS}</div>
    <div class="communitycard" id="community_12"></div>
    <div class="communitycard" id="community_13"></div>
    <div class="communitycard" id="community_14"></div>
</div>

<div class="community whiteblock" id="community_aura">
    <div class="communityname">{AURAS}</div>
    <div class="communitycard" id="community_15"></div>
    <div class="communitycard" id="community_16"></div>
</div>

<div class="community whiteblock" id="community_curse">
    <div class="communityname">{CURSES}</div>
    <div class="communitycard" id="community_17"></div>
    <div class="communitycard" id="community_18"></div>
</div>

<div id="defense_tokens"></div>
<div id="disabled_actions"></div>
<div id="curse_timers"></div>

<script type="text/javascript">

// Javascript HTML templates

var jstpl_defense_token='<div class="defense_token defense_token_${player_id}" id="defense_token_${card_id}_${action_id}_${player_id}">${value}</div>';
var jstpl_disabled_action='<div class="disabled_action" id="disabled_action_${card_id}_${action_id}"></div>';
var jstpl_curse_timer='<div class="curse_timer" id="curse_timer_${card_id}">${value}</div>';

var jstpl_player_panel='<div class="player_panel player_${player_id}">\
    <div class="player_health"></div>\
    <span class="player_health_counter" id="player_health_${player_id}">${health}</span >\
    </div>';

</script>  

{OVERALL_GAME_FOOTER}
