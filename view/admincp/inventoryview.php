<?php

namespace View\AdminCP;
use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\TableHelper;

class InventoryView extends View{

	public function index(){
	    parent::index();
		$ownedItems = $this->getField("ownedItems");
        $document = $this->document;
		
        $helper = new TableHelper;
		$inventoryTable = new TableBuilder("inventory");
		$inventoryTable->setAlign(new Align("center", "middle"));
		$inventoryTable->buildHeaders("ID", "Item", "Owner", "Quantity", "Edit", "Delete");
		$inventoryTable->setHelper($helper);
                
        $iterator = $ownedItems->iterator();
        while($iterator->hasNext()){
            $ownedItems = $iterator->next();
		    $cells = new LinkedList;
		    $cells->add(new TCell($ownedItems->getInventoryID()));
		    $cells->add(new TCell($ownedItems->getItemname()));
		    $cells->add(new TCell($ownedItems->getOwnerName()));
            $cells->add(new TCell($ownedItems->getQuantity()));
		    $cells->add(new TCell($helper->getEditLink($ownedItems->getID())));
		    $cells->add(new TCell($helper->getDeleteLink($ownedItems->getID())));
		    $inventoryTable->buildRow($cells);              
        }
        $document->add($inventoryTable);	

        $pagination = $this->getField("pagination");
		$document->addLangvar($pagination->showPage());	
	}
	
	public function add(){
	    $mysidia = Registry::get("mysidia");
		$document = $this->document;	
	    if($mysidia->input->post("submit")){
		    $document->setTitle($this->lang->added_title);
			$document->addLangvar($this->lang->added);
			return;
		}
		
		$document->setTitle($this->lang->add_title);
		$document->addLangvar($this->lang->add);
		$inventoryForm = new FormBuilder("additem", "add", "post");
		$title = new Comment("Give an Item to User:");
		$title->setBold();
		$title->setUnderlined();
		$inventoryForm->add($title);
		
		$inventoryForm->add(new Comment("Item Type: ", FALSE));
		$inventoryForm->buildDropdownList("item", "ItemNameList");
		$inventoryForm->add(new Comment("Item Owner: ", FALSE));
		$inventoryForm->buildDropdownList("owner", "UsernameList");
		$inventoryForm->add(new Comment("Item Quantity: ", FALSE));
		$inventoryForm->add(new TextField("quantity", 1, 6));
		$inventoryForm->add(new Button("Give Item", "submit", "submit"));
		$document->add($inventoryForm);
	}
	
	public function edit(){
	    $mysidia = Registry::get("mysidia");
		$document = $this->document;	
		$ownedItem = $this->getField("ownedItem");
	    if(!$ownedItem) $this->index();
		elseif($mysidia->input->post("submit")){
		    $document->setTitle($this->lang->edited_title);
			$document->addLangvar($this->lang->edited);
		}
		else{
		    $document->setTitle($this->lang->edit_title);
			$document->addLangvar($this->lang->edit);
		    $title = new Comment("Edit User's Items:");
		    $title->setBold();
		    $title->setUnderlined();
			
			$inventoryForm = new FormBuilder("edititem", $ownedItem->getID(), "post");			
		    $inventoryForm->add($title);		
		    $inventoryForm->add(new Comment("Item Name: ", FALSE));
		    $inventoryForm->buildDropdownList("item", "ItemNameList", $ownedItem->getItemID());
		    $inventoryForm->add(new Comment("Item Owner: ", FALSE));
            $inventoryForm->buildDropdownList("owner", "UsernameList", $ownedItem->getOwner());
		    $inventoryForm->add(new Comment("Item Quantity: ", FALSE));
		    $inventoryForm->add(new TextField("quantity", $ownedItem->getQuantity(), 6));
		    $inventoryForm->add(new Button("Edit Item", "submit", "submit"));
		    $document->add($inventoryForm); 
	    }
	}
	
	public function delete(){
		$document = $this->document;
        $ownedItem = $this->getField("ownedItem");
        if(!$ownedItem) $this->index();
		else{
		    $document->setTitle($this->lang->delete_title);
		    $document->addLangvar($this->lang->delete);
        }
    }
}	