<?php

namespace Controller\Main;
use Model\DomainModel\ProfileException;
use Model\DomainModel\UserProfile;
use Model\ViewModel\UserProfileViewModel;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\EmailException;
use Resource\Exception\PasswordException;
use Resource\Native\Integer;
use Resource\Native\MysString;
use Resource\Utility\Password;
use Service\ApplicationService\AccountService;

class AccountController extends AppController{
    
    private $accountService;
    
    public function __construct(){
        parent::__construct("member");
        $this->accountService = new AccountService(new Password);
    }
	
	public function password(){
		$mysidia = Registry::get("mysidia");		
	    if($mysidia->input->post("submit")){
            if(!$mysidia->input->post("np1") || !$mysidia->input->post("np2")) throw new PasswordException("password_blank");
            if($mysidia->input->post("np1") != $mysidia->input->post("np2")) throw new PasswordException("password_new");
            $this->accountService->authenticate($mysidia->user->getUsername(), $mysidia->input->post("cpass"));
            $this->accountService->updatePassword($mysidia->user, $mysidia->input->post("np1"));
            $this->accountService->logout();
		}
	}
	
	public function email(){
	    $mysidia = Registry::get("mysidia");		
	    if($mysidia->input->post("submit")){
            if(!$this->accountService->isValidEmail($mysidia->input->post("email"))){ 
                throw new EmailException("email_invalid");
            }
            $mysidia->user->setEmail($mysidia->input->post("email"), Model::UPDATE);
		}
	}
	
	public function friends(){
		$mysidia = Registry::get("mysidia");
        $this->setField("totalFriends", new Integer($mysidia->user->countFriends()));
	    $this->setField("profileViewModel", new UserProfileViewModel($mysidia->user->getProfile()));
	}
	
	public function profile(){
		$mysidia = Registry::get("mysidia");
		$profile = $mysidia->user->getProfile();
		
	    if($mysidia->input->post("submit")){
		    $mysidia->db->update("users_profile", ["avatar" => $mysidia->input->post("avatar"), "nickname" => $mysidia->input->post("nickname"), "gender" => $mysidia->input->post("gender"), "color" => $mysidia->input->post("color"), "bio" => $mysidia->input->post("bio"), "favpet" => (int)$mysidia->input->post("favpet"), "about" => $mysidia->input->post("about")], "uid = '{$mysidia->user->getID()}'");
			return;
		}
		
        if(!($profile instanceof UserProfile)) throw new ProfileException("profile_nonexist");
        elseif(!$profile->isUser($mysidia->user)) throw new ProfileException("profile_edit");
        else{
            $stmt = $mysidia->db->select("owned_adoptables", ["name", "aid"], "owner = '{$mysidia->user->getID()}'");
            $map = $mysidia->db->fetchMap($stmt);
	        $this->setField("profile", $profile);
			$this->setField("petMap", $map);
        }
	}
	
	public function contacts(){
	    $mysidia = Registry::get("mysidia");		
	    if($mysidia->input->post("submit")){
		    $newmsgnotify = ($mysidia->input->post("newmsgnotify") == 1) ? 1 : 0 ;
            $mysidia->db->update("users_options", ["newmessagenotify" => $newmsgnotify], "uid = '{$mysidia->user->getID()}'");
            $mysidia->db->update("users_contacts", ["website" => $mysidia->input->post("website"), "facebook" => $mysidia->input->post("facebook"), "twitter" => $mysidia->input->post("twitter"), "aim" => $mysidia->input->post("aim"), "yahoo" => $mysidia->input->post("yim"), "msn" => $mysidia->input->post("msn"), "skype" => $mysidia->input->post("skype")], "uid = '{$mysidia->user->getID()}'");
		    return;
		}
        
        $contactList = new ArrayList;	
		$contactList->add(new MysString("website"));
		$contactList->add(new MysString("facebook"));
		$contactList->add(new MysString("twitter"));	
		$contactList->add(new MysString("msn"));
		$contactList->add(new MysString("aim"));
		$contactList->add(new MysString("yim"));
		$contactList->add(new MysString("skype"));	
		$this->setField("contactList", $contactList);
	}
}