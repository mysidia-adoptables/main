<?php

namespace View\Main;

use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Document\Comment;
use Service\Builder\FormBuilder;

class ForgotpassView extends View
{
    public function index()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->email_title);
            $document->addLangvar($this->lang->email);
            return;
        }

        $document->setTitle($this->lang->title);
        $document->addLangvar($this->lang->default);
        $requestForm = new FormBuilder("requestform", "forgotpass", "post");
        ;
        $requestForm->buildComment("username: ", false)
                    ->buildTextField("username")
                    ->buildComment("Email Address: ", false)
                    ->buildPasswordField("email", "email")
                    ->buildParagraph(new Comment(""))
                    ->buildButton("Request Password Reset", "submit", "submit");
        $document->add($requestForm);
    }

    public function reset()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $username = $this->getField("username");
            $tempPass = $this->getField("tempPass");
            $document->setTitle($this->lang->success_title);
            $document->add(new Comment("Dear {$username}, <br>"));
            $document->addLangvar($this->lang->success);
            $document->add(new Comment("<br>Here is your temporary password: <b>{$tempPass}</b><br>"));
            $document->addLangvar($this->lang->instruction);
            return;
        }

        $document->setTitle($this->lang->reset_title);
        $document->addLangvar($this->lang->reset);
        $resetForm = new FormBuilder("requestform", "reset", "post");
        ;
        $resetForm->buildComment("Username: ", false)
                  ->buildTextField("username")
                  ->buildComment("Email Address: ", false)
                  ->buildPasswordField("email", "email")
                  ->buildComment("<br>Reset Code: ", false)
                  ->buildTextField("resetcode")
                  ->buildComment("")
                  ->buildButton("Request Password Reset", "submit", "submit");
        $document->add($resetForm);
    }
}
