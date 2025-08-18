<?php

namespace Model\DomainModel;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\GUI\Component\Image;

class Level extends Model{

    const IDKEY = "lvid";
    protected $lvid;
	protected $adopt;
	protected $level;
	protected $requiredclicks;
	protected $primaryimage;
	protected $alternateimage;
	protected $rewarduser;
	protected $promocode;
  
    public function __construct($adopt, $level, $dto = NULL){	  
	    $mysidia = Registry::get("mysidia");
        if(!$dto){
            $values = ["adopt" => $adopt, "level" => $level];
	        $dto = $mysidia->db->select("levels", [], "adopt = :adopt AND level = :level", $values)->fetchObject();
            if(!is_object($dto)) throw new LevelNotfoundException("The level {$level} for {$adopt} does not exist...");
        }
        parent::__construct($dto);  
    }
	
    public function getAdopt($fetchMode = ""){
	    if($fetchMode == Model::MODEL) return new Adoptable($this->adopt);
        else return $this->adopt;
    }

    public function getLevel(){
        return $this->level;
    }

    public function getRequiredClicks(){
        return $this->requiredclicks;
    }
  
    public function getPrimaryImage($fetchMode = ""){
	    if($fetchMode == Model::GUI) return new Image($this->primaryimage);
        return $this->primaryimage;
    }
	
	public function getAlternateImage($fetchMode = ""){
	    if($fetchMode == Model::GUI) return new Image($this->alternateimage);
	    return $this->alternateimage;
	}
	
	public function getNextLevel($fetchMode = ""){
	    if($fetchMode == Model::MODEL) return new static($this->adopt, $this->level + 1);
        else return ($this->level + 1);
	}
    
    public function getRewardUser(){
        return $this->rewarduser;
    }
    
    public function getPromocode(){
        return $this->promocode;
    }
    
    public function hasUploadedImage(){
        return (strpos($this->primaryimage, "picuploads") !== FALSE);
    }
    
    public function updatePrimaryImage($primaryImage){
        $this->primaryimage = $primaryImage;
        $this->save("primaryimage", $this->primaryimage);
    }
    
    public function updateRequiredClicks($requiredClicks){
        $this->requiredclicks = $requiredClicks;
        $this->save("requiredclicks", $this->requiredclicks);
    }
	
	protected function save($field, $value){
		$mysidia = Registry::get("mysidia");
		$mysidia->db->update("levels", [$field => $value], "adopt='{$this->adopt}' AND level='{$this->level}'");
	}
}