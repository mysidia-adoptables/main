<?php

namespace Model\DomainModel;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\GUI\Component\Image;
use Resource\Native\MysString;

abstract class Shop extends Model{
    
    const IDKEY = "sid";
    protected $sid;
    protected $shoptype;
    protected $shopname;
    protected $category;
    protected $description;
    protected $imageurl;
    protected $status;
    protected $restriction;
    protected $salestax;
    protected $total = 0;
    
    public function __construct($shopinfo, $dto = NULL){
        $mysidia = Registry::get("mysidia");
        if($shopinfo instanceof MysString) $shopinfo = $shopinfo->getValue();
        if(!$dto){
	        $whereclause = is_numeric($shopinfo) ? "sid = :shopinfo" : "shopname = :shopinfo";
	        $dto = $mysidia->db->select("shops", [], $whereclause, ["shopinfo" => $shopinfo])->fetchObject();
	        if(!is_object($dto)) throw new ShopException("The shop specified is invalid...");
        }
        parent::__construct($dto);               
    }
    
    public function getShoptype(){
        return $this->shoptype;
    }
    
    public function getShopname(){
        return $this->shopname;
    }   
        
    public function getCategory(){
        return $this->category;
    }
    
    public function getDescription(){
        return stripslashes($this->description);
    }
    
    public function getImageURL($fetchMode = ""){
 	    if($fetchMode == Model::GUI) return new Image($this->imageurl);
	    return $this->imageurl;       
    }
    
    public function getStatus(){
        return $this->status;
    }
    
    public function getRestriction(){
        return $this->restriction;
    }
    
    public function getSalesTax(){
        return $this->salestax;
    }
    
    public function getTotal(){
        return $this->total;
    }
    
    public function hasCategory(){
        // This method checks if the item category exists in items database or not
	    $mysidia = Registry::get("mysidia");
	    $stmt = $mysidia->db->select("shops", [], "category ='{$this->category}'");
        $cate_exist = ($row = $stmt->fetchObject());     
	    return $cate_exist;
    } 
    
    public function isOpen(){ 
        return ($this->status == "open");
    }
    
    public function isVisible(){ 
        return($this->status != "invisible");
    }
    
    public function rent($name, $period){
        // to be implemented later    
    }
    
    protected function save($field, $value){
		$mysidia = Registry::get("mysidia");
		$mysidia->db->update("shops", [$field => $value], "sid='{$this->sid}'");            
    }
}