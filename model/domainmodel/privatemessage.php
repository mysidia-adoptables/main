<?php

namespace Model\DomainModel;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Utility\Date;

class PrivateMessage extends Message{

    const IDKEY = "mid";
    protected $mid;
    protected $fromuser;
    protected $touser;
    protected $folder = "inbox";
    protected $status;
    protected $datesent;
    protected $messagetitle;
    protected $messagetext;

    public function __construct($mid = 0, $folder = "inbox", $dto = NULL, $notifier = FALSE){	 
	    $mysidia = Registry::get("mysidia");	  
	    if($mid == 0){
	        //This is a new private message not yet exist in database
		    $this->mid = $mid;
		    $this->fromuser = $mysidia->user->getID();
		    $this->folder = ($folder == "inbox") ? $this->folder : $folder;
            return;
	    }
	    elseif(!$dto){
	        // The private message is not being composed, so fetch the information from database
            $table = ($folder == "inbox") ? "messages" : "folders_messages";
            $dto = $mysidia->db->select($table, [], "mid = :mid", ["mid" => $mid])->fetchObject();
            if(!is_object($dto)) throw new MessageNotfoundException("The message does not exist in database.");	    		
	    }
        parent::__construct($dto);
        if($notifier == TRUE) $this->getNotifier();		
    }
    
    protected function createFromDTO($dto){
        parent::createFromDTO($dto);
        $this->datesent = new Date($dto->datesent);
    }
    
    public function getTitle(){
        if(!empty($this->messagetitle)) return $this->messagetitle;
	    else return FALSE;
    }
  
    public function getContent(){
        if(!empty($this->messagetext)) return $this->messagetext;
	    else return FALSE;
    }

    public function getNotifier(){
        if(is_object($this->notifier)) throw new MessageException("A PM Notifier already exists...");
	    else $this->notifier = new PMNotifier;
    }
    
    public function getFolder(){
        return $this->folder;
    }
    
    public function getStatus(){
        return $this->status;
    }
    
    public function isRead(){
        return ($this->status == "read");
    }

    public function setMessage($title, $text){
        if(empty($title) or empty($text)) throw new InvalidActionException("Cannot set an empty private message");
	    else{
	        $this->messagetitle = $title;
		    $this->messagetext = $this->format($text);
	    }
    }
  
    public function markRead($read = TRUE){
        $mysidia = Registry::get("mysidia");
        $this->status = $read ? "read" : "unread";
	    $mysidia->db->update("messages", ["status" => $this->status], "mid='{$this->mid}'");
    }
  
    public function post(){
        $mysidia = Registry::get("mysidia");
	    $date = new Date;
        if(!$this->messagetitle) $this->messagetitle = $mysidia->input->post("mtitle");
	    if(!$this->messagetext) $this->messagetext = $this->format($mysidia->input->rawPost("mtext")); 
	    $mysidia->db->insert("messages", ["mid" => NULL, "fromuser" => $this->fromuser, "touser" => $this->touser, "status" => "unread", "datesent" => $date->format("Y-m-d"), "messagetitle" => $this->messagetitle, "messagetext" => $this->messagetext]);     
	  
	    if($mysidia->input->post("outbox") == "yes"){
	        $mysidia->db->insert("folders_messages", ["mid" => NULL, "fromuser" => $this->fromuser, "touser" => $this->touser, "folder" => "outbox", "datesent" => $date->format("Y-m-d"), "messagetitle" => $this->messagetitle, "messagetext" => $this->messagetext]);
	    }
        if(is_numeric($mysidia->input->post("draftid"))){
            $mysidia->db->delete("folders_messages", "mid = :mid AND fromuser = '{$mysidia->user->getID()}'", ["mid" => $mysidia->input->post("draftid")]);
        } 
	    return TRUE;
    }
  
    public function postDraft($recipient, $title = NULL, $content = NULL){
        $mysidia = Registry::get("mysidia");
	    $date = new Date;
        if(!is_numeric($recipient)) $recipient = $mysidia->db->select("users", ["uid"], "username = :username", ["username" => $recipient])->fetchColumn();
        $this->recipient = $recipient;
        if($title) $this->messagetitle = $title;
	    if($content) $this->messagetext = $this->format($content); 
	    $mysidia->db->insert("folders_messages", ["mid" => NULL, "fromuser" => $mysidia->user->getID(), "touser" => $this->recipient, "folder" => "draft", "datesent" => $date->format("Y-m-d"), "messagetitle" => $this->messagetitle, "messagetext" => $this->format($this->messagetext)]);     
	    return TRUE;
    }
  
    public function editDraft(){
        $mysidia = Registry::get("mysidia");
	    $date = new DateTime;
        if(!$this->messagetitle) $this->messagetitle = $mysidia->input->post("mtitle");
        if(!$this->messagetext) $this->messagetext = $this->format($mysidia->input->rawPost("mtext")); 
	    $mysidia->db->update("folders_messages", ["fromuser" => $this->fromuser, "touser" => $this->touser, "folder" => "draft", "datesent" => $date->format("Y-m-d"), "messagetitle" => $this->messagetitle, "messagetext" => $this->messagetext], "fromuser='{$mysidia->user->getID()}' AND mid = :mid", ["mid" => $mysidia->input->post("id")]);
        return TRUE;
    }
  
    public function remove(){
        $mysidia = Registry::get("mysidia");
	    if($this->mid == 0) return FALSE;
	    if($this->folder == "inbox") $mysidia->db->delete("messages", "mid = '{$this->mid}'");
        else $mysidia->db->delete("folders_messages", "mid = '{$this->mid}' AND folder = '{$this->folder}'");
	    return TRUE;
    }
  
    public function report(){
        $mysidia = Registry::get("mysidia");
	    $date = new Date;
        $recipient = $mysidia->db->select("users", ["uid"], "username = :username", ["username" => $mysidia->settings->systemuser])->fetchColumn();
        if(!$this->messagetitle) $this->messagetitle = $mysidia->input->post("mtitle");
	    if(!$this->messagetext) $this->messagetext = $this->format($mysidia->input->post("mtext"));
        $mysidia->db->insert("messages", ["mid" => NULL, "fromuser" => $mysidia->user->getID(), "touser" => $recipient, "status" => "unread", "datesent" => $date->format("Y-m-d"), "messagetitle" => "A user has reported private message ID: {$this->mid}", "messagetext" => $this->messagetext]);     
	    return TRUE; 	
    }

    public function format($text){
        return preg_replace('`<((script)|(style))[^>]*>[^<]*</\1>`si', '', stripslashes(html_entity_decode($text)));
    }
  
	protected function save($field, $value){
		$mysidia = Registry::get("mysidia");
		$mysidia->db->update("messages", [$field => $value], "mid = '{$this->mid}'");
	}  
}