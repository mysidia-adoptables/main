<?php

namespace View\Main;

use Model\ViewModel\VisitorMessageViewModel;
use Resource\Core\Registry;
use Resource\Core\View;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;

class VmessageView extends View
{

    public function view()
    {
        $document = $this->document;
        $user = $this->getField("user");
        $user2 = $this->getField("user2");
        $document->setTitle($this->lang->view_title);
        $document->addLangvar("{$this->lang->view} {$user->getUsername()} and {$user2->getUsername()}<br><br>");

        $vmList = new TableBuilder("vmlist", "", false);
        $vmessages = $this->getField("vmessages");
        $iterator = $vmessages->iterator();
        while ($iterator->hasNext()) {
            $vmessage = new VisitorMessageViewModel($iterator->next());
            $vmList->buildRow($vmessage->view());
        }
        $document->add($vmList);
    }

    public function edit()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $document->setTitle($this->lang->edit_title);

        $vmessage = $this->getField("vmessage");
        if ($mysidia->input->post("submit")) {
            $document->addLangvar($this->lang->edit_success);
            return;
        }

        $editForm = new FormBuilder("editform", "", "post");
        $editForm->buildTextArea("vmtext", $vmessage->getContent())
            ->buildButton("Edit Message", "submit", "submit");
        $document->addLangvar($this->lang->edit_default);
        $document->add($editForm);
    }

    public function delete()
    {
        $document = $this->document;
        $document->setTitle($this->lang->delete_title);
        $document->addLangvar($this->lang->delete_success);
    }
}
