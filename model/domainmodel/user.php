<?php

namespace Model\DomainModel;
use Resource\Core\Model;
use Resource\Utility\Date;

abstract class User extends Model{
    
    const IDKEY = "uid";
    protected $uid;
    protected $username;
    protected $ip;
    protected $usergroup;
    protected $lastActivity;
    
    public function getUsername(){
        return $this->username;
    }
    
    public function getIP(){
        return $this->ip;
    }
    
    public function getUsergroup($fetchMode = ""){
	    if($fetchMode == Model::MODEL) return new Usergroup($this->usergroup);
        else return $this->usergroup;        
    }
    
    public function getUsergroupName(){
        if(!$this->usergroup) return NULL;
        return $this->getUsergroup(Model::MODEL)->getGroupname();
    }
    
    public function getLastActivity(){
        return $this->lastActivity;
    }
    
    public function __toString() {
        return $this->username;
    }
    
    abstract public function isCurrentUser();
    abstract public function isLoggedIn();
    abstract public function isAdmin();   
    abstract public function isBanned(); 
    abstract public function getTheme();
    abstract public function getVotes(?Date $time = NULL);
}