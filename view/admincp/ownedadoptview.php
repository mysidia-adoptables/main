<?php

namespace View\AdminCP;

use Resource\Collection\LinkedHashMap;
use Resource\Collection\LinkedList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Container\TCell;
use Resource\GUI\Element\Align;
use Resource\Native\MysString;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\AdoptTableHelper;

class OwnedadoptView extends View
{
    public function index()
    {
        parent::index();
        $ownedAdopts = $this->getField("ownedAdopts");
        $document = $this->document;

        $helper = new AdoptTableHelper();
        $ownedAdoptTable = new TableBuilder("ownedadopt");
        $ownedAdoptTable->setAlign(new Align("center", "middle"));
        $ownedAdoptTable->buildHeaders("ID", "Type", "Name", "Owner", "Gender", "Edit", "Delete");
        $ownedAdoptTable->setHelper($helper);

        $iterator = $ownedAdopts->iterator();
        while ($iterator->hasNext()) {
            $ownedAdopt = $iterator->next();
            $cells = new LinkedList();
            $cells->add(new TCell($ownedAdopt->getID()));
            $cells->add(new TCell($ownedAdopt->getType()));
            $cells->add(new TCell($ownedAdopt->getName()));
            $cells->add(new TCell($ownedAdopt->getOwner(Model::MODEL)->getUsername()));
            $cells->add(new TCell($helper->getGenderImage($ownedAdopt->getGender())));
            $cells->add(new TCell($helper->getEditLink($ownedAdopt->getID())));
            $cells->add(new TCell($helper->getDeleteLink($ownedAdopt->getID())));
            $ownedAdoptTable->buildRow($cells);
        }
        $document->add($ownedAdoptTable);
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
        $genders = new LinkedHashMap();
        $genders->put(new MysString("female"), new MysString("f"));
        $genders->put(new MysString("male"), new MysString("m"));

        $ownedAdoptForm = new FormBuilder("addform", "add", "post");
        $ownedAdoptForm->buildComment("<u><strong>Create A New Adoptable For a User:</strong></u>")
                       ->buildComment("Adoptable Type: ", false)->buildDropdownList("adopt", "AdoptTypeList")
                       ->buildComment("Adoptable Name: ", false)->buildTextField("name")
                       ->buildComment("Adoptable Owner: ", false)->buildDropdownList("owner", "UsernameList")
                       ->buildComment("Adoptable Clicks: ", false)->buildTextField("clicks")
                       ->buildComment("Adoptable Level: ", false)->buildTextField("level")
                       ->buildComment("Adoptable Alternate ID: ", false)->buildTextField("alternate")
                       ->buildComment("Adoptable Gender: ", false)
                       ->buildRadioList("gender", $genders)
                       ->buildButton("Give it to User", "submit", "submit");
        $document->add($ownedAdoptForm);
    }

    public function edit()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $ownedAdopt = $this->getField("ownedAdopt");
        if (!$ownedAdopt) {
            $this->index();
        } elseif (!$mysidia->input->post("submit")) {
            $document->setTitle("{$this->lang->edit_title} {$ownedAdopt->getName()}({$ownedAdopt->getType()}))");
            $document->addLangvar($this->lang->edit);
            $genders = new LinkedHashMap();
            $genders->put(new MysString("female"), new MysString("f"));
            $genders->put(new MysString("male"), new MysString("m"));

            $ownedAdoptForm = new FormBuilder("editform", $ownedAdopt->getID(), "post");
            $ownedAdoptForm->buildComment("<u><strong>Edit User's Owned Adoptable:</strong></u>")
                           ->buildComment("Adoptable Type: ", false)->buildDropdownList("adopt", "AdoptTypeList", $ownedAdopt->getSpeciesID())
                           ->buildComment("Adoptable Name: ", false)->buildTextField("name", $ownedAdopt->getName())
                           ->buildComment("Adoptable Owner: ", false)->buildDropdownList("owner", "UsernameList", $ownedAdopt->getOwner())
                           ->buildComment("Adoptable Clicks: ", false)->buildTextField("clicks", $ownedAdopt->getTotalClicks())
                           ->buildComment("Adoptable Level: ", false)->buildTextField("level", $ownedAdopt->getCurrentLevel())
                           ->buildComment("Adoptable Alternate ID: ", false)->buildTextField("alternate", $ownedAdopt->getAlternate())
                           ->buildComment("Adoptable Gender: ", false)
                           ->buildRadioList("gender", $genders, $ownedAdopt->getGender())
                           ->buildButton("Edit this Adoptable", "submit", "submit");
            $document->add($ownedAdoptForm);
        } else {
            $document->setTitle($this->lang->edited_title);
            $document->addLangvar($this->lang->edited);
        }
    }

    public function delete()
    {
        $document = $this->document;
        $ownedAdopt = $this->getField("ownedAdopt");
        if (!$ownedAdopt) {
            $this->index();
        } else {
            $document->setTitle($this->lang->delete_title);
            $document->addLangvar($this->lang->delete);
            header("Refresh:3; URL='../index'");
        }
    }
}
