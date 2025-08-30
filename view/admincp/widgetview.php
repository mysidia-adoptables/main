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

class WidgetView extends View
{
    public function index()
    {
        parent::index();
        $document = $this->document;
        $helper = new TableHelper();
        $widgetTable = new TableBuilder("widgets");
        $widgetTable->setAlign(new Align("center", "middle"));
        $widgetTable->buildHeaders("ID", "Widget", "Controller", "Order", "Status", "Edit", "Delete");
        $widgetTable->setHelper($helper);

        $widgets = $this->getField("widgets");
        $iterator = $widgets->iterator();
        while ($iterator->hasNext()) {
            $widget = $iterator->next();
            $cells = new LinkedList();
            $cells->add(new TCell($widget->getID()));
            $cells->add(new TCell($widget->getName()));
            $cells->add(new TCell($widget->getController()));
            $cells->add(new TCell($widget->getOrder()));
            $cells->add(new TCell($widget->getStatus()));
            $cells->add(new TCell($helper->getEditLink($widget->getID())));
            $cells->add(new TCell($helper->getDeleteLink($widget->getID())));
            $widgetTable->buildRow($cells);
        }
        $document->add($widgetTable);

        $pagination = $this->getField("pagination");
        $document->addLangvar($pagination->showPage());
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->added_title);
            $document->addLangvar($this->lang->added);
            return;
        }

        $document->setTitle($this->lang->add_title);
        $document->addLangvar($this->lang->add);
        $widgetForm = new FormBuilder("addform", "add", "post");
        $widgetForm->buildComment("Widget Name: ", false)->buildTextField("name")
                   ->buildComment("Controller Level: ", false)->buildTextField("controllers")
                   ->buildComment("<b>You may enter 'main', 'admincp' or leave the above field blank.</b>")
                   ->buildComment("Widget Order: ", false)->buildTextField("order", 0)
                   ->buildComment("Widget Status:(enabled or disabled) ", false)->buildTextField("status", "enabled")
                   ->buildButton("Create Widget", "submit", "submit");
        $document->add($widgetForm);
    }

    public function edit()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $widget = $this->getField("widget");
        if (!$widget) {
            $this->index();
        } elseif ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->edited_title);
            $document->addLangvar($this->lang->edited);
        } else {
            $document->setTitle($this->lang->edit_title);
            $document->addLangvar($this->lang->edit);
            $widgetForm = new FormBuilder("editform", $widget->getID(), "post");
            $widgetForm->buildComment("Widget Name: ", false)->buildTextField("name", $widget->getName())
                       ->buildComment("Controller Level: ", false)->buildTextField("controllers", $widget->getController())
                       ->buildComment("<b>You may enter 'main', 'admincp' or leave the above field blank.</b>")
                       ->buildComment("Widget Order: ", false)->buildTextField("order", $widget->getOrder())
                       ->buildComment("Widget Status:(enabled or disabled) ", false)->buildTextField("status", $widget->getStatus())
                       ->buildButton("Change Widget", "submit", "submit");
            $document->add($widgetForm);
        }
    }

    public function delete()
    {
        $document = $this->document;
        $widget = $this->getField("widget");
        if (!$widget) {
            $this->index();
        } else {
            $document->setTitle($this->lang->delete_title);
            $document->addLangvar($this->lang->delete);
        }
    }
}
