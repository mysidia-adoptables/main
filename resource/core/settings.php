<?php

namespace Resource\Core;
use Exception, SplFileInfo;

abstract class Settings extends Core{
    // The settings class, the base class for all settings objects
    protected $cfsetting = FALSE;
    protected $dbsetting = FALSE;
  
    public function __construct($object){
        $mode = $this->getMode($object);
        switch($mode){
	        case "config":
               $this->cfsetting = TRUE;
               $this->fetch();
               break;
            case "database":
               $this->dbsetting = TRUE;
               $this->fetch($object);
               break;
            default:
               throw new Exception("Settings fetch mode not recognized.");		
	    }
    }
    
    public function hasConfig(){
        return $this->cfsetting;
    }
    
    public function hasDatabase(){
        return $this->dbsetting;
    }
  
    private function getMode($object){     
	    if($object instanceof SplFileInfo) return "config";
	    elseif($object instanceof Database) return "database";
	    else return NULL;
    }

    abstract public function fetch($object);
}