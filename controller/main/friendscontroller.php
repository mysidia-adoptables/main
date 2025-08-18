<?php

namespace Controller\Main;
use Model\DomainModel\FriendRequest;
use Model\DomainModel\Member;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\DuplicateIDException;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;
use Resource\Native\MysString;
use Service\ApplicationService\FriendService;

class FriendsController extends AppController{

	private $friendService;

    public function __construct(){
        parent::__construct("member");
		$mysidia = Registry::get("mysidia");
        if($mysidia->systems->friends != "enabled") throw new NoPermissionException("The admin has turned off friend feature for this site, please contact him/her for detailed information.");		            
		if(!$mysidia->user->hasPermission("canfriend")) throw new NoPermissionException("banned");
		$this->friendService = new FriendService($mysidia->user);
    }
	
	public function index(){
	    throw new InvalidActionException("global_action");
	}
	
	public function request($uid = NULL){
		$mysidia = Registry::get("mysidia");        
	    if(!$uid) throw new InvalidIDException("friend_id");
        if($uid == $mysidia->user->getID()) throw new InvalidIDException("friend_self");
        $recipient = new Member($uid);
        
        if(!$this->friendService->isFriendWith($recipient)){	  
            if($this->friendService->hasRequest($recipient)){ 
                throw new DuplicateIDException("<br>Invalid Action! This is a duplicate friend request between you and {$recipient->getUsername()}.");                
            }
            $this->friendService->sendRequest($recipient);
            $this->setField("recipient", $recipient);
		}
        else throw new InvalidIDException("<br>Invalid Action! The user {$recipient->getUsername()} is already on your friendlist.");
	}
			
	public function option(){
		$mysidia = Registry::get("mysidia");
	    $options = $mysidia->user->getOption();
		if($mysidia->input->post("submit")){
            $options->setPrivacy($mysidia->input->post("pm"), $mysidia->input->post("vm"), $mysidia->input->post("trade"));
		    return;
		}	
		$this->setField("options", $options);	
	}
	
	public function edit($fid = NULL, $confirm = NULL){
        $mysidia = Registry::get("mysidia");
	    switch($confirm){
            case "accept":
                if(!$fid) throw new InvalidIDException("request_invalid");
                $friendrequest = $this->friendService->getValidRequest($fid);
		        $friendrequest->setStatus("accepted");
                $sender = $friendrequest->getSender(Model::MODEL);
                $this->friendService->addFriend($sender);
                $this->setField("sender", $sender);				
	            break;
	        case "decline":
                if(!$fid) throw new InvalidIDException("request_invalid");
                $friendrequest = $this->friendService->getValidRequest($fid);
                $sender = $friendrequest->getSender(Model::MODEL);
		        $friendrequest->setStatus("declined");
                $this->setField("sender", $sender);					
                break;
	        default:
                $stmt = $mysidia->db->select("friend_requests", [], "touser = '{$mysidia->user->getID()}' AND status = 'pending'");
				if($stmt->rowCount() == 0) throw new InvalidIDException("request_empty");
                $requests = new ArrayList;
                while($dto = $stmt->fetchObject()){
                    $requests->add(new FriendRequest($dto->fid, $dto));
                }
				$this->setField("requests", $requests);
        }
        $this->setField("confirm", new MysString($confirm));
	}
	
	
	public function delete($uid){      
	    if(!$uid) throw new InvalidIDException("friend_id");
        $this->friendService->removeFriend(new Member($uid));        
	}
}