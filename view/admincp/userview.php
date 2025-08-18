<?php

namespace View\AdminCP;
use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Image;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\UserTableHelper;

class UserView extends View{

	public function index(){
	    parent::index();
		$document = $this->document;				
        $helper = new UserTableHelper;
		$userTable = new TableBuilder("user");
		$userTable->setAlign(new Align("center", "middle"));
		$userTable->buildHeaders("User ID", "Username", "Email", "IP", "Usergroup", "Edit", "Delete");
		$userTable->setHelper($helper);
        
        $users = $this->getField("users");
        $iterator = $users->iterator();
        while($iterator->hasNext()){
            $user = $iterator->next();
		    $cells = new LinkedList;
		    $cells->add(new TCell($user->getID()));
		    $cells->add(new TCell($helper->getProfileLink($user->getID(), $user->getUsername())));
		    $cells->add(new TCell($user->getEmail()));
            $cells->add(new TCell($user->getIP()));
            $cells->add(new TCell($user->getUsergroup()));
		    $cells->add(new TCell($helper->getEditLink($user->getID())));
		    $cells->add(new TCell($helper->getDeleteLink($user->getID())));
		    $userTable->buildRow($cells);	            
        }		
        $document->add($userTable);	

        $pagination = $this->getField("pagination");
		$document->addLangvar($pagination->showPage());	
	}
	
	public function edit(){
	    $mysidia = Registry::get("mysidia");
		$document = $this->document;
        $user = $this->getField("user");
	    if(!$user) $this->index();
		elseif($mysidia->input->post("submit")){
			$document->setTitle($this->lang->edited_title);
		    $document->addLangvar($this->lang->edited);
		}
		else{
			$document->setTitle($this->lang->edit_title);
		    $document->addLangvar($this->lang->edit);
			$userForm = new FormBuilder("editform", $user->getID(), "post");
			$userForm->add(new Comment("<br><br>"));
			$userForm->buildComment("Assign New Password: ", FALSE)->buildPasswordField("password", "pass1", "", TRUE)
					 ->buildComment("Passwords may contain letters and numbers only. Leave the box blank to keep the current password.")
		             ->buildCheckBox(" Email the user the new password (Only takes effect if setting a new password) ", "emailpwchange", "yes")
					 ->buildComment("Change Email Address: ", FALSE)->buildTextField("email", $user->getEmail())
					 ->buildCheckBox(" Ban this user's rights to click adoptables", "canlevel", "no")
					 ->buildCheckBox(" Ban this user's rights to post profile comments", "canvm", "no")
					 ->buildCheckBox(" Ban this user's rights to make trade offers", "cantrade", "no")
					 ->buildCheckBox(" Ban this user's rights to send friend requests", "canfriend", "no")
					 ->buildCheckBox(" Ban this user's rights to breed adoptables", "canbreed", "no")
					 ->buildCheckBox(" Ban this user's rights to abandon adoptables", "canpound", "no")
					 ->buildCheckBox(" Ban this user's rights to visit Shops", "canshop", "no")
                     ->buildCheckBox(" Unban this user to the rights above", "unban", "yes");					 
			$userForm->add(new Comment("<u>{$user->getUsername()}'s Current Usergroup:</u> Group {$user->getUsergroupName()}"));	
            $userForm->add(new Comment("Change {$user->getUsername()}'s Usergroup To:", FALSE));
	        $userForm->buildDropdownList("usergroup", "UsergroupList", $user->getUsergroup());				
			$userForm->buildButton("Edit User", "submit", "submit");
			$document->add($userForm);
		}
	}
	
	public function delete(){
		$document = $this->document;
        $user = $this->getField("user");
	    if(!$user) $this->index();
		else{
		    $document->setTitle($this->lang->delete_title);
		    $document->addLangvar($this->lang->delete);
		    header("Refresh:3; URL='../../index'");
        }
	}
}