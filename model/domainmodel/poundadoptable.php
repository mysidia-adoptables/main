<?php

namespace Model\DomainModel;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;
use Resource\Utility\Date;

class PoundAdoptable extends OwnedAdoptable{
    
    protected $firstowner;
    protected $lastowner;
    protected $currentowner;
    protected $recurrence;
    protected $datepound;
    protected $dateadopt;
  
    public function __construct($aid, $dto = NULL){
        $mysidia = Registry::get("mysidia");
        if(!$dto){
            $prefix = constant("PREFIX");
	        $dto = $mysidia->db->join("owned_adoptables", "owned_adoptables.aid = pounds.aid")
                               ->join("adoptables", "adoptables.id = owned_adoptables.adopt")
                               ->select("pounds", [], "{$prefix}pounds.aid = :aid", ["aid" => $aid])->fetchObject();
            if(!is_object($dto)) throw new InvalidIDException("The pound adoptable ID {$aid} does not exist...");
        }
		$this->createFromDTO($dto);
    }
    
    protected function createFromDTO($dto){
        parent::createFromDTO($dto);
        $this->datepound = new Date($dto->datepound);
        $this->dateadopt = new Date($dto->dateadopt);
    }
    
    public function getFirstOwner($fetchMode = ""){
        if($fetchMode == Model::MODEL) return new Member($this->firstowner); 
        else return $this->firstowner;
    }
    
    public function isFirstOwner(User $user = NULL){ 
        if(!$user) return FALSE;
        return ($this->firstowner == $user->getID());
    }
    
    public function getLastOwner($fetchMode = ""){
        if($fetchMode == Model::MODEL) return new Member($this->lastowner); 
        else return $this->lastowner;
    }
    
    public function isLastOwner(User $user = NULL){
        if(!$user) return FALSE;
        return ($this->lastowner == $user->getID());
    } 
    
    public function getCurrentOwner($fetchMode = ""){
        if($fetchMode == Model::MODEL) return new Member($this->currentowner); 
        else return $this->currentowner;
    }
    
    public function isCurrentOwner(User $user = NULL){ 
        if(!$user) return FALSE;
        return ($this->currentowner == $user->getID());
    }
    
    public function getRecurrence(){
        return $this->recurrence;
    }
    
    public function getDatePound($format = NULL){
        return $format ? $this->datepound->format($format) : $this->datepound;
    }
    
    public function getDateAdopt($format = NULL){
        return $format ? $this->dateadopt->format($format) : $this->dateadopt;
    }
    
    protected function updatePound(){
        $mysidia = Registry::get("mysidia");
        $this->recurrence++;
        $this->lastowner = $this->currentowner;
        $this->currentowner = 0;
        $this->datepound = new Date;
        $this->dateadopt = NULL;
		$mysidia->db->update("pounds", ["lastowner" => (int)$this->lastowner, "currentowner" => (int)$this->currentowner, "recurrence" => (int)$this->recurrence, "datepound" => $this->datepound->format('Y-m-d'), "dateadopt" => $this->dateadopt], "aid='{$this->aid}'");        
    }
    
    public function readopt($owner){
        $mysidia = Registry::get("mysidia");
        if($this->owner) throw new InvalidIDException("unlucky");
        $this->owner = $owner;
        $this->dateadopt = new Date;
        $mysidia->db->update("owned_adoptables", ["owner" => $this->owner], "aid = '{$this->aid}'");
        $mysidia->db->update("pounds", ["currentowner" => $this->owner, "dateadopt" => $this->dateadopt->format("Y-m-d")], "aid ='{$this->aid}'");        
    }
    
    protected function save($field, $value){
		$mysidia = Registry::get("mysidia");
		$mysidia->db->update("pounds", [$field => $value], "aid = '{$this->aid}'");
	}
}