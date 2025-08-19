<?php

namespace View\AdminCP;

use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Legend;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\FieldSet;
use Resource\GUI\Container\Form;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Service\Builder\CKEditorBuilder;
use Service\Builder\FieldSetBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\TableHelper;

class ContentView extends View
{

    public function index()
    {
        parent::index();
        $document = $this->document;
        $contents = $this->getField("contents");
        $helper = new TableHelper;
        $contentsTable = new TableBuilder("ads");
        $contentsTable->setAlign(new Align("center", "middle"));
        $contentsTable->buildHeaders("Page ID", "Page URL", "Title", "Edit", "Delete");
        $contentsTable->setHelper($helper);

        $iterator = $contents->iterator();
        while ($iterator->hasNext()) {
            $content = $iterator->next();
            $cells = new LinkedList;
            $cells->add(new TCell($content->getID()));
            $cells->add(new TCell(($content->getID() > 2) ? "pages/view/{$content->getPage()}" : $content->getPage()));
            $cells->add(new TCell($content->getTitle()));
            $cells->add(new TCell($helper->getEditLink($content->getID())));
            $cells->add(new TCell($helper->getDeleteLink($content->getID())));
            $contentsTable->buildRow($cells);
        }
        $document->add($contentsTable);

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
        $pageForm = new Form("addpage", "add", "post");
        $editor = new CKEditorBuilder("full");

        $basic = new FieldSet;
        $basic->add(new Legend("Basic Info"));
        $basic->add(new Comment("Page URL: ", false));
        $basic->add(new TextField("page"));
        $basic->add(new Comment("{$this->lang->explain} {$mysidia->path->getAbsolute()}{$this->lang->explain2}"));
        $basic->add(new Comment("Page Title: ", false));
        $basic->add(new TextField("title"));
        $basic->add(new Comment("Page Content: ", false));
        $basic->add($editor->buildEditor("content", "Enter your content here."));
        $pageForm->add($basic);

        $accessibility = new FieldSetBuilder("Accessibility Info");
        $accessibility->add(new Comment($this->lang->accessibility));
        $accessibility->add(new Comment("Code: (The promocode required to access this page) ", false));
        $accessibility->add(new TextField("promocode"));
        $accessibility->add(new Comment("Item: (The item necessary to access this page) ", false));
        $accessibility->buildDropdownList("item", "ItemNameList");
        $accessibility->add(new Comment("Date: (The time available to access this page, format: Y-m-d) ", false));
        $accessibility->add(new TextField("time"));
        $accessibility->add(new Comment("Group: (The usergroup allowed to access this page) ", false));
        $accessibility->buildDropdownList("group", "UsergroupList");
        $accessibility->buildButton("Create New Page", "submit", "submit");
        $pageForm->add($accessibility);
        $document->add($pageForm);
    }

    public function edit()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $content = $this->getField("content");
        if (!$content) $this->index();
        elseif (!$mysidia->input->post("submit")) {
            $document->setTitle($this->lang->edit_title);
            $document->addLangvar($this->lang->edit);
            $pageForm = new Form("editpage", $content->getID(), "post");
            $editor = new CKEditorBuilder("full");

            $basic = new FieldSet;
            $basic->add(new Legend("Basic Info"));
            $basic->add(new Comment("{$this->lang->editing} {$content->getPage()}"));
            $basic->add(new Comment("Page Title: ", false));
            $basic->add(new TextField("title", $content->getTitle()));
            $basic->add(new Comment("Page Content: ", false));
            $basic->add($editor->buildEditor("content", $mysidia->format($content->getContent())));
            $pageForm->add($basic);

            $accessibility = new FieldSetBuilder("Accessibility Info");
            $accessibility->add(new Comment($this->lang->accessibility));
            $accessibility->add(new Comment("Code: (The promocode required to access this page) ", false));
            $accessibility->add(new TextField("promocode", $content->getCode()));
            $accessibility->add(new Comment("Item: (The item necessary to access this page) ", false));
            $accessibility->buildDropdownList("item", "ItemNameList", $content->getItem());
            $accessibility->add(new Comment("Date: (The time available to access this page, format: Y-m-d) ", false));
            $accessibility->add(new TextField("time", $content->getTime("Y-m-d")));
            $accessibility->add(new Comment("Group: (The usergroup allowed to access this page) ", false));
            $accessibility->buildDropdownList("group", "UsergroupList", $content->getGroup());
            $accessibility->buildButton("Edit this Page", "submit", "submit");
            $pageForm->add($accessibility);
            $document->add($pageForm);
        } else {
            $document->setTitle($this->lang->edited_title);
            $document->addLangvar($this->lang->edited);
        }
    }

    public function delete()
    {
        $document = $this->document;
        $content = $this->getField("content");
        if (!$content) $this->index();
        else {
            $document->setTitle($this->lang->delete_title);
            $document->addLangvar($this->lang->delete);
        }
    }
}
