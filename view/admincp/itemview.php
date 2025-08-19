<?php

namespace View\AdminCP;

use Resource\Collection\LinkedList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\CheckBox;
use Resource\GUI\Component\TextArea;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\TableHelper;

class ItemView extends View
{

    public function index()
    {
        parent::index();
        $items = $this->getField("items");
        $document = $this->document;
        $helper = new TableHelper;
        $itemTable = new TableBuilder("item");
        $itemTable->setAlign(new Align("center", "middle"));
        $itemTable->buildHeaders("Image", "Item", "Description", "Function", "Edit", "Delete");
        $itemTable->setHelper($helper);

        $iterator = $items->iterator();
        while ($iterator->hasNext()) {
            $item = $iterator->next();
            $cells = new LinkedList;
            $cells->add(new TCell($item->getImageURL(Model::GUI)));
            $cells->add(new TCell($item->getItemname()));
            $cells->add(new TCell($item->getDescription()));
            $cells->add(new TCell($item->getFunction()));
            $cells->add(new TCell($helper->getEditLink($item->getID())));
            $cells->add(new TCell($helper->getDeleteLink($item->getID())));
            $itemTable->buildRow($cells);
        }
        $document->add($itemTable);

        $pagination = $this->getField("pagination");
        $document->addLangvar($pagination->showPage());
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->added_title);
            $document->addLangvar("{$this->lang->added} {$mysidia->input->post("itemname")}, {$this->lang->added2}");
            return;
        }

        $document->setTitle($this->lang->add_title);
        $document->addLangvar($this->lang->add);
        $itemForm = new FormBuilder("addform", "add", "post");
        $title = new Comment("Create a New Item:");
        $title->setBold();
        $title->setUnderlined();
        $itemForm->add($title);

        $itemForm->add(new Comment("Basic Info: ", true, "b"));
        $itemForm->add(new Comment("Item Name: ", false));
        $itemForm->add(new TextField("itemname"));
        $itemForm->add(new Comment($this->lang->itemname_explain));
        $itemForm->add(new Comment("Item Category: ", false));
        $itemForm->add(new TextField("category"));
        $itemForm->add(new Comment($this->lang->category_explain));
        $itemForm->add(new Comment("Item Description: "));
        $itemForm->add(new TextArea("description", "Here you can enter a description for your item", 4, 45));
        $itemForm->add(new Comment("Item Image: ", false));
        $itemForm->add(new TextField("imageurl"));
        $itemForm->add(new Comment($this->lang->image_explain));
        $itemForm->add(new Comment("Or select an existing image: ", false));
        $itemForm->buildDropdownList("existingimageurl", "ImageList");

        $itemForm->add(new Comment("<hr>Items Functions and Intents:", true, "b"));
        $itemForm->add(new Comment("Choose an item function from the list below, this will determine what happens to this item if used in inventory:"));
        $itemForm->buildDropdownList("function", "ItemFunctionList");
        $itemForm->add(new Comment($this->lang->target_explain));
        $itemForm->add(new TextField("target", "all"));
        $itemForm->add(new Comment("You may also assign a unique value to the item:"));
        $itemForm->add(new TextField("value"));
        $itemForm->add(new Comment($this->lang->value_explain));
        $itemForm->add(new Comment("<hr>Item Shop Settings:", true, "b"));
        $itemForm->add(new Comment("Item Shop: ", false));
        $itemForm->buildDropdownList("shop", "ItemShopList");
        $itemForm->add(new Comment($this->lang->shop_explain));
        $itemForm->add(new Comment("Item Price: ", false));
        $itemForm->add(new TextField("price"));
        $itemForm->add(new Comment($this->lang->price_explain));

        $itemForm->add(new Comment("Miscellaneous Settings:", true, "b"));
        $itemForm->add(new Comment("Chance for item to take effect", false));
        $itemForm->add(new TextField("chance", 100));
        $itemForm->add(new Comment($this->lang->chance_explain));
        $itemForm->add(new Comment("Upper Limit to Purchasable Amount: ", false));
        $itemForm->add(new TextField("cap", 99));
        $itemForm->add(new Comment($this->lang->limit_explain));
        $itemForm->add(new CheckBox("<b>The item can be traded.</b>", "tradable", "yes"));
        $itemForm->add(new CheckBox("<b>The item can be consumed(thus its quantity decreases by 1 each time used)</b>", "consumable", "yes"));
        $itemForm->add(new Button("Create Item", "submit", "submit"));
        $document->add($itemForm);
    }

    public function edit()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $item = $this->getField("item");
        if (!$item) $this->index();
        elseif ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->edited_title);
            $document->addLangvar("{$this->lang->edited} {$mysidia->input->post("itemname")}, {$this->lang->edited2}");
            return;
        } else {
            $document->setTitle($this->lang->edit_title);
            $document->addLangvar($this->lang->edit);
            $itemForm = new FormBuilder("editform", $item->getID(), "post");
            $title = new Comment("Edit an Item:");
            $title->setBold();
            $title->setUnderlined();
            $itemForm->add($title);

            $itemForm->add(new Comment("Basic Info: ", true, "b"));
            $itemForm->add(new Comment("Item Name: ", false));
            $itemForm->add(new TextField("itemname", $item->getItemname()));
            $itemForm->add(new Comment($this->lang->itemname_explain));
            $itemForm->add(new Comment("Item Category: ", false));
            $itemForm->add(new TextField("category", $item->getCategory()));
            $itemForm->add(new Comment("Item Description: "));
            $itemForm->add(new TextArea("description", $item->getDescription(), 4, 45));
            $itemForm->add(new Comment("Item Image: ", false));
            $itemForm->add(new TextField("imageurl", $item->getImageURL()));
            $itemForm->add(new Comment($this->lang->image_explain));
            $itemForm->add(new Comment("Or select an existing image: ", false));
            $itemForm->buildDropdownList("existingimageurl", "ImageList", $item->getImageURL());
            $itemForm->add(new Comment("<hr>Items Functions and Intents:", true, "b"));
            $itemForm->add(new Comment("Choose an item function from the list below, this will determine what happens to this item if used in inventory:"));
            $itemForm->buildDropdownList("function", "ItemFunctionList", $item->getFunction());

            $itemForm->add(new Comment($this->lang->target_explain));
            $itemForm->add(new TextField("target", $item->getTarget()));
            $itemForm->add(new Comment("You may also assign a unique value to the item:", false));
            $itemForm->add(new TextField("value", $item->getValue()));
            $itemForm->add(new Comment($this->lang->value_explain));
            $itemForm->add(new Comment("<hr>Item Shop Settings:", true, "b"));
            $itemForm->add(new Comment("Item Shop: ", false));
            $itemForm->buildDropdownList("shop", "ItemShopList", $item->getShop());
            $itemForm->add(new Comment($this->lang->shop_explain));
            $itemForm->add(new Comment("Item Price: ", false));
            $itemForm->add(new TextField("price", $item->getPrice()));
            $itemForm->add(new Comment($this->lang->price_explain));

            $itemForm->add(new Comment("Miscellaneous Settings:", true, "b"));
            $itemForm->add(new Comment("Chance for item to take effect", false));
            $itemForm->add(new TextField("chance", $item->getChance()));
            $itemForm->add(new Comment($this->lang->chance_explain));
            $itemForm->add(new Comment("Upper Limit to Purchasable Amount: ", false));
            $itemForm->add(new TextField("cap", $item->getCap()));
            $itemForm->add(new Comment($this->lang->limit_explain));
            $itemForm->add(new CheckBox("<b>The item can be traded.</b>", "tradable", "yes", $item->isTradable() ? "yes" : "no"));
            $itemForm->add(new CheckBox("<b>The item can be consumed(thus its quantity decreases by 1 each time used)</b>", "consumable", "yes", $item->isConsumable() ? "yes" : "no"));
            $itemForm->add(new Button("Edit Item", "submit", "submit"));
            $document->add($itemForm);
        }
    }

    public function delete()
    {
        $document = $this->document;
        $item = $this->getField("item");
        if (!$item) $this->index();
        else {
            $document->setTitle($this->lang->delete_title);
            $document->addLangvar($this->lang->delete);
        }
    }

    public function functions()
    {
        $document = $this->document;
        $document->setTitle($this->lang->functions_title);
        $document->addLangvar($this->lang->functions);

        $itemFunctions = $this->getField("itemFunctions");
        $functionsTable = new TableBuilder("functions");
        $functionsTable->setAlign(new Align("center", "middle"));
        $functionsTable->buildHeaders("ID", "Function", "Intent", "Description");
        $functionsTable->setHelper(new TableHelper);

        $iterator = $itemFunctions->iterator();
        while ($iterator->hasNext()) {
            $itemFunction = $iterator->next();
            $cells = new LinkedList;
            $cells->add(new TCell($itemFunction->getID()));
            $cells->add(new TCell($itemFunction->getFunction()));
            $cells->add(new TCell($itemFunction->getIntent()));
            $cells->add(new TCell($itemFunction->getDescription()));
            $functionsTable->buildRow($cells);
        }
        $document->add($functionsTable);
    }
}
