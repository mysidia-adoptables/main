<?php

namespace Controller\Main;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\NoPermissionException;
use Resource\Native\Integer;
use Service\ApplicationService\OnlineService;

class OnlineController extends AppController{

	public function index(){
	    $mysidia = Registry::get("mysidia");
        if($mysidia->systems->online != "enabled") throw new NoPermissionException("The admin has turned off who's online feature for this site, please contact him/her for detailed information.");		            
	    $wol = new OnlineService("members");        
		$this->setField("total", new Integer($wol->getTotal()));
        $this->setField("members", $wol->getOnlineMembers());				    
	}
}