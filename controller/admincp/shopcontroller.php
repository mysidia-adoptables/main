<?php

namespace Controller\AdminCP;
use Exception;
use Model\DomainModel\Adoptshop;
use Model\DomainModel\Itemshop;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;

class ShopController extends AppController{

	public function __construct(){
        parent::__construct();
		$mysidia = Registry::get("mysidia");
		if($mysidia->usergroup->getpermission("canmanagesettings") != "yes"){
		    throw new NoPermissionException("You do not have permission to manage shops.");
		}		
    }
	
	public function index(){
	    parent::index();
	    $mysidia = Registry::get("mysidia");
        $total = $mysidia->db->select("shops")->rowCount();
        if($total == 0) throw new InvalidIDException("default_none");
		$pagination = new Pagination($total, $mysidia->settings->pagination, "admincp/shop", $mysidia->input->get("page"));
        $stmt = $mysidia->db->select("shops", [], "1 LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
		$shops = new ArrayList;
        while($dto = $stmt->fetchObject()){
            $shops->add($this->fetchShop($dto));
        }
        $this->setField("pagination", $pagination);
		$this->setField("shops", $shops);
	}
	
	public function add(){
	    $mysidia = Registry::get("mysidia");	
	    if($mysidia->input->post("submit")){
		    $this->dataValidate();
		    $imageurl = (!$mysidia->input->post("imageurl")) ? $mysidia->input->post("existingimageurl") : $mysidia->input->post("imageurl");
		    $mysidia->db->insert("shops", ["category" => $mysidia->input->post("category"), "shopname" => $mysidia->input->post("shopname"), "shoptype" => $mysidia->input->post("shoptype"), "imageurl" => $imageurl, 
			                               "description" => $mysidia->input->post("description"), "status" => $mysidia->input->post("status"), "restriction" => $mysidia->input->post("restriction"), "salestax" => (int)$mysidia->input->post("salestax")]);	
		}	
	}
	
	public function edit($sid = NULL){
	    $mysidia = Registry::get("mysidia");
	    if(!$sid) return $this->index();
        try{
            $dto = $mysidia->db->select("shops", [], "sid = :sid", ["sid" => $sid])->fetchObject();
            $shop = $this->fetchShop($dto);
            if($mysidia->input->post("submit")){
                $this->dataValidate();
                $imageurl = (!$mysidia->input->post("imageurl")) ? $mysidia->input->post("existingimageurl") : $mysidia->input->post("imageurl");
			    $mysidia->db->update("shops", ["category" => $mysidia->input->post("category"), "shopname" => $mysidia->input->post("shopname"), "description" => $mysidia->input->post("description"), "imageurl" => $imageurl,
			                                   "status" => $mysidia->input->post("status"), "restriction" => $mysidia->input->post("restriction"), "salestax" => (int)$mysidia->input->post("salestax")], "sid='{$shop->getID()}'");
            }
            $this->setField("shop", $shop);
        }
        catch(Exception $e){
            throw new InvalidIDException("nonexist");
        }
	}

    public function delete($sid = NULL){
	   	$mysidia = Registry::get("mysidia");
        if(!$sid) $this->index();
		else{
            $dto = $mysidia->db->select("shops", [], "sid = :sid", ["sid" => $sid])->fetchObject();
            $shop = $this->fetchShop($dto);
            $mysidia->db->delete("shops", "sid = '{$shop->getID()}'");
        }
        $this->setField("shop", $sid ? $shop : NULL);
    }

    private function fetchShop($dto = NULL){
        if(!$dto) throw new InvalidIDException("nonexist");
        return ($dto->shoptype == "adoptshop") ? new Adoptshop($dto->sid, $dto) : new Itemshop($dto->sid, $dto);
    }
    
 	private function dataValidate(){
	    $mysidia = Registry::get("mysidia");
		if(!$mysidia->input->post("category")) throw new BlankFieldException("category");
		if(!$mysidia->input->post("shopname")) throw new BlankFieldException("shopname");
		if(!$mysidia->input->post("imageurl") && $mysidia->input->post("existingimageurl") == "none") throw new BlankFieldException("images"); 
        if(!$mysidia->input->post("status")) throw new BlankFieldException("status");
		if(!is_numeric($mysidia->input->post("salestax")) || $mysidia->input->post("salestax") < 0) throw new InvalidActionException("salestax");
		if($this->action == "add"){
       		$shopExist = $mysidia->db->select("shops", ["sid"], "shopname = :shopname", ["shopname" => $mysidia->input->post("shopname")])->fetchObject();     
            if($shopExist) throw new DuplicateIDException("duplicate");            
        }
	}
}