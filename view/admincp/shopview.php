<?php

namespace View\AdminCP;

use Resource\Collection\LinkedList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\Option;
use Resource\GUI\Component\RadioButton;
use Resource\GUI\Component\TextArea;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\DropdownList;
use Resource\GUI\Container\RadioList;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\ShopTableHelper;

class ShopView extends View
{
    public function index()
    {
        parent::index();
        $document = $this->document;
        $shops = $this->getField("shops");
        $helper = new ShopTableHelper();
        $shopTable = new TableBuilder("shop");
        $shopTable->setAlign(new Align("center", "middle"));
        $shopTable->buildHeaders("Image", "Shop", "Description", "Status", "Edit", "Delete");
        $shopTable->setHelper($helper);

        $iterator = $shops->iterator();
        while ($iterator->hasNext()) {
            $shop = $iterator->next();
            $cells = new LinkedList();
            $cells->add(new TCell($shop->getImageURL(Model::GUI)));
            $cells->add(new TCell($shop->getShopname()));
            $cells->add(new TCell($shop->getDescription()));
            $cells->add(new TCell($shop->getStatus()));
            $cells->add(new TCell($helper->getEditLink($shop->getID())));
            $cells->add(new TCell($helper->getDeleteLink($shop->getID())));
            $shopTable->buildRow($cells);
        }
        $document->add($shopTable);

        $pagination = $this->getField("pagination");
        $document->addLangvar($pagination->showPage());
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->added_title);
            $document->addLangvar("{$this->lang->added} {$mysidia->input->post("shopname")},{$this->lang->added2}");
            return;
        }

        $document->setTitle($this->lang->add_title);
        $document->addLangvar($this->lang->add);
        $shopForm = new FormBuilder("addform", "add", "post");
        $shopForm->add(new Comment("<br><u>Create A New Shop:</u>", true, "b"));
        $shopForm->add(new Comment("Basic Settings", true, "b"));
        $shopForm->add(new Comment("Shop Name: ", false));
        $shopForm->add(new TextField("shopname"));
        $shopForm->add(new Comment($this->lang->shopname_explain));
        $shopForm->add(new Comment("Shop Category: ", false));
        $shopForm->add(new TextField("category"));
        $shopForm->add(new Comment($this->lang->category_explain));

        $shopForm->add(new Comment("Shop Type: ", false));
        $typesList = new DropdownList("shoptype");
        $typesList->add(new Option("Itemshop", "itemshop"));
        $typesList->add(new Option("Adoptshop", "adoptshop"));
        $shopForm->add($typesList);
        $shopForm->add(new Comment($this->lang->shoptype_explain));
        $shopForm->add(new Comment("Shop Description:"));
        $shopForm->add(new TextArea("description", "Here you can enter a description for your shop", 4, 50));
        $shopForm->add(new Comment("Shop Image: ", false));
        $shopForm->add(new TextField("imageurl"));
        $shopForm->add(new Comment($this->lang->imageurl_explain));
        $shopForm->add(new Comment("OR select an existing image: ", false));
        $shopForm->buildDropdownList("existingimageurl", "ImageList");

        $shopForm->add(new Comment("<hr>Miscellaneous Settings:", true, "b"));
        $shopForm->add(new Comment("Shop Status: ", false));
        $shopStatus = new RadioList("status");
        $shopStatus->add(new RadioButton("Open", "status", "open"));
        $shopStatus->add(new RadioButton("Closed", "status", "closed"));
        $shopStatus->add(new RadioButton("Hidden", "status", "invisible"));
        $shopStatus->check("open");
        $shopForm->add($shopStatus);
        $shopForm->add(new Comment("Restriction: ", false));
        $shopForm->add(new TextField("restriction"));
        $shopForm->add(new Comment($this->lang->restrict_explain));
        $shopForm->add(new Comment("Sales Tax: ", false));
        $shopForm->add(new TextField("salestax", 0));
        $shopForm->add(new Comment($this->lang->salestax_explain));
        $shopForm->add(new Button("Create Shop", "submit", "submit"));
        $document->add($shopForm);
    }

    public function edit()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $shop = $this->getField("shop");
        if (!$shop) {
            $this->index();
        } elseif ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->edited_title);
            $document->addLangvar("{$this->lang->edited} {$mysidia->input->post("shopname")},{$this->lang->edited2}");
            return;
        } else {
            $shop = $this->getField("shop");
            $document->setTitle($this->lang->edit_title);
            $document->addLangvar($this->lang->edit);
            $shopForm = new FormBuilder("editform", $shop->getID(), "post");
            $shopForm->add(new Comment("<br><u>Edit an existing Shop:</u>", true, "b"));
            $shopForm->add(new Comment("Basic Settings", true, "b"));
            $shopForm->add(new Comment("Shop Name: ", false));
            $shopForm->add(new TextField("shopname", $shop->getShopname()));
            $shopForm->add(new Comment($this->lang->shopname_explain));
            $shopForm->add(new Comment("Shop Category: ", false));
            $shopForm->add(new TextField("category", $shop->getCategory()));
            $shopForm->add(new Comment($this->lang->category_explain));
            $shopForm->add(new Comment("Shop Description:"));
            $shopForm->add(new TextArea("description", $shop->getDescription(), 4, 50));
            $shopForm->add(new Comment("Shop Image: ", false));
            $shopForm->add(new TextField("imageurl", $shop->getImageURL()));
            $shopForm->add(new Comment($this->lang->imageurl_explain));
            $shopForm->add(new Comment("OR select an existing image: ", false));
            $shopForm->buildDropdownList("existingimageurl", "ImageList", $shop->getImageURL());

            $shopForm->add(new Comment("<hr>Miscellaneous Settings:", true, "b"));
            $shopForm->add(new Comment("Shop Status: ", false));
            $shopStatus = new RadioList("status");
            $shopStatus->add(new RadioButton("Open", "status", "open"));
            $shopStatus->add(new RadioButton("Closed", "status", "closed"));
            $shopStatus->add(new RadioButton("Hidden", "status", "invisible"));
            $shopStatus->check($shop->getStatus());
            $shopForm->add($shopStatus);
            $shopForm->add(new Comment("Restriction: ", false));
            $shopForm->add(new TextField("restriction", $shop->getRestriction()));
            $shopForm->add(new Comment($this->lang->restrict_explain));
            $shopForm->add(new Comment("Sales Tax: ", false));
            $shopForm->add(new TextField("salestax", $shop->getSalesTax()));
            $shopForm->add(new Comment($this->lang->salestax_explain));
            $shopForm->add(new Button("Edit Shop", "submit", "submit"));
            $document->add($shopForm);
        }
    }

    public function delete()
    {
        $document = $this->document;
        $shop = $this->getField("shop");
        if (!$shop) {
            $this->index();
        } else {
            $document->setTitle($this->lang->delete_title);
            $document->addLangvar($this->lang->delete);
        }
    }
}
