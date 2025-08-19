<?php

namespace Controller\Main;
use Model\DomainModel\Member;
use Model\DomainModel\VisitorMessage;
use Resource\Collection\LinkedList;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\DuplicateIDException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;

class VmessageController extends AppController{

    public function __construct(){
        parent::__construct("member");
	    $mysidia = Registry::get("mysidia");
        if($mysidia->systems->vmessages != "enabled") throw new NoPermissionException("disabled");		            	
    }

    public function index(): never{
        throw new InvalidActionException("global_action");
    }

    public function view($uid, $uid2){
	    $mysidia = Registry::get("mysidia");		
		if(!$uid || !$uid2) throw new InvalidIDException("user_nonexist");
		if($uid == $uid2) throw new DuplicateIDException("user_same");
        $user = new Member($uid);
        $user2 = new Member($uid2);
        if(!$user || !$user2) throw new InvalidIDException("user_nonexist");
        
        $values = ["uid" => $uid, "uid2" => $uid2];
        $stmt = $mysidia->db->select("visitor_messages", [], "(touser = :uid AND fromuser = :uid2) OR (fromuser = :uid AND touser = :uid2) ORDER BY vid DESC LIMIT 0, 25", $values);	
        $vmessages = new LinkedList;
		while($dto = $stmt->fetchObject()){
		    $vmessages->add(new VisitorMessage($dto->vid, $dto));
		}
        $this->setField("user", $user);
        $this->setField("user2", $user2);
		$this->setField("vmessages", $vmessages);	
	}
	
	public function edit($vid){
	    $mysidia = Registry::get("mysidia");			
		$vmessage = new VisitorMessage($vid);
		if(!$vmessage->isSender($mysidia->user) && !$mysidia->user->isAdmin()){
		    throw new NoPermissionException("edit_denied");
		}	

		$this->setField("vmessage", $vmessage);	
	    if($mysidia->input->post("submit")) $vmessage->edit($mysidia->input->post("vmtext"));
	}

    public function delete($vid){
	    $mysidia = Registry::get("mysidia");		
		$vmessage = new VisitorMessage($vid);
		if(!$vmessage->isSender($mysidia->user) && !$mysidia->user->isAdmin()){
		    throw new NoPermissionException("delete_denied");
		}		
	    $vmessage->remove();
	}
}