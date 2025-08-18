<?php

namespace Model\DomainModel;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Utility\Date;

class AdoptCondition extends Model{

    const IDKEY = "id";
    protected $id;
	protected $adopt;
	protected $freqcond;
    protected $number;
    protected $datecond;
    protected $date;
 	protected $adoptscond;
 	protected $moreless;
 	protected $morelessnum;
	protected $levelgrle;
	protected $grlelevel;
  
    public function __construct(Adoptable $adopt, $dto = NULL){	  
	    $mysidia = Registry::get("mysidia");		
		$this->adopt = $adopt;
        if(!$dto){
	        $fields = ["freqcond", "number", "datecond", "date", "adoptscond", "moreless", "morelessnum", "levelgrle", "grlelevel"];
	        $dto = $mysidia->db->select("adoptables_conditions", $fields, "id = '{$adopt->getID()}'")->fetchObject();
            if(!is_object($dto)) throw new AdoptConditionException("Adoptable condition does not exist...");
        }
        parent::__construct($dto);     
    }
    
    protected function createFromDTO($dto){
        parent::createFromDTO($dto);
        $this->date = new Date($dto->date);
    }

    public function getAdopt(){
        return $this->adopt;
    }
  
    public function hasFreqCondition(){
        return $this->freqcond;
    }
	
	public function getFreqCondition(){
	    return $this->number;
	}
	
	protected function checkFreqCondition(){
	    if($this->freqcond == "enabled"){
		    $mysidia = Registry::get("mysidia");
		    $freq = $mysidia->db->select("owned_adoptables", ["aid"], "adopt = '{$this->adopt->getID()}'")->rowCount();
			if($freq >= $this->number) throw new AdoptConditionException("Freq Condition Not met.");
		}
	}
	
	public function hasDateCondition(){
	    return $this->datecond;
	}
	
	public function getDateCondition($format = NULL){
        if(!$this->date) return NULL;
        return $format ? $this->date->format($format) : $this->date;
	}
	
	protected function checkDateCondition(){
		if($this->datecond == "enabled"){
		    $today = new Date;
			if($this->getDateCondition('Y-m-d') != $today->format('Y-m-d')) throw new AdoptConditionException("Date Condition Not met.");
		}
	}
	
	public function hasAdoptConditions(){
	    return $this->adoptscond;
	}
  
    public function hasNumberCondition(){
	    return $this->moreless;
    }
  
  	public function getNumberCondition(){
	    return $this->morelessnum;
	}
	
	protected function checkNumberCondition(){
		if($this->moreless == "enabled"){
		    $mysidia = Registry::get("mysidia");
		    $num = $mysidia->db->select("owned_adoptables", ["aid"], "owner = '{$mysidia->user->getID()}' AND adopt = '{$this->adopt->getID()}'")->rowCount();
			if($num >= $this->morelessnum) throw new AdoptConditionException("Number Condition Not met.");
		}
	}
	
    public function hasGroupCondition(){
	    return $this->levelgrle;
    }
  
  	public function getGroupCondition(){
	    return $this->grlelevel;
	}

    protected function checkGroupCondition(){
		if($this->levelgrle == "enabled"){
		    $mysidia = Registry::get("mysidia");		    
			if($mysidia->user->getUsergroup() != $this->grlelevel) throw new AdoptConditionException("Group Condition Not met.");
		}
	}
  
    public function checkConditions(){
	    $mysidia = Registry::get("mysidia");
        if(!$mysidia->user->isLoggedIn()) return FALSE;
        $usergroup = $mysidia->user->getUsergroup(Model::MODEL);
        if(!$usergroup->hasPermission("canadopt")) return FALSE;
		
		switch($this->adopt->getWhenAvailable()){
		    case "always":
			    return TRUE;
			case "conditions":
	            try{
			        $this->checkFreqCondition();
			        $this->checkDateCondition();
		            $this->checkNumberCondition();
		            $this->checkGroupCondition();
				    return TRUE;
		        }
                catch(AdoptConditionException $ace){
                    return FALSE;   
                }             
            default:
                return FALSE;			
		}	
    }
	
	protected function save($field, $value){
		$mysidia = Registry::get("mysidia");
		$mysidia->db->update("adoptables_conditions", [$field => $value], "id = '{$this->adopt->getID()}'");
	}
}