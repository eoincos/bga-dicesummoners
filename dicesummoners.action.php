<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DiceSummoners implementation : © Eoin Costelloe <eoin@dag.irish>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 * 
 * dicesummoners.action.php
 *
 * DiceSummoners main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/dicesummoners/dicesummoners/myAction.html", ...)
 *
 */
  
  
  class action_dicesummoners extends APP_GameAction
  { 
    // Constructor: please do not modify
   	public function __default()
  	{
  	    if( self::isArg( 'notifwindow') )
  	    {
            $this->view = "common_notifwindow";
  	        $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
  	    }
  	    else
  	    {
            $this->view = "dicesummoners_dicesummoners";
            self::trace( "Complete reinitialization of board game" );
      }
  	} 

    public function pass() {
      self::setAjaxMode();
      $this->game->pass();
      self::ajaxResponse();
    }

    public function selectCard() {
      self::setAjaxMode();
      $card_id = self::getArg( "card_id", AT_posint, true );
      $this->game->selectCard( $card_id );
      self::ajaxResponse();
    }

    public function selectAction() {
      self::setAjaxMode();
      $card_id = self::getArg( "card_id", AT_posint, true );
      $action_id = self::getArg( "action_id", AT_posint, true );
      $this->game->selectAction( $card_id, $action_id );
      self::ajaxResponse();
    }

    public function selectDice() {
      self::setAjaxMode();
      $dice_list_string = self::getArg( "dice_list_string", AT_alphanum, true );
      $this->game->selectDice( $dice_list_string );
      self::ajaxResponse();
    }

    public function selectSacrifice() {
      self::setAjaxMode();
      $card_id = self::getArg( "card_id", AT_posint, true );
      $this->game->selectSacrifice( $card_id );
      self::ajaxResponse();
    }

    public function selectCommunityCard() {
      self::setAjaxMode();
      $card_id = self::getArg( "card_id", AT_posint, true );
      $this->game->selectCommunityCard( $card_id );
      self::ajaxResponse();
    }

    public function takeDamage() {
      self::setAjaxMode();
      $this->game->takeDamage();
      self::ajaxResponse();
    }

    public function selectDefenseToken() {
      self::setAjaxMode();
      $card_id = self::getArg( "card_id", AT_posint, true );
      $action_id = self::getArg( "action_id", AT_posint, true );
      $this->game->selectDefenseToken( $card_id, $action_id );
      self::ajaxResponse();
    }
  }
  

