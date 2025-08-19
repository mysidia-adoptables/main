<?php

namespace View\AdminCP;

use Resource\Collection\LinkedHashMap;
use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Resource\Native\MysString;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\TableHelper;

class LinksView extends View
{
    public function index()
    {
        parent::index();
        $document = $this->document;
        $helper = new TableHelper();
        $linksTable = new TableBuilder("links");
        $linksTable->setAlign(new Align("center", "middle"));
        $linksTable->buildHeaders("ID", "Link Type", "Link Text", "Link Url", "Link Parent", "Link Order", "Edit", "Delete");
        $linksTable->setHelper($helper);

        $links = $this->getField("links");
        $iterator = $links->iterator();
        while ($iterator->hasNext()) {
            $link = $iterator->next();
            $cells = new LinkedList();
            $cells->add(new TCell($link->getID()));
            $cells->add(new TCell($link->getType()));
            $cells->add(new TCell($link->getText()));
            $cells->add(new TCell($link->getURL()));
            $cells->add(new TCell($link->hasParent() ? $link->getParentText() : "N/A"));
            $cells->add(new TCell($link->getOrder()));
            $cells->add(new TCell($helper->getEditLink($link->getID())));
            $cells->add(new TCell($helper->getDeleteLink($link->getID())));
            $linksTable->buildRow($cells);
        }
        $document->add($linksTable);

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
        $linkTypes = new LinkedHashMap();
        $linkTypes->put(new MysString("Navlink"), new MysString("navlink"));
        $linkTypes->put(new MysString("Sidelink"), new MysString("sidelink"));

        $linksForm = new FormBuilder("addform", "add", "post");
        $linksForm->add(new Comment("Link Type: ", false));
        $linksForm->buildRadioList("linktype", $linkTypes, "navlink");
        $linksForm->add(new Comment("Link Text: ", false));
        $linksForm->add(new TextField("linktext"));
        $linksForm->add(new Comment($this->lang->text));
        $linksForm->add(new Comment("Link URL: ", false));
        $linksForm->add(new TextField("linkurl"));
        $linksForm->add(new Comment($this->lang->url));
        $linksForm->add(new Comment("Link parent:", false));
        $linksForm->buildDropdownList("linkparent", "ParentLinkList");
        $linksForm->add(new Comment("Link Order: ", false));
        $linksForm->add(new TextField("linkorder", 0));
        $linksForm->add(new Button("Add Link", "submit", "submit"));
        $document->add($linksForm);
    }

    public function edit()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $link = $this->getField("link");
        if (!$link) {
            $this->index();
        } elseif ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->edited_title);
            $document->addLangvar($this->lang->edited);
        } else {
            $document->setTitle($this->lang->edit_title);
            $document->addLangvar($this->lang->edit);
            $linkTypes = new LinkedHashMap();
            $linkTypes->put(new MysString("Navlink"), new MysString("navlink"));
            $linkTypes->put(new MysString("Sidelink"), new MysString("sidelink"));

            $linksForm = new FormBuilder("editform", $link->getID(), "post");
            $linksForm->add(new Comment("Link Type: ", false));
            $linksForm->buildRadioList("linktype", $linkTypes, $link->getType());
            $linksForm->add(new Comment("Link Text: ", false));
            $linksForm->add(new TextField("linktext", $link->getText()));
            $linksForm->add(new Comment($this->lang->text));
            $linksForm->add(new Comment("Link URL: ", false));
            $linksForm->add(new TextField("linkurl", $link->getURL()));
            $linksForm->add(new Comment($this->lang->url));
            $linksForm->add(new Comment("Link parent:", false));
            $linksForm->buildDropdownList("linkparent", "ParentLinkList", $link->getParent());
            $linksForm->add(new Comment("Link Order: ", false));
            $linksForm->add(new TextField("linkorder", $link->getOrder()));
            $linksForm->add(new Button("Edit Link", "submit", "submit"));
            $document->add($linksForm);
        }
    }

    public function delete()
    {
        $document = $this->document;
        $link = $this->getField("link");
        if (!$link) {
            $this->index();
        } else {
            $document->setTitle($this->lang->delete_title);
            $document->addLangvar($this->lang->delete);
        }
    }
}
