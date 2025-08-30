<?php

namespace Service\ApplicationService;
use ArrayObject;
use Model\DomainModel\OwnedAdoptable;
use Model\DomainModel\PoundAdoptable;
use Model\Settings\PoundSettings;
use Resource\Collection\ArrayList;
use Resource\Core\Registry;
use Resource\Native\MysObject;
use Service\Validator\PoundValidator;
use Service\Validator\ReadoptValidator;

class PoundService extends MysObject{ 
    
    private $settings;
    
    public function __construct(PoundSettings $settings){
        $this->settings = $settings;
    }
    
    public function getSettings(){ 
        return $this->settings;
    }
    
    public function getPoundedAdopts(){
        $mysidia = Registry::get("mysidia");
		$stmt = $mysidia->db->join("owned_adoptables", "owned_adoptables.aid = pounds.aid")
                            ->join("adoptables", "adoptables.id = owned_adoptables.adopt")                           
                            ->select("pounds", [], "currentowner = 0");
        if(!is_object($stmt)) throw new InvalidActionException("There are no adoptables ready to adopt at this time.");
        else{
            $poundedAdopts = new ArrayList;
            while($dto = $stmt->fetchObject()){ 
                $poundedAdopts->add(new PoundAdoptable($dto->aid, $dto));
                return $poundedAdopts;
            }
        }
    }
    
    public function isPoundedBefore($aid){
        $mysidia = Registry::get("mysidia");
        $stmt = $mysidia->db->select("pounds", ["aid"], "aid = :aid", ["aid" => $aid]);
        return ($stmt->rowCount() > 0);        
    }
    
    public function isPoundedNow($aid){
        $mysidia = Registry::get("mysidia");
        $stmt = $mysidia->db->select("pounds", ["aid"], "aid = :aid AND currentowner = 'SYSTEM'", ["aid" => $aid]);
        return ($stmt->rowCount() > 0);              
    }
    
    public function pound($aid){
        $mysidia = Registry::get("mysidia");
        $adopt = $this->isPoundedBefore($aid) ? new PoundAdoptable($aid) : new OwnedAdoptable($aid);
        $this->validatePound($adopt);
        $adopt->pound();
        if($this->settings->cost){    
		    $cost = $this->getCost($adopt, "pound");
			$mysidia->user->changeMoney(-$cost);
            return $cost;
		}
        return 0;
    }
    
    public function readopt($aid){ 
        $mysidia = Registry::get("mysidia");
        $adopt = new PoundAdoptable($aid);
        $this->validateReadopt($adopt);
        $adopt->readopt($mysidia->user->getID());
        if($this->settings->cost){
    		$cost = $this->getCost($adopt, "readopt");
			$mysidia->user->changeMoney(-$cost);
            return $cost;        
        }
        return 0;
    }
    
    public function getCost(OwnedAdoptable $adopt, $action){ 
	    $actionCode = ($action == "pound") ? 0 : 1;
	    if($this->settings->cost){
            return ($this->settings->costtype == "percent") 
                    ? $adopt->getCost() * (1 + (0.01 * $this->settings->cost[$actionCode]))
                    : $adopt->getCost() + $this->settings->cost[$actionCode];	  
	    }
	    if($this->settings->levelbonus){
            return ($this->settings->leveltype == "increment")
                    ? $adopt->getCost() + ($this->settings->levelbonus * $adopt->getCurrentLevel())
                    : $adopt->getCost() * $adopt->getCurrentLevel(); 
	    }
	    return 0;        
    }
    
    private function validatePound(OwnedAdoptable $adopt){
        $validations = new ArrayObject(["adopt", "owner", "number", "recurrence", "money"]);
        $validator = new PoundValidator($adopt, $this->getCost($adopt, "pound"), $this->settings, $validations);
        $validator->validate();
    }
    
    private function validateReadopt(PoundAdoptable $adopt){ 
        $validations = new ArrayObject(["owner", "number", "duration", "money"]);
        $validator = new ReadoptValidator($adopt, $this->getCost($adopt, "readopt"), $this->settings, $validations);
        $validator->validate();
    }
}