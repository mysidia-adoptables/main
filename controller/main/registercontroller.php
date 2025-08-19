<?php

namespace Controller\Main;

use ArrayObject;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Exception\NoPermissionException;
use Resource\Utility\Password;
use Service\ApplicationService\AccountService;
use Service\ApplicationService\RegistrationException;
use Service\Validator\RegistrationValidator;

class RegisterController extends AppController
{

    private $accountService;

    public function __construct()
    {
        parent::__construct("guest");
        $mysidia = Registry::get("mysidia");
        if ($mysidia->systems->register != "enabled") throw new NoPermissionException("The admin has turned off registration for this site, please contact him/her for detailed information.");
        $this->accountService = new AccountService(new Password);
    }

    public function index()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            $mysidia->session->validate("register");
            try {
                $formData = ["username" => $mysidia->input->post("username"), "password" => $mysidia->input->post("pass1"), "password2" => $mysidia->input->post("pass2"), "email" => $mysidia->input->post("email"),
                    "birthday" => $mysidia->input->post("birthday"), "ip" => $mysidia->input->post("ip"), "avatar" => $mysidia->input->post("avatar"), "bio" => $mysidia->input->post("bio"), "color" => $mysidia->input->post("color"),
                    "gender" => $mysidia->input->post("gender"), "nickname" => $mysidia->input->post("nickname"), "answer" => $mysidia->input->post("answer"), "tos" => $mysidia->input->post("tos")];
                $form = new ArrayObject($formData);
                $validations = new ArrayObject(array_keys($formData));
                $validator = new RegistrationValidator($form, $validations);
                $validator->validate();
            } catch (RegistrationException $rge) {
                $status = $rge->getMessage();
                throw new InvalidActionException($status);
            }

            $this->accountService->register($form);
            $this->accountService->login($form["username"]);
            $mysidia->session->terminate("register");
            return;
        }
        $mysidia->session->assign("register", 1, true);
    }
}
