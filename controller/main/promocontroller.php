<?php

namespace Controller\Main;
use ArrayObject;
use Model\DomainModel\Promocode;
use Model\DomainModel\PromocodeException;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Exception\NoPermissionException;
use Resource\Native\MysString;
use Service\Validator\PromocodeValidator;

class PromoController extends AppController{

    public function __construct(){
        parent::__construct("member");
        $mysidia = Registry::get("mysidia");	
        if($mysidia->systems->promo != "enabled") throw new NoPermissionException("The admin has turned off promocode feature for this site, please contact him/her for detailed information.");	
    }
	
	public function index(){
	    $mysidia = Registry::get("mysidia");	            	
	    if($mysidia->input->post("promocode")){
		    $mysidia->session->validate("promo");
            try{
                $promocode = new Promocode($mysidia->input->post("promocode"));
                if($promocode->isNew()) throw new PromocodeException("fail");
                $validator = new PromocodeValidator($promocode, new ArrayObject(["user", "number", "date"]));
                $validator->validate();
                $reward = $promocode->execute();
			    $mysidia->session->terminate("promo");
            }
            catch(PromocodeException $pre){ 
    			$status = $pre->getMessage();
			    throw new InvalidActionException($status);              
            }
            $this->setField("type", new MysString($promocode->getType()));
            $this->setField("reward", $reward ? new MysString($reward) : NULL);
			return;
		}
        $mysidia->session->assign("promo", 1);		  
	}              
}