<?php

namespace Model\DomainModel;
use Resource\Core\Model;
use Resource\Core\Registry;

class UserContact extends Model{
    
    const IDKEY = "uid";
    protected $uid; 
    protected $website;
    protected $facebook;
    protected $twitter;
    protected $aim;
    protected $yahoo;
    protected $msn;
    protected $skype;
    
    protected $user;
    
    public function __construct($uid, $dto = NULL, User|NULL $user = NULL){
		$mysidia = Registry::get("mysidia");
        if(!$dto){
            $prefix = constant("PREFIX");
	        $dto = $mysidia->db->join("users", "users.uid = users_contacts.uid")
                           ->select("users_contacts", [], "{$prefix}users.uid = :uid", ["uid" => $uid])->fetchObject();
            if(!is_object($dto)) throw new MemberNotfoundException("The specified user contact {$uid} does not exist...");
        }
        parent::__construct($dto);
        $this->user = $user ? $user : new Member($uid, $dto);
    }
    
    public function getWebsite(){
        return $this->website;
    }
    
    public function getFacebook(){
        return $this->facebook;
    }
    
    public function getTwitter(){
        return $this->twitter;
    }
    
    public function getAIM(){
        return $this->aim;
    }
    
    public function getYahoo(){
        return $this->yahoo;
    }
    
    public function getYIM(){ 
        return $this->getYahoo();
    }
  
    public function getMSN(){
        return $this->msn;
    }
    
    public function getSkype(){
        return $this->skype;
    }
    
    public function getUser(){
        return $this->user;
    }
    
    protected function save($field, $value){
		$mysidia = Registry::get("mysidia");
		$mysidia->db->update("users_contacts", [$field => $value], "uid='{$this->uid}'");         
    }
}