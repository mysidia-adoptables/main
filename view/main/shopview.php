<?php

namespace View\Main;
use Model\ViewModel\AdoptShopViewModel;
use Model\ViewModel\ItemShopViewModel;
use Resource\Collection\LinkedList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\Option;
use Resource\GUI\Container\DropdownList;
use Resource\GUI\Container\Form;
use Resource\GUI\Container\TCell;
use Resource\GUI\Element\Align;
use Service\Builder\TableBuilder;
use Service\Helper\ShopTableHelper;

class ShopView extends View{
	
	public function index(){
		$document = $this->document;
	    $document->setTitle($this->lang->access);
		
		$typeForm = new Form("shoptypes", "shop", "post");
		$typeSelection = new DropdownList("shoptype");
		$typeSelection->add(new Option("Itemshop", "itemshop"));
		$typeSelection->add(new Option("Adoptshop", "adoptshop"));
		$typeForm->add($typeSelection);
		$typeForm->add(new Button("Go", "submit", "submit"));
		$document->add($typeForm);
		
		$shopList = $this->getField("shopList"); 
	    $document->addLangvar($this->lang->select);
        $helper = new ShopTableHelper;
		$shopTable = new TableBuilder("shoplist");
		$shopTable->setAlign(new Align("center", "middle"));
		$shopTable->buildHeaders("Image", "Category", "Name", "Description", "Sales Tax", "Enter");	
	    $shopTable->setHelper($helper);		 
        
		$iterator = $shopList->iterator();
		while($iterator->hasNext()){
            $shop = $iterator->next();
			$cells = new LinkedList;
			$cells->add(new TCell($shop->getImageURL(Model::GUI)));
			$cells->add(new TCell($shop->getCategory()));
			$cells->add(new TCell($shop->getShopname()));
			$cells->add(new TCell($shop->getDescription()));
			$cells->add(new TCell($helper->getSalestax($shop->getSalesTax())));
			$cells->add(new TCell($helper->getShopStatus($shop)));
			$shopTable->buildRow($cells);
		}
        $document->add($shopTable);  
	}
	
	public function browse(){
		$document = $this->document;			        
        $shop = $this->getField("shop");
		$document->setTitle($this->lang->welcome . $shop->getShopname());
        if($shop->getShoptype() == "adoptshop"){ 
            $select = $this->lang->select_adopt;
            $shopViewModel = new AdoptShopViewModel($shop);
        }
        else{
            $select = $this->lang->select_item;
            $shopViewModel = new ItemShopViewModel($shop);
            
        }
        $document->addLangvar($select);
        if($shopViewModel->isEmpty()) $document->addLangvar($this->lang->empty);
        else $document->add($shopViewModel->display());
	}
	
	public function purchase(){
        $mysidia = Registry::get("mysidia");
		$cost = $this->getField("cost");
		$document = $this->document;		
		
	    if($mysidia->input->post("shoptype") == "itemshop"){
		    $document->setTitle($this->lang->global_transaction_complete);
	        $document->addLangvar("{$this->lang->purchase} {$mysidia->input->post("quantity")} {$mysidia->input->post("itemname")} {$this->lang->cost}{$cost->getValue()} {$mysidia->settings->cost}");
		}
		elseif($mysidia->input->post("shoptype") == "adoptshop"){
   			$document->setTitle($this->lang->global_transaction_complete);
	        $document->addLangvar("{$this->lang->purchase} adoptable {$mysidia->input->post("adopttype")} {$this->lang->cost}{$cost->getValue()} {$mysidia->settings->cost}");	  
		}
		else return;
	}
}