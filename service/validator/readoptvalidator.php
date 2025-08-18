<?php

namespace Service\Validator;
use ArrayObject, DateInterval;
use Model\DomainModel\PoundAdoptable;
use Model\DomainModel\PoundException;
use Model\Settings\PoundSettings;
use Resource\Core\Registry;
use Resource\Core\Validator;
use Resource\Utility\Date;

class ReadoptValidator extends Validator{ 
    
    private $adopt;
    private $cost;
    private $settings;
    
    public function __construct(PoundAdoptable $adopt, $cost, PoundSettings $settings, ArrayObject $validations){
        parent::__construct($validations);
	    $this->adopt = $adopt;	
        $this->cost = $cost;
	    $this->settings = $settings;
	}   
    
    protected function checkOwner(){ 
        $mysidia = Registry::get("mysidia");
        if($this->adopt->getOwner() || $this->adopt->getCurrentOwner()) throw new PoundException("unlucky");
        if($this->settings->owner == "yes" && $this->adopt->isLastOwner($mysidia->user)){ 
            throw new PoundException("readopt2_disabled");
        }
    }
    
    protected function checkNumber(){ 
        $mysidia = Registry::get("mysidia");
        $today = new Date;
        $whereClause = "currentowner = '{$mysidia->user->getID()}'";
        if($this->settings->date == "yes") $whereClause .= " AND dateadopt = '{$today->format('Y-M-D')}'";
        $total = $mysidia->db->select("pounds", ["aid"], $whereClause)->rowCount();
        if($total >= $this->settings->number[1]){ 
            throw new PoundException(($this->settings->date == "yes") ? "readopt_time1" : "readopt_time2");
        }        
    }
    
    protected function checkDuration(){ 
        if($this->settings->duration){ 
            $today = new Date;
            $validDate = $this->adopt->getDatePound()->add(new DateInterval("P{$this->settings->duration}D"));
            if($today < $validDate){ 
                throw new PoundException("duration");
            }
        }        
    }
    
    protected function checkMoney(){ 
        $mysidia = Registry::get("mysidia");
        if($this->settings->cost && $this->cost > $mysidia->user->getMoney()){ 
            throw new PoundException("money");
        }        
    }
}