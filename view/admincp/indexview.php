<?php

namespace View\AdminCP;

use Resource\Core\Registry;
use Resource\Core\View;
use Service\Builder\FormBuilder;

class IndexView extends View
{
    public function index()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $document->setTitle($this->lang->welcome);
        $status = $mysidia->session->fetch("status");
        if ($status) {
            $this->login($status);
            return;
        }
        $document->addLangvar($this->lang->default . $this->lang->credits);
    }

    private function login($status)
    {
        $method = "{$status}Login";
        $this->$method();
    }

    private function prepareLogin()
    {
        $document = $this->document;
        $document->addLangvar($this->lang->login);

        $loginForm = new FormBuilder("loginform", "", "post");
        $loginForm->buildComment("username: ", false)
            ->buildTextField("username")
            ->buildComment("password: ", false)
            ->buildPasswordField("password", "password", "", true)
            ->buildButton("Log In", "submit", "submit");
        $document->add($loginForm);
    }

    private function handleLogin()
    {
        $document = $this->document;
        $document->addLangvar($this->lang->success);
        $this->refresh(3);
    }
}
