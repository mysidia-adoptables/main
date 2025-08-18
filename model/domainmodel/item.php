<?php

namespace Model\DomainModel;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\GUI\Component\Image;
use Resource\Native\MysString;

class Item extends Model{

    protected $id;
    protected $category;
    protected $itemname;
    protected $description;
    protected $imageurl;
    protected $function;
    protected $target;
    protected $value;
    protected $shop;
    protected $price;
    protected $chance;
    protected $cap;
    protected $tradable;
    protected $consumable;

    public function __construct($iteminfo, $dto = NULL){
	    // Fetch the database info into object property
	    $mysidia = Registry::get("mysidia");
        if($iteminfo instanceof MysString) $iteminfo = $iteminfo->getValue();
        if(!$dto){
	        $whereclause = (is_numeric($iteminfo)) ? "id = :iteminfo" : "itemname = :iteminfo";
	        $dto = $mysidia->db->select("items", [], $whereclause, ["iteminfo" => $iteminfo])->fetchObject();
	        if(!is_object($dto)) throw new ItemException("The item specified is invalid...");
        }
        parent::__construct($dto);
    }
    
    public function getCategory(){
        return $this->category;
    }
    
    public function getItemname(){
        return $this->itemname;
    }
    
    public function getDescription(){
        return stripslashes($this->description);
    }
    
    public function getImageURL($fetchMode = ""){
 	    if($fetchMode == Model::GUI) return new Image($this->imageurl);
	    return $this->imageurl;       
    }
    
    public function getFunction($fetchMode = ""){
        if($fetchMode == Model::MODEL) return new ItemFunction($this->function);
        return $this->function;
    }
    
    public function getTarget(){
        return $this->target;
    }
    
    public function getValue(){
        return $this->value;
    }
    
    public function getShop($fetchMode = ""){
	    if($fetchMode == Model::MODEL) return new ItemShop($this->shop);
	    else return $this->shop;        
    }
    
    public function getPrice(){
        return $this->price;
    }
    
    public function getChance(){
        return $this->chance;
    }
    
    public function getCap(){
        return $this->cap;
    }
    
    public function isTradable(){
        return ($this->tradable == "yes");
    }
    
    public function isConsumable(){
        return ($this->consumable == "yes");
    }

    public function hasCategory(){
        // This method checks if the item category exists in items database or not
	    $mysidia = Registry::get("mysidia");
	    $stmt = $mysidia->db->select("items", [], "category ='{$this->category}'");
        $cate_exist = ($row = $stmt->fetchObject());     
	    return $cate_exist;
    }
 
    public function hasItems(){
        // This method checks if the item exists in items database or not
	    $mysidia = Registry::get("mysidia");
	    $stmt = $mysidia->db->select("items", [], "itemname ='{$this->itemname}'");
        $item_exist = ($row = $stmt->fetchObject());     
	    return $item_exist;
    }

      
    public function checkTarget($aid){
        // This method checks if the item is usable
	    $adopt = new OwnedAdoptable($aid);
        $id = $adopt->getSpeciesID();
	    $usable = FALSE;
	    switch($this->target){
            case "all":
		        $usable = TRUE;
		        break;
            case "user":
		        $usable = TRUE;
		        break;
		    default:
		        $target = explode(",", $this->target);
                if(in_array($id, $target)) $usable = TRUE;			
	    }
	    return $usable;
    }
    
    public function randomChance(){
	    switch($this->chance){
	        case 100:
                $usable = TRUE;
		        break;
            default:
		        $temp = mt_rand(0,99);
			    $usable = ($temp < $this->chance);
	    }
        return $usable;        
    }
    
    protected function save($field, $value){
		$mysidia = Registry::get("mysidia");
		$mysidia->db->update("inventory", [$field => $value], "id='{$this->id}'");            
    }
}