<?php

namespace View\Main;
use Resource\Collection\LinkedList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\Option;
use Resource\GUI\Component\PasswordField;
use Resource\GUI\Container\DropdownList;
use Resource\GUI\Container\Form;
use Resource\GUI\Container\TCell;
use Resource\GUI\Element\Align;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\ItemTableHelper;

class InventoryView extends View{
	
	public function index(){
		$document = $this->document;
		$document->setTitle($this->lang->inventory);
		
		$inventory = $this->getField("inventory");
	    $inventoryTable = new TableBuilder("inventory");
	    $inventoryTable->setAlign(new Align("center", "middle"));
	    $inventoryTable->buildHeaders("Image", "Name", "Description", "Quantity", "Use", "Sell", "Toss");	
	    $inventoryTable->setHelper(new ItemTableHelper);
	  
	    $inventoryIterator = $inventory->iterator();
		while($inventoryIterator->hasNext()){
	        $item = $inventoryIterator->next();
		    $cells = new LinkedList;
		    $cells->add(new TCell($item->getImageURL(Model::GUI)));
		    $cells->add(new TCell($item->getItemname()));
		    $cells->add(new TCell($item->getDescription()));
		    $cells->add(new TCell($item->getQuantity()));
		    $cells->add(new TCell($inventoryTable->getHelper()->getUseForm($item)));
		    $cells->add(new TCell($inventoryTable->getHelper()->getSellForm($item)));
		    $cells->add(new TCell($inventoryTable->getHelper()->getTossForm($item)));
		    $inventoryTable->buildRow($cells);		
		}
 	    $document->add($inventoryTable);
	}
			
	public function uses(){
		$mysidia = Registry::get("mysidia");
		$document = $this->document;	
		if($mysidia->input->post("aid")){
		    $message = (string)$this->getField("message");
		    $document->setTitle($this->lang->global_action_complete);
            $document->addLangvar($message);
            return;		
		}
		
		$petMap = $this->getField("petMap");
		$document->setTitle($this->lang->select_title);
        $document->addLangvar($this->lang->select);		
		$chooseFrom = new Form("chooseform", "uses", "post");
		
		$adoptable = new DropdownList("aid");
		$adoptable->add(new Option("None Selected", "none"));
        if($petMap->size() > 0){
            $iterator = $petMap->iterator();
            while($iterator->hasNext()){
                $adopt = $iterator->nextEntry();
                $adoptable->add(new Option($adopt->getValue(), $adopt->getKey()));
            }
        }		
		$chooseFrom->add($adoptable);
		
		$chooseFrom->add(new PasswordField("hidden", "item", $mysidia->input->post("item")));
		$chooseFrom->add(new PasswordField("hidden", "validation", "valid"));
		$chooseFrom->add(new Button("Choose this Adopt", "submit", "submit"));
        $document->add($chooseFrom);
	}
	
	public function sell(){
		$mysidia = Registry::get("mysidia");
		$document = $this->document;
        $item = $this->getField("item");
		$document->setTitle($this->lang->global_transaction_complete);
		$document->addLangvar("{$this->lang->sell}{$mysidia->input->post("quantity")} {$item->getItemname()} {$this->lang->sell2}");
	}
	
	public function toss(){
	    $mysidia = Registry::get("mysidia");
		$document = $this->document;
        $item = $this->getField("item");
        $confirm = $this->getField("confirm");
		if($confirm){
			$document->setTitle($this->lang->global_action_complete);
	        $document->addLangvar("{$this->lang->toss}{$item->getItemname()}{$this->lang->toss2}");
	        return;
		}
	
		$document->setTitle($this->lang->toss_confirm);
		$document->addLangvar("{$this->lang->toss_warning}{$item->getItemname()}?<br>{$this->lang->toss_warning2}");	
		$confirmForm = new FormBuilder("confirmform", "toss/confirm", "post");
		$confirmForm->buildPasswordField("hidden", "action", "toss")
		            ->buildPasswordField("hidden", "item", $mysidia->input->post("item"))
					->buildButton("Please Toss", "confirm", "confirm");
		$document->add($confirmForm);			
	}
}