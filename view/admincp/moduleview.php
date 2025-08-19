<?php

namespace View\AdminCP;

use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Container\TCell;
use Resource\GUI\Element\Align;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\TableHelper;

class ModuleView extends View
{

    public function index()
    {
        parent::index();
        $document = $this->document;
        $helper = new TableHelper;
        $moduleTable = new TableBuilder("modules");
        $moduleTable->setAlign(new Align("center", "middle"));
        $moduleTable->buildHeaders("ID", "Widget", "Name", "Order", "Status", "Edit", "Delete");
        $moduleTable->setHelper($helper);

        $modules = $this->getField("modules");
        $iterator = $modules->iterator();
        while ($iterator->hasNext()) {
            $module = $iterator->next();
            $cells = new LinkedList;
            $cells->add(new TCell($module->getID()));
            $cells->add(new TCell($module->getWidgetName()));
            $cells->add(new TCell($module->getName()));
            $cells->add(new TCell($module->getOrder()));
            $cells->add(new TCell($module->getStatus()));
            $cells->add(new TCell($helper->getEditLink($module->getID())));
            $cells->add(new TCell($helper->getDeleteLink($module->getID())));
            $moduleTable->buildRow($cells);
        }
        $document->add($moduleTable);

        $pagination = $this->getField("pagination");
        $document->addLangvar($pagination->showPage());
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($mysidia->lang->added_title);
            $document->addLangvar($mysidia->lang->added);
            return;
        }

        $document->setTitle($this->lang->add_title);
        $document->addLangvar($this->lang->add);
        $moduleForm = new FormBuilder("addform", "add", "post");
        $moduleForm->buildComment("Parent Widget: ", false)->buildDropdownList("widget", "ParentWidgetList", "sidebar")
            ->buildComment("<b>You may browse a list of available widgets to decide what to enter here.</b>")
            ->buildComment("Module Name: ", false)->buildTextField("name")
            ->buildComment("Module Subtitle: ", false)->buildTextField("subtitle")
            ->buildComment("Required Userlevel: ", false)->buildTextField("userlevel")
            ->buildComment("<b>You may enter 'member', 'visitor' or leave the above field blank.</b>")
            ->buildComment("Module HTML Code: ")->buildTextArea("html")
            ->buildComment("Module PHP Code: ")->buildTextArea("php")
            ->buildComment("<b>Be cautious with the PHP code for your module, it may or may not work!</b>")
            ->buildComment("Module Order: ", false)->buildTextField("order", 0)
            ->buildComment("Module Status:(enabled or disabled) ", false)->buildTextField("status", "enabled")
            ->buildButton("Create Module", "submit", "submit");
        $document->add($moduleForm);
    }

    public function edit()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $module = $this->getField("module");
        if (!$module) $this->index();
        elseif ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->edited_title);
            $document->addLangvar($this->lang->edited);
        } else {
            $document->setTitle($this->lang->edit_title);
            $document->addLangvar($this->lang->edit);
            $moduleForm = new FormBuilder("editform", $module->getID(), "post");
            $moduleForm->buildComment("Parent Widget: ", false)->buildDropdownList("widget", "ParentWidgetList", $module->getWidget())
                ->buildComment("<b>You may browse a list of available widgets to decide what to enter here.</b>")
                ->buildComment("Module Name: ", false)->buildTextField("name", $module->getName())
                ->buildComment("Module Subtitle: ", false)->buildTextField("subtitle", $module->getSubtitle())
                ->buildComment("Required Userlevel: ", false)->buildTextField("userlevel", $module->getUserLevel())
                ->buildComment("<b>You may enter 'member', 'visitor' or leave the above field blank.</b>")
                ->buildComment("Module HTML Code: ")->buildTextArea("html", $module->getHTML())
                ->buildComment("Module PHP Code: ")->buildTextArea("php", $module->getPHP())
                ->buildComment("<b>Be cautious with the PHP code for your module, it may or may not work!</b>")
                ->buildComment("Module Order: ", false)->buildTextField("order", $module->getOrder())
                ->buildComment("Module Status:(enabled or disabled) ", false)->buildTextField("status", $module->getStatus())
                ->buildButton("Change Module", "submit", "submit");
            $document->add($moduleForm);
        }
    }

    public function delete()
    {
        $document = $this->document;
        $module = $this->getField("module");
        if (!$module) $this->index();
        else {
            $document->setTitle($this->lang->delete_title);
            $document->addLangvar($this->lang->delete);
        }
    }
}
