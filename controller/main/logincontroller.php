<?php

namespace Controller\Main;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Utility\Password;
use Service\ApplicationService\AccountService;
use Service\ApplicationService\AuthenticationException;
use Service\ApplicationService\LoginException;

class LoginController extends AppController{

    private $accountService;
    
    public function __construct(){
        parent::__construct();
        $this->accountService = new AccountService(new Password);
    }
	
	public function index(){
	    $this->access = "guest";
	    $this->handleAccess();
	    $mysidia = Registry::get("mysidia");

	    if($mysidia->input->post("submit")){
            if(!$mysidia->input->post("username") || !$mysidia->input->post("password")) throw new LoginException("fail_blank");
			try{
                $this->accountService->authenticate($mysidia->input->post("username"), $mysidia->input->post("password"));
				$this->accountService->login($mysidia->input->post("username"));
		        $mysidia->session->terminate("clientip");
			}
            catch(AuthenticationException $ane){ 
                throw new LoginException("fail_details");
            }
		}		
	    $mysidia->session->assign("clientip", $_SERVER['REMOTE_ADDR']);
	}
	
	public function logout(){
        $this->access = "member";
	    $this->handleAccess();
        $this->accountService->logout();		
	}
}