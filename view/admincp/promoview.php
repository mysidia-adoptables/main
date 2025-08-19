<?php

namespace View\AdminCP;

use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\RadioButton;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\RadioList;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\TableHelper;

class PromoView extends View
{
    public function index()
    {
        parent::index();
        $document = $this->document;
        $helper = new TableHelper();
        $promoTable = new TableBuilder("promocode");
        $promoTable->setAlign(new Align("center", "middle"));
        $promoTable->buildHeaders("ID", "User", "Code", "Reward", "Edit", "Delete");
        $promoTable->setHelper($helper);

        $promocodes = $this->getField("promocodes");
        $iterator = $promocodes->iterator();
        while ($iterator->hasNext()) {
            $promocode = $iterator->next();
            $cells = new LinkedList();
            $cells->add(new TCell($promocode->getID()));
            $cells->add(new TCell($promocode->getUser() ? $promocode->getUsername() : "<i>All Users</i>"));
            $cells->add(new TCell($promocode->getCode()));
            $cells->add(new TCell("{$promocode->getType()}(ID: {$promocode->getReward()})"));
            $cells->add(new TCell($helper->getEditLink($promocode->getID())));
            $cells->add(new TCell($helper->getDeleteLink($promocode->getID())));
            $promoTable->buildRow($cells);
        }
        $document->add($promoTable);

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
            header("Refresh:3; URL='../index'");
            return;
        }

        $document->setTitle($this->lang->add_title);
        $document->addLangvar($this->lang->add);
        $promoForm = new FormBuilder("addform", "add", "post");
        $promoForm->add(new Comment("<br><u>Create A New Promocode:</u><br>", true, "b"));
        $promoForm->add(new Comment("Type:(adoptables or items)"));
        $typesList = new RadioList("type");
        $typesList->add(new RadioButton(" Adoptables", "type", "Adopt"));
        $typesList->add(new RadioButton(" Items", "type", "Item"));
        $typesList->add(new RadioButton(" Pages", "type", "Page"));
        $promoForm->add($typesList);

        $promoForm->add(new Comment("User: (enter user ID, leave blank if you want it to be available to everyone)"));
        $promoForm->buildDropdownList("user", "UsernameList");
        $promoForm->add(new Comment("Code: (the code must have at least one alphabetic char, cannot be all numbers)"));
        $promoForm->add(new TextField("code"));
        $promoForm->add(new Comment("Availability:(how many times can the promocode be used before it expires)"));
        $promoForm->add(new TextField("availability", 1, 6));
        $promoForm->add(new Comment("Start Date:(the specified date promocode can be used afterwards, leave blank if it is readily available)"));
        $promoForm->add(new TextField("fromdate"));
        $promoForm->add(new Comment("Expiration Date:(the specified date promocode expires, leave blank if it does not have a deadline)"));
        $promoForm->add(new TextField("todate"));
        $promoForm->add(new Comment("Note: Date must follow the format (YYYY-mm-dd)"));
        $promoForm->add(new Comment("Reward:(the adoptable or item your member can obtain by entering this promocode. Enter 'Page' if this is a page promocode.)"));
        $promoForm->add(new TextField("reward"));
        $promoForm->add(new Button("Create Promocode", "submit", "submit"));
        $document->add($promoForm);
    }

    public function edit()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $promocode = $this->getField("promocode");
        if (!$promocode) {
            $this->index();
        } elseif ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->edited_title);
            $document->addLangvar($this->lang->edited);
            header("Refresh:3; URL='../edit'");
        } else {
            $promocode = $this->getField("promocode");
            $document->setTitle($this->lang->edit_title);
            $document->addLangvar($this->lang->edit);
            $promoForm = new FormBuilder("editform", $promocode->getID(), "post");
            $promoForm->add(new Comment("<br><u>Editing Promocode:</u><br>", true, "b"));
            $promoForm->add(new Comment("Type:(adoptables or items)"));
            $typesList = new RadioList("type");
            $typesList->add(new RadioButton(" Adoptables", "type", "Adopt"));
            $typesList->add(new RadioButton(" Items", "type", "Item"));
            $typesList->add(new RadioButton(" Pages", "type", "Page"));
            $typesList->check($promocode->getType());
            $promoForm->add($typesList);

            $promoForm->add(new Comment("User: (enter user ID, or leave blank if you want it to be available to everyone)"));
            $promoForm->buildDropdownList("user", "UsernameList", $promocode->getUser());
            $promoForm->add(new Comment("Code: (the code must have at least one alphabetic char, cannot be all numbers)"));
            $promoForm->add(new TextField("code", $promocode->getCode()));
            $promoForm->add(new Comment("Availability:(how many times can the promocode be used before it expires)"));
            $promoForm->add(new TextField("availability", $promocode->getAvailability(), 6));
            $promoForm->add(new Comment("Start Date:(the specified date promocode can be used afterwards, leave blank if it is readily available)"));
            $promoForm->add(new TextField("fromdate", $promocode->getDateFrom("Y-m-d")));
            $promoForm->add(new Comment("Expiration Date:(the specified date promocode expires, leave blank if it does not have a deadline)"));
            $promoForm->add(new TextField("todate", $promocode->getDateTo("Y-m-d")));
            $promoForm->add(new Comment("Note: Date must follow the format (YYYY-mm-dd)"));
            $promoForm->add(new Comment("Reward:(the adoptable or item ID your member can obtain by entering this promocode. Leave blank if this is a page promocode.)"));
            $promoForm->add(new TextField("reward", $promocode->getReward()));
            $promoForm->add(new Button("Modify Promocode", "submit", "submit"));
            $document->add($promoForm);
        }
    }

    public function delete()
    {
        $document = $this->document;
        $promocode = $this->getField("promocode");
        if (!$promocode) {
            $this->index();
        } else {
            $document->setTitle($this->lang->delete_title);
            $document->addLangvar($this->lang->delete);
            header("Refresh:3; URL='../index'");
        }
    }
}
