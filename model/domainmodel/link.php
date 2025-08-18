<?php

namespace Model\DomainModel;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;
use Resource\Exception\UnsupportedOperationException;
use Resource\Native\Objective;
use Resource\Utility\Comparable;

class Link extends Model implements Comparable{
    
    protected $id;
    protected $linktype;
    protected $linktext;
    protected $linkurl;
    protected $linkparent;
    protected $linkorder;
    
    public function __construct($id, $dto = NULL){
	    $mysidia = Registry::get("mysidia");
        if(!$dto){
	        $dto = $mysidia->db->select("links", [], "id = :id", ["id" => $id])->fetchObject();
            if(!is_object($dto)) throw new InvalidIDException("Link {$id} does not exist...");
        }
        parent::__construct($dto);   
    }
    
    public function getType(){
        return $this->linktype;
    }
    
    public function getText(){
        return $this->linktext;
    }
    
    public function getURL(){
        return $this->linkurl;
    }
   
    public function hasParent(){
        return ($this->linkparent && $this->linkparent != 0);
    }
    
	public function getParent($fetchMode = ""){
        if(!$this->linkparent) return NULL;
	    if($fetchMode == Model::MODEL) return new static($this->linkparent);
	    return $this->linkparent;
	}
    
    public function getParentText(){
        if(!$this->linkparent) return "";
        $mysidia = Registry::get("mysidia");
        return $mysidia->db->select("links", ["linktext"], "id='{$this->linkparent}'")->fetchColumn();
    }
    
    public function getOrder(){
        return $this->linkorder;
    }
    
    public function compareTo(Objective $object){
        if(!($object instanceof Link)) throw new UnsupportedOperationException("Link can only be compared with another link.");
        return $this->linkorder - $object->getOrder();
    }
    
    protected function save($field, $value) {
		$mysidia = Registry::get("mysidia");
		$mysidia->db->update("links", [$field => $value], "id='{$this->id}'");        
    }
}