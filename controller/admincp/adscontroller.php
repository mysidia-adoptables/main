<?php

namespace Controller\AdminCP;
use Model\DomainModel\Advertisement;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;
use Resource\Utility\Date;

class AdsController extends AppController{

	const PARAM = "aid";
	
	public function __construct(){
        parent::__construct();
		$mysidia = Registry::get("mysidia");
		if($mysidia->usergroup->getpermission("canmanageads") != "yes"){
		    throw new NoPermissionException("You do not have permission to manage ads.");
		}
    }
	
	public function index(){
		$mysidia = Registry::get("mysidia");	
		$stmt = $mysidia->db->select("ads");
        $num = $stmt->rowCount();
        if($num == 0) throw new InvalidIDException("default_none");
        $ads = new ArrayList;
        while($dto = $stmt->fetchObject()){
            $ads->add(new Advertisement($dto->id, $dto));
        }
        $this->setField("ads", $ads);
	}
	
	public function add(){
        $mysidia = Registry::get("mysidia");		
	    if($mysidia->input->post("submit")){
            $this->dataValidate();
		    $date = new Date;
			$mysidia->db->insert("ads", ["id" => NULL, "adname" => $mysidia->input->post("adname"), "text" => $mysidia->input->post("description"), "page" => $mysidia->input->post("adpage"), 
			                             "impressions" => (int)$mysidia->input->post("impressions"),  "actualimpressions" => 0, "date" => $date->format('Y-m-d'), "status" => 'active', "user" => NULL, "extra" => NULL]);			
		}
	}
	
	public function edit($aid = NULL){
	    $mysidia = Registry::get("mysidia");
	    if(!$aid) $this->index();
		elseif($mysidia->input->post("submit")){
            $this->dataValidate();
            $ad = new Advertisement($aid);
		    $mysidia->db->update("ads", ["adname" => $mysidia->input->post("adname"), "text" => $mysidia->input->post("description"), "page" => $mysidia->input->post("adpage"), "impressions" => (int)$mysidia->input->post("impressions")], "id='{$ad->getID()}'");
			if($mysidia->input->post("aimp") >= $mysidia->input->post("impressions") && $mysidia->input->post("impressions") != 0){
                $ad->updateStatus("inactive");
            }
			else $ad->updateStatus("active");
		}
		else{
            $ad = new Advertisement($aid);
            $this->setField("ad", $ad);	
		}
	}
	
	public function delete($aid = NULL){
		$mysidia = Registry::get("mysidia");
        if(!$aid) $this->index();
        else{
            $ad = new Advertisement($aid);
            $mysidia->db->delete("ads", "id='{$ad->getID()}'");
        }
	}

    private function dataValidate(){
        $mysidia = Registry::get("mysidia");
		if(!$mysidia->input->post("adname")) throw new BlankFieldException("name");
		if(!$mysidia->input->post("description")) throw new BlankFieldException("text");        
    }
}