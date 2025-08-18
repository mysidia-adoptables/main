<?php

namespace Controller\Main;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;
use Resource\Native\Integer;
use Service\ApplicationService\ShopService;

class ShopController extends AppController{

    private $shopService;
    
    public function __construct(){
        parent::__construct("member");	
        $mysidia = Registry::get("mysidia");
        if($mysidia->systems->shops != "enabled") throw new NoPermissionException("The admin has turned off shop feature for this site, please contact him/her for detailed information.");		            	
		if(!$mysidia->user->hasPermission("canshop")){
		    throw new NoPermissionException("denied");
		}
        $this->shopService = new ShopService;
    }
	
	public function index(){
	    $mysidia = Registry::get("mysidia");
        $type = ($mysidia->input->post("shoptype") == "adoptshop") ? "adoptshop" : "itemshop";
        $numShops = $this->shopService->countTotalShops($type, "visible");
		if($numShops == 0) throw new InvalidIDException("none");
        $this->setField("shopList", $this->shopService->getShops($type, "visible"));
	}
	
	public function browse($sid){
        $shop = $this->shopService->getShop($sid, TRUE);
        $this->setField("shop", $shop);
	}
	
	public function purchase($sid){
        $mysidia = Registry::get("mysidia");
	    if(!$mysidia->input->post("buy")) throw new InvalidIDException("global_id");        
	    if($mysidia->input->post("shoptype") == "itemshop") $cost = $this->shopService->purchaseItem($sid, $mysidia->input->post("itemid"), $mysidia->input->post("quantity"));
		elseif($mysidia->input->post("shoptype") == "adoptshop") $cost = $this->shopService->purchaseAdopt($sid, $mysidia->input->post("adoptid"));
		else throw new InvalidActionException("global_action");
        $this->setField("cost", new Integer($cost));
	}
}