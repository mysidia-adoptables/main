<?php

namespace Controller\Main;
use Model\DomainModel\Member;
use Model\DomainModel\PasswordReset;
use Resource\Core\AppController;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidCodeException;
use Resource\Exception\PasswordException;
use Resource\Native\MysString;
use Resource\Utility\Password;
use Service\ApplicationService\AccountService;

class ForgotpassController extends AppController{

    private $accountService;
    
    public function __construct(){
        parent::__construct("guest");
        $this->accountService = new AccountService(new Password);
    }
	
	public function index(){
		$mysidia = Registry::get("mysidia");		
		if($mysidia->input->post("submit")){
            $values = ["username" => $mysidia->input->post("username"), "email" => $mysidia->input->post("email")];
		    $uid = $mysidia->db->select("users", ["uid"], "username = :username AND email = :email", $values)->fetchColumn();
	        if(!$uid) throw new PasswordException("match");			 
	        else{
                $user = new Member($uid);
                $passwordReset = $user->createPasswordReset();
                $passwordReset->sendResetEmail($user);
	        }
            return;
		}		  
	}
	
	public function reset(){
	    $mysidia = Registry::get("mysidia");	
	    if($mysidia->input->post("submit")){
            $values = ["username" => $mysidia->input->post("username"), "email" => $mysidia->input->post("email"), "code" => $mysidia->input->post("resetcode")];
		    $dto = $mysidia->db->select("passwordresets", [], "username = :username AND email = :email AND code= :code ORDER BY id DESC LIMIT 1", $values)->fetchObject();	
		    if(!$dto) throw new InvalidCodeException("invalidcode");		
	        else{
                $passwordReset = new PasswordReset($dto->id, $dto->username, $dto);
                $user = $passwordReset->getUser(Model::MODEL);
		        $tempPass = $mysidia->user->reset($passwordReset->getCode()); 
                $this->accountService->updatePassword($user, $tempPass);
                $this->setField("username", new MysString($user->getUsername()));
                $this->setField("tempPass", new MysString($tempPass));				
	        }		 	    
			return;
		}
	}
}