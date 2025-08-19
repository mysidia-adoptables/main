<?php

namespace View\AdminCP;

use Resource\Collection\LinkedHashMap;
use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\CheckBox;
use Resource\GUI\Container\Form;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Resource\Native\MysString;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\GroupTableHelper;

class UsergroupView extends View
{
    public function index()
    {
        parent::index();
        $document = $this->document;
        $helper = new GroupTableHelper();
        $usergroupTable = new TableBuilder("user");
        $usergroupTable->setAlign(new Align("center", "middle"));
        $usergroupTable->buildHeaders("ID", "Usergroup", "Can Adopt Pets", "Can Use PM", "Can Access ACP", "Edit", "Delete");
        $usergroupTable->setHelper($helper);

        $usergroups = $this->getField("usergroups");
        $iterator = $usergroups->iterator();
        while ($iterator->hasNext()) {
            $usergroup = $iterator->next();
            $cells = new LinkedList();
            $cells->add(new TCell($usergroup->getID()));
            $cells->add(new TCell($usergroup->getGroupname()));
            $cells->add(new TCell($helper->getPermissionImage($usergroup->getPermission("canadopt"))));
            $cells->add(new TCell($helper->getPermissionImage($usergroup->getPermission("canpm"))));
            $cells->add(new TCell($helper->getPermissionImage($usergroup->getPermission("cancp"))));
            $cells->add(new TCell($helper->getEditLink($usergroup->getID())));
            $cells->add(new TCell($helper->getDeleteLink($usergroup->getID())));
            $usergroupTable->buildRow($cells);
        }
        $document->add($usergroupTable);

        $pagination = $this->getField("pagination");
        $document->addLangvar($pagination->showPage());
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit") && $mysidia->input->post("group")) {
            $document->setTitle($this->lang->added_title);
            $document->addLangvar($this->lang->added);
            return;
        }

        $document->setTitle($this->lang->add_title);
        $document->addLangvar($this->lang->add);
        $usergroupForm = new FormBuilder("addform", "add", "post");
        $usergroupForm->buildComment("New Usergroup Name: ")
            ->buildTextField("group")
            ->buildButton("Create new Usergroup", "submit", "submit");
        $document->add($usergroupForm);
    }

    public function edit()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $usergroup = $this->getField("usergroup");
        if (!$usergroup) {
            $this->index();
        } elseif ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->edited_title);
            $document->addLangvar($this->lang->edited);
        } else {
            $document->setTitle("{$this->lang->edit_title}: {$usergroup->getID()}");
            $document->addlangvar("{$this->lang->edit} {$usergroup->getGroupname()}, {$this->lang->edit2}");
            $checkBoxes = new LinkedHashMap();
            $permissions = ["canadopt", "canpm", "cancp", "canmanageusers", "canmanageadopts", "canmanagecontent", "canmanagesettings", "canmanageads"];
            foreach ($permissions as $permission) {
                $checkBoxes->put(new MysString($permission), new CheckBox($this->lang->{$permission}, $permission, "yes", $usergroup->getPermission($permission) == "yes"));
            }
            $usergroupForm = new Form("editform", $usergroup->getID(), "post");
            $usergroupForm->add($checkBoxes->get(new MysString("canadopt")));
            $usergroupForm->add($checkBoxes->get(new MysString("canpm")));
            $usergroupForm->add(new Comment("<u>Admin Settings: </u>", true, "b"));
            $usergroupForm->add($checkBoxes->get(new MysString("cancp")));
            $usergroupForm->add($checkBoxes->get(new MysString("canmanageadopts")));
            $usergroupForm->add(new Comment($this->lang->notice));
            $usergroupForm->add($checkBoxes->get(new MysString("canmanageads")));
            $usergroupForm->add($checkBoxes->get(new MysString("canmanagecontent")));
            $usergroupForm->add($checkBoxes->get(new MysString("canmanagesettings")));
            $usergroupForm->add($checkBoxes->get(new MysString("canmanageusers")));
            $usergroupForm->add(new Comment($this->lang->warning));
            $usergroupForm->add(new Button("Edit Usergroup", "submit", "submit"));
            $document->add($usergroupForm);
        }
    }

    public function delete()
    {
        $document = $this->document;
        $usergroup = $this->getField("usergroup");
        if (!$usergroup) {
            $this->index();
        } else {
            $document->setTitle($this->lang->delete_title);
            $document->addLangvar($this->lang->delete);
        }
    }
}
