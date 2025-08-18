<?php

namespace Controller\Main;
use PDO;
use Model\DomainModel\Adoptable;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;
use Resource\Native\MysArray;

class AdoptController extends AppController{

    public function __construct(){
        parent::__construct("member");
		$mysidia = Registry::get("mysidia");
        if($mysidia->systems->adopts != "enabled") throw new NoPermissionException("The admin has turned off adoption feature for this site, please contact him/her for detailed information.");
		if($mysidia->usergroup->getPermission("canadopt") != "yes"){
		    throw new NoPermissionException("permission");
		}	
    }
	
	public function index(){
	    $mysidia = Registry::get("mysidia");
	    if($mysidia->input->post("submit")){
		    $this->access = "member";
	        $this->handleAccess();
            $id = $mysidia->input->post("id");
			if($mysidia->session->fetch("adopt") != 1 || !$id) throw new InvalidIDException("global_id");			
			
			$adopt = new Adoptable($id);			    
			$conditions = $adopt->getConditions();
			if(!$conditions->checkConditions()) throw new NoPermissionException("condition");
			
			$name = $mysidia->input->post("name") ? $mysidia->input->post("name") : $adopt->getType();
            $ownedAdopt = $adopt->makeOwnedAdopt($mysidia->user->getID(), $name);		    	
			$this->setField("ownedAdopt", $ownedAdopt);
		    return;
		}
		
		$mysidia->session->assign("adopt", 1, TRUE);
        $ids = $mysidia->db->select("adoptables", ["id"], "shop='none'")->fetchAll(PDO::FETCH_COLUMN);
        $total = $ids ? count($ids) : 0;
		
		if($total == 0) throw new InvalidActionException("adopt_none");
		else{		
		    $adopts = new MysArray($total);
			$available = 0;
			
		    foreach($ids as $id){
                $adopt = new Adoptable($id);
			    $conditions = $adopt->getConditions();	
      			if($conditions->checkConditions()) $adopts[$available++] = $adopt;	
            }
			
            if($available == 0) throw new InvalidActionException("adopt_none");
            else $adopts->setSize($available);			
		}		
		$this->setField("adopts", $adopts);
	}
}