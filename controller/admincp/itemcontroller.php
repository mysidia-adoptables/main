<?php

namespace Controller\AdminCP;
use Exception;
use Model\DomainModel\Item;
use Model\DomainModel\ItemFunction;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\DuplicateIDException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;

class ItemController extends AppController{
	
	public function __construct(){
        parent::__construct();
		$mysidia = Registry::get("mysidia");
		if($mysidia->usergroup->getpermission("canmanagesettings") != "yes"){
		    throw new NoPermissionException("You do not have permission to manage items.");
		}		
    }
	
	public function index(){
	    parent::index();
	    $mysidia = Registry::get("mysidia");
        $total = $mysidia->db->select("items")->rowCount();
        if($total == 0) throw new InvalidIDException("default_none");
		$pagination = new Pagination($total, $mysidia->settings->pagination, "admincp/item", $mysidia->input->get("page"));
        $stmt = $mysidia->db->select("items", [], "1 LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
		$items = new ArrayList;
        while($dto = $stmt->fetchObject()){ 
            $items->add(new Item($dto->id, $dto));       
        }
        $this->setField("pagination", $pagination);
		$this->setField("items", $items);
	}
	
	public function add(){
	    $mysidia = Registry::get("mysidia");		
	    if($mysidia->input->post("submit")){
		    $this->dataValidate();
			$imageurl = ($mysidia->input->post("existingimageurl") == "none") ? $mysidia->input->post("imageurl") : $mysidia->input->post("existingimageurl");		
		    $mysidia->db->insert("items", ["category" => $mysidia->input->post("category"), "itemname" => $mysidia->input->post("itemname"), "description" => $mysidia->input->post("description"), "imageurl" => $imageurl, "function" => $mysidia->input->post("function"), "target" => $mysidia->input->post("target"), "value" => (int)$mysidia->input->post("value"), 
			                               "shop" => (int)$mysidia->input->post("shop"), "price" => (int)$mysidia->input->post("price"), "chance" => (int)$mysidia->input->post("chance"), "cap" => (int)$mysidia->input->post("cap"), "tradable" => $mysidia->input->post("tradable"), "consumable" => $mysidia->input->post("consumable")]);		
		}
	}
	
	public function edit($id = NULL){
	    $mysidia = Registry::get("mysidia");		
	    if(!$id) return $this->index();
        try{
            $item = new Item($id);
            if($mysidia->input->post("submit")){
 		        $this->dataValidate();
                $imageurl = ($mysidia->input->post("existingimageurl") == "none") ? $mysidia->input->post("imageurl") : $mysidia->input->post("existingimageurl");
                $mysidia->db->update("items", ["category" => $mysidia->input->post("category"), "itemname" => $mysidia->input->post("itemname"), "description" => $mysidia->input->post("description"), "imageurl" => $imageurl, "function" => $mysidia->input->post("function"), "target" => $mysidia->input->post("target"), "value" => (int)$mysidia->input->post("value"), 
			                                   "shop" => (int)$mysidia->input->post("shop"), "price" => (int)$mysidia->input->post("price"), "chance" => (int)$mysidia->input->post("chance"), "cap" => (int)$mysidia->input->post("cap"), "tradable" => $mysidia->input->post("tradable"), "consumable" => $mysidia->input->post("consumable")], "id='{$item->getID()}'");              
            }
            $this->setField("item", $item);
        }
        catch(Exception $e){
            throw new InvalidIDException("nonexist");
        }
	}

	public function delete($id = NULL){
	    $mysidia = Registry::get("mysidia");	
        if(!$id) $this->index();
        else{
            $item = new Item($id);            
            $mysidia->db->delete("items", "id = '{$item->getID()}'");
            $mysidia->db->delete("inventory", "item = '{$item->getID()}'");
        }
        $this->setField("item", $id ? $item : NULL);
	}
	
	public function functions(){
	    $mysidia = Registry::get("mysidia");
		$stmt = $mysidia->db->select("items_functions");
        $itemFunctions = new ArrayList;
        while($dto = $stmt->fetchObject()){
            $itemFunctions->add(new ItemFunction($dto->ifid, $dto));
        }
        $this->setField("itemFunctions", $itemFunctions);
	}
	
	private function dataValidate(){
	    $mysidia = Registry::get("mysidia");
		if(!$mysidia->input->post("category")) throw new BlankFieldException("category");
		if(!$mysidia->input->post("itemname")) throw new BlankFieldException("itemname");	
		if(!$mysidia->input->post("imageurl") and $mysidia->input->post("existingimageurl") == "none") throw new BlankFieldException("images"); 		
		if($this->action == "add"){
		    $itemExist = $mysidia->db->select("items", ["id"], "itemname = :itemname", ["itemname" => $mysidia->input->post("itemname")])->fetchObject();
		    if($itemExist) throw new DuplicateIDException("duplicate");
		}
	}
}