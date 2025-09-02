<?php

namespace Controller\AdminCP;

use Resource\Core\FrontController;
use Resource\Core\Registry;
use Resource\Utility\Password;
use Service\ApplicationService\AccountService;
use Service\ApplicationService\AuthenticationException;

class IndexController extends FrontController
{
    public const string DENIED = "You do not have permission to access Admin Control Panel.";
    public const string BLANK = "You have not entered all of your login information yet.";
    public const string INCORRECT = "Wrong information entered, please fill in login form again.";
    public const string ALREADY = "You are already logged into admin control panel...";
    private readonly string $session;
    private $accountService;

    public function __construct()
    {
        $mysidia = Registry::get("mysidia");
        parent::__construct();
        $this->session = $mysidia->session->getid();
        $this->accountService = new AccountService(new Password());
    }

    public function index()
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->session->terminate("status");
        if (!$mysidia->session->fetch("acplogin")) {
            if ($mysidia->input->post("submit")) {
                $this->handleLogin();
                $mysidia->session->assign("status", "handle");
            } else {
                $mysidia->session->assign("status", "prepare");
            }
            return;
        }
    }

    private function handleLogin()
    {
        $mysidia = Registry::get("mysidia");
        if (!$mysidia->input->post("username") || !$mysidia->input->post("password")) {
            $this->setFlags("global_error", self::BLANK);
        } else {
            try {
                $this->accountService->authenticate($mysidia->input->post("username"), $mysidia->input->post("password"));
                if ($mysidia->session->fetch("acplogin")) {
                    $this->setFlags("global_error", self::ALREADY);
                }
                $mysidia->cookies->setAdminCookies();
                $mysidia->session->assign("acplogin", true);
            } catch (AuthenticationException) {
                $mysidia->cookies->loginAdminCookies();
                $this->setFlags("global_error", self::INCORRECT);
            }
        }
        return true;
    }

    public function getRequest()
    {
        $mysidia = Registry::get("mysidia");
        if (!$mysidia->user->isAdmin()) {
            header('Location: /');
            exit;
        }
        return $mysidia->user->isLoggedIn() ? parent::getRequest() : false;
    }
}
