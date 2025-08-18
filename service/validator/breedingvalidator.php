<?php

namespace Service\Validator;
use ArrayObject;
use Model\DomainModel\BreedingException;
use Model\DomainModel\OwnedAdoptable;
use Model\DomainModel\OwnedItem;
use Model\Settings\BreedingSettings;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\Validator;
use Resource\Utility\Date;

class BreedingValidator extends Validator{ 
    
    private $female;
    private $male;
    private $settings; 
    
    public function __construct(OwnedAdoptable $female, OwnedAdoptable $male, BreedingSettings $settings, ArrayObject $validations){
        parent::__construct($validations);
        $this->female = $female;
        $this->male = $male;		
	    $this->settings = $settings;
	}    

    public function setStatus($status = ""){
        parent::setStatus($status);
        if($this->status == "chance" || $this->status == "complete"){
	        $mysidia = Registry::get("mysidia");
		    $date = new Date;
            $mysidia->user->changeMoney(-$this->settings->cost);
            $this->female->setOffsprings($this->female->getOffsprings() + 1, Model::UPDATE);
		    $this->female->setLastBred($date->getTimestamp(), Model::UPDATE);
		    $this->male->setOffsprings($this->male->getOffsprings() + 1 , Model::UPDATE);
		    $this->male->setLastBred($date->getTimestamp(), Model::UPDATE); 
        }	
    }
    
    protected function checkClass(){
	    $femaleClass = explode(",", $this->female->getClass());
		$maleClass = explode(",", $this->male->getClass());
        foreach($femaleClass as $fclass){
            foreach($maleClass as $mclass){
                if($fclass == $mclass) return TRUE;
            }
        }  
        throw new BreedingException("class");
    }
	
	protected function checkGender(){
	    $mysidia = Registry::get("mysidia");
		if($this->female->getGender() != "f" or $this->male->getGender() != "m"){
            $mysidia->user->ban();
			throw new BreedingException("gender");
		}
		return TRUE;	    
	}
  
  	protected function checkOwner(){
	    $mysidia = Registry::get("mysidia");
		if($this->female->getOwner() != $mysidia->user->getID() || $this->male->getOwner() != $mysidia->user->getID()){
		    $mysidia->user->ban();
			throw new BreedingException("owner");
		}
		return TRUE;
	}
  
    protected function checkSpecies(){
	    if(empty($this->settings->species)) return TRUE;
		foreach($this->settings->species as $type){
		    if($this->female->getType() == $type or $this->male->getType() == $type) throw new BreedingException("species");
		}
		return TRUE;
    }
	
	protected function checkInterval(){
        if(!$this->female->getLastBred() && !$this->male->getLastBred()) return TRUE;
	    $current = new Date;
        $interval = $this->settings->interval ? $this->settings->interval : 1;
        $expirationDate = $current->modify("-{$interval} day"); 
		
		if($this->female->getLastBred() > $expirationDate || $this->male->getLastBred() > $expirationDate){
		    throw new BreedingException("interval");      
		}
		return TRUE;
	}
	
	protected function checkLevel(){
	    if($this->female->getCurrentLevel() < $this->settings->level || $this->male->getCurrentLevel() < $this->settings->level){
		    throw new BreedingException("level");
		}
		return TRUE;
	}
  
    protected function checkCapacity(){
        if($this->female->getOffsprings() >= $this->settings->capacity || $this->male->getOffsprings() >= $this->settings->capacity){
		    throw new BreedingException("capacity");
		}
		return TRUE;
    }
	 
    protected function checkNumber(){
        if($this->settings->number == 0) throw new BreedingException("number");
        return TRUE;		
    }

	protected function checkChance(){
		$rand = rand(0, 99);
		if($rand < $this->settings->chance) return TRUE;
		throw new BreedingException("chance");
	}

	protected function checkCost(){
	    $mysidia = Registry::get("mysidia");
	    if($mysidia->user->getMoney() < $this->settings->cost) throw new BreedingException("cost");
		return TRUE;
	}
	
    protected function checkUsergroup(){
		if($this->settings->usergroup == "all") return TRUE;
        $mysidia = Registry::get("mysidia");
		
		foreach($this->settings->usergroup as $usergroup){
		    if($mysidia->user->getUsergroup() == $usergroup) return TRUE;   
		}
		throw new BreedingException("usergroup");
    }
    
    protected function checkItem(){
	    if(!$this->settings->item) return TRUE;
        $mysidia = Registry::get("mysidia");
		
		foreach($this->settings->item as $item){
		    $item = new OwnedItem($item, $mysidia->user->getID());
            if(!$item->inInventory()) throw new BreedingException("item");
            if($item->isConsumable()) $item->remove();			
		}
		return TRUE;
    }
}
