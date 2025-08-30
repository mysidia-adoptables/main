<?php

namespace Model\DomainModel;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;

class UserPermission extends Model{
    
    const IDKEY = "uid";
    protected $uid; 
    protected $canlevel;
    protected $canvm;
    protected $canfriend;
    protected $cantrade;
    protected $canbreed;
    protected $canpound;
    protected $canshop;
        
    protected $user;
    
    public function __construct($uid, $dto = NULL, User|NULL $user = NULL){
		$mysidia = Registry::get("mysidia");
        if(!$dto){
            $prefix = constant("PREFIX");
	        $dto = $mysidia->db->join("users", "users.uid = users_permissions.uid")
                           ->select("users_permissions", [], "{$prefix}users.uid = :uid", ["uid" => $uid])->fetchObject();
            if(!is_object($dto)) throw new MemberNotfoundException("The specified user permission {$uid} does not exist...");
        }
        parent::__construct($dto);
        $this->user = $user ? $user : new Member($uid, $dto);
    }
    
    public function getPermission($perms){
	    if(isset($this->$perms)) return $this->$perms;
        else{
            $usergroup = $this->user->getUsergroup(Model::MODEL);
            return $usergroup->getPermission($perms);
        }
    }
    
    public function hasPermission($perms){
        return ($this->getPermission($perms) == "yes");
    }
  
    public function setPermission($fields = []){
        $mysidia = Registry::get("mysidia");
        if(!$this->isAssoc($fields)) throw new InvalidIDException('The parameter must be an associative array...');
	    $mysidia->db->update("users_permissions", $fields, "uid ='{$this->uid}'");
    }

    public function getUser(){
        return $this->user;
    }    
    
    protected function save($field, $value){
		$mysidia = Registry::get("mysidia");
		$mysidia->db->update("users_permissions", [$field => $value], "uid='{$this->uid}'");         
    }
}