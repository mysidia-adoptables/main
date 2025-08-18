<?php

namespace Controller\Main;
use Model\DomainModel\Member;
use Model\DomainModel\MemberNotfoundException;
use Model\DomainModel\VisitorMessage;
use Model\ViewModel\UserProfileViewModel;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;

class ProfileController extends AppController{
	
	public function index(){
	    $mysidia = Registry::get("mysidia");
		$total = $mysidia->db->select("users", ["uid"])->rowCount();		
		$pagination = new Pagination($total, 10, "profile");
        $pagination->setPage($mysidia->input->get("page"));					
		$stmt = $mysidia->db->select("users", [], "1 ORDER BY uid ASC LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");	
		$users = new ArrayList;
		while($dto = $stmt->fetchObject()){
		    $users->add(new Member($dto->uid, $dto));
		}
		$this->setField("pagination", $pagination);
        $this->setField("users", $users);
	}
	
	public function view($uid = NULL){
		$mysidia = Registry::get("mysidia");
        try{
		    $user = new Member($uid, NULL, TRUE); 
	        $profile = $user->getProfile();
        }
        catch(MemberNotfoundException $mne){
		    throw new InvalidIDException("nonexist");
        }		
       
        if($mysidia->input->post("vmtext")){ 
	        $vmessage = new VisitorMessage;
            $options = $user->getOption();
	        if($options->getVMStatus() == 1 && !$user->isFriend($mysidia->user)) throw new InvalidActionException("VM_friend");   
		    $vmessage->post($user, $mysidia->input->post("vmtext"));
            $this->setField("vmessage", $vmessage);
        }
        $this->setField("user", $user);
		$this->setField("profile", new UserProfileViewModel($profile));
	}
}