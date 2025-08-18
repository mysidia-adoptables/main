<?php

namespace Model\ViewModel;
use Resource\Core\Registry;
use Resource\Core\ViewModel;
use Resource\GUI\Component\Image;
use Resource\GUI\Container\Table;
use Resource\GUI\Container\TCell;
use Resource\GUI\Container\TRow;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Division;

class PrivateMessageViewModel extends ViewModel{
    
    protected $postbar;
    
    public function getSender($fetchMode = ""){ 
        return $this->model->getSender($fetchMode);
    }
    
    public function getSenderName(){
        return $this->model->getSenderName();
    }
    
    public function getRecipient($fetchMode = ""){
        return $this->model->getRecipient($fetchMode);
    }
    
    public function getRecipientName(){
        return $this->model->getRecipientName();
    }
    
    public function getTitle(){
        return $this->model->getTitle();
    }
    
    public function getContent(){
        return $this->model->getContent();
    }
    
    public function formatContent(){
        return $this->model->format($this->getContent());
    }
    
    public function getMessageBody(){
        if($this->getID() == 0) return FALSE;
	    else{
	        // We are reading this PM now!		
		    $mysidia = Registry::get("mysidia");
		    $pmFormat = "<table width='100%' border='4' cellpadding='3' cellspacing='0' bordercolor='1'>
					         <td>
                                 <table width='100%' border='3' cellpadding='3' cellspacing='0' bordercolor='1'>
					                 <tr>
                                         <td width='100%' class='tr><strong>Date Received: {$this->model->getDateSent('Y-m-d')}</strong></td>
					                 </tr>
                                 </table>
					             <table width='100%' border='2' cellpadding='3' cellspacing='0' bordercolor='1'>
                                     <tr>
                                         <td class='trow'>
                                             <center><a href='../../profile/view/{$this->model->getSender()}' target='_blank'>{$this->model->getSenderName()}</a> sent you this PM.</center>
                                             <br />
                                             {$this->getPostbar()->render()}
                                         </td>
                                     </tr>
					                 <tr>
                                         <td class='trow'>
                                             <center><strong>{$this->model->getTitle()}<br />_______________________________</strong></center><br />{$this->formatContent()}
                                         </td>
					                 </tr>
                                 </table>";
            if($this->model->getFolder() == "inbox"){             
			$pmFormat .= "<table width='100%' border='1' cellpadding='3' cellspacing='0' bordercolor='1'>
					          <tr>
                                  <td width='100%' colspan='2' class='tr'><strong><b><a href='../../messages'><img src='{$mysidia->path->getAbsolute()}templates/icons/next.gif' border=0> Return to Inbox</a> | <a href='../../messages/newpm/{$this->model->getSender()}'><img src='{$mysidia->path->getAbsolute()}templates/icons/comment.gif' border=0> Reply to this Message</a> | <a href='../../messages/report/{$this->getID()}'><img src='{$mysidia->path->getAbsolute()}templates/icons/next.gif' border=0> Report this member</a></b></strong></td>
					          </tr>
                          </table>";
            }
			$pmFormat .= "</td></table><br />";
	        $message = new Division("message");
		    $message->add(new Comment($pmFormat));			 
            return $message;		 
	    }
    }
    
    public function getPostbar(){
        if($this->getID() == 0) return FALSE;
        $sender = $this->model->getSender("model");
	    $profile = $sender->getProfile();
	    $this->postbar = new Table("postbar", "100%", FALSE);
        $postHeader = new TRow;
	    $postHeader->add(new TCell(new Image($profile->getAvatar())));
	    $postHeader->add(new TCell("<b>Member Since: </b><br>{$sender->getMemberSince('Y-m-d')}<br> <b>Bio:</b><br>{$profile->getBio()}<br> "));
	    $postHeader->add(new TCell("<b>Nickname:</b> {$profile->getNickname()}<br><b>Gender:</b> {$profile->getGender()}<br><b>Cash:</b> <a href='../../donate'>{$sender->getMoney()}</a><br>"));
	    $this->postbar->add($postHeader); 
        return $this->postbar; 			 
    }
}