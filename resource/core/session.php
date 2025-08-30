<?php

namespace Resource\Core;
use Exception;

/**
 * The Session Class, it is one of Mysidia system core classes.
 * It defines a generic storage object that holds all keys and values available across different sessions.
 * This specific instance is available from Registry, just like any other Mysidia core objects. 
 * @category Resource
 * @package Core
 * @author Hall of Famer 
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.2
 * @todo Improvement over include_path methods to enable better auto-loading.
 */

class Session extends Core{

	/**
	 * The ssid property, which stores the current session id.
	 * @access private
	 * @var String
     */	
    private $ssid;
  
  	/**
	 * The started property, which determines whether the session has started.
	 * @access private
	 * @var Boolean
     */	
    private $started = FALSE;
	
	/**
	 * The initime property, which specifies the time when the session starts.
	 * @access private
	 * @var int
     */	
    private $initime;
    
	/**
	 * The useragent property, which specifies the user agent/browser.
	 * @access private
	 * @var String
     */	
	private $useragent;
    
	/**
	 * The clientip property, which stores the client user's IP address.
	 * @access public
	 * @var String
     */	
	public $clientip;
     
   public function __construct(){
      // Start our session
      session_set_cookie_params([
         'lifetime' => 60*60*24*100,
         'path' => '/',
         'domain' => $_SERVER['HTTP_HOST'],
         'secure' => true,
         'httponly' => true,
         'samesite' => 'Strict'
      ]);
      if (session_status() == PHP_SESSION_NONE)  session_start(['use_only_cookies' => true, 'use_trans_sid' => false]);
      //if (session_status() == PHP_SESSION_NONE) session_start(['cookie_lifetime' => 604800, 'use_cookies' => true, 'cookie_secure' => true,'cookie_httponly' => true,'use_only_cookies' => true, 'use_trans_sid' => false, 'use_strict_mode' => true, 'cookie_samesite' => 'Strict' ]);
	   $this->started = TRUE;
	 
	   // Initiate our session properties
	   $this->ssid = session_id();
	   $this->initime = time();
	   $this->useragent = $_SERVER['HTTP_USER_AGENT'];
	   $this->clientip = $_SERVER['REMOTE_ADDR'];	 
    }
  
    public function getid(){
       if(empty($this->ssid)) $this->error("Session already expired...");	
	   else return $this->ssid; 
    }
  
    private function exist($name){
       if(!empty($_SESSION[$name])) return TRUE;
	   else return FALSE;
    }
  
    public function fetch($name){
       if($this->exist($name)) return $_SESSION[$name];
       else return FALSE;
    }
  
    public function assign($name, $value, $override = TRUE, $encrypt = FALSE){
       $value = ($encrypt == TRUE)?hash('sha512', $value):$value;
	   if(!empty($_SESSION[$name]) and $override == FALSE) $this->error("Cannot override session var {$name}.");	
	   else $_SESSION[$name] = $value;
    }
  
    public function terminate($name){
       if(!isset($_SESSION[$name])) return FALSE;	 
	   else unset($_SESSION[$name]);
    }
  
    public function regen($name){
       $this->ssid = session_regenerate_id();
	   $this->initime = time();
    }
  
    public function validate($name){
       if($this->useragent != $_SERVER['HTTP_USER_AGENT'] || $this->clientip != $_SERVER['REMOTE_ADDR']){ 
	      $this->destroy();
	      $this->error("User IP has changed...");
       }
	   elseif(!isset($_SESSION[$name])){
          $this->error("Session already expired...");
       } 
       else return TRUE;	 
    }
  
    public function destroy(){
       $_SESSION = [];
       session_destroy();
    }

    private function error($message){
       throw new Exception($message);	 
    }  

}