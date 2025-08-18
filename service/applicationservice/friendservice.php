<?php

namespace Service\ApplicationService;
use Model\DomainModel\FriendRequest;
use Model\DomainModel\Member;
use Model\DomainModel\PrivateMessage;
use Resource\Collection\ArrayList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Native\MysObject;

class FriendService extends MysObject{ 
    
    private $user;
    private $friendList;
    
    public function __construct(Member $user){
        $this->user = $user;
    }
    
    public function getFriends(){
        if(!$this->friendList){
            $this->friendList = $this->user->getFriendsList(Model::MODEL);
        }
        return $this->friendList;
    }
    
    public function getFriendIDs(){
        return $this->user->getFriendsList();
    }
    
    public function isFriendWith($user = NULL){ 
        if($user instanceof Member){
            $friends = $user->getFriendsList();
            return $friends ? in_array($this->user->getID(), $friends) : FALSE;
        }
        return FALSE;
    }
    
    public function getValidRequest($fid){
        $friendRequest = new FriendRequest($fid);
        if($friendRequest->getStatus() != "pending") throw new InvalidActionException("The friend request is not pending, it may have been accepted or declined.");
        if($friendRequest->getRecipientID() != $this->user->getID()) throw new InvalidActionException("The friend request has invalid recipient.");
        return $friendRequest;
    }
    
    public function getPendingRequests(){
        $mysidia = Registry::get("mysidia");
        $stmt = $mysidia->db->select("friend_requests", [], "touser='{$this->user->getID()}' AND status='pending'");
        if($stmt->rowCount() == 0) throw new InvalidIDException("request_empty");
        $requests = new ArrayList;
        while($dto = $stmt->fetchObject){ 
            $requests->add(new FriendRequest($dto->fid, $dto));
        }
        return $requests;
    }
    
    public function hasRequest(Member $user){ 
	    $mysidia = Registry::get("mysidia");
	    $exist1  = $mysidia->db->select("friend_requests", ["fid"], "fromuser='{$this->user->getID()}' AND touser='{$user->getID()}'")->fetchColumn();	
        $exist2 = $mysidia->db->select("friend_requests", ["fid"], "touser='{$this->user->getID()}' AND fromuser='{$user->getID()}'")->fetchColumn();        
        return ($exist1 || $exist2);
    }
    
    public function sendRequest(Member $user){
        $title = "New Friend Request Received";
		$offer = "You have received a friendrequest from {$this->user->getUsername()}! You may go to your usercp to accept/decline this offer.";
	    $frequest = new FriendRequest;
		$frequest->setMessage($offer);
		$frequest->post($user->getID());
		 
		 // And at the very last, send a PM to the very user receiving this request
		$message = new PrivateMessage;
		$message->setRecipient($user->getID());
		$message->setMessage($title, $offer);
		$message->post();    
    }
    
    public function addFriend(Member $user){
        if($this->isFriendWith($user)) throw new InvalidIDException("The user is already on the friend list.");
        $this->user->addFriend($user->getID());
        $user->addFriend($this->user->getID());        
    }
    
    public function removeFriend(Member $user){
        $mysidia = Registry::get("mysidia");
        if(!$this->isFriendWith($user)) throw new InvalidIDException("The user is not currently on the friend list.");
        $this->user->removeFriend($user->getID());
        $user->removeFriend($this->user->getID());
        $fid = $mysidia->db->select("friend_requests", ["fid"], "(fromuser = '{$this->user->getID()}' AND touser = '{$user->getID()}') OR (touser = '{$this->user->getID()}' AND fromuser = '{$user->getID()}')")->fetchColumn();
        if($fid) $mysidia->db->delete("friend_requests", "fid = '{$fid}'");        
    }
}