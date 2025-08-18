<?php

namespace Model\ViewModel;
use Model\DomainModel\Item;
use Resource\Collection\LinkedList;
use Resource\Core\Model;
use Resource\GUI\Container\TCell;
use Resource\GUI\Element\Align;
use Service\Builder\TableBuilder;
use Service\Helper\ShopTableHelper;

class ItemShopViewModel extends ShopViewModel{ 
    
    public function display(){	  
        $helper = new ShopTableHelper;
	    $itemList = new TableBuilder("shop");
	    $itemList->setAlign(new Align("center", "middle"));
        $itemList->buildHeaders("Image", "Category", "Name", "Description", "Price", "Buy");	
	    $itemList->setHelper($helper);
	  
        $itemnames = $this->model->getItemnames();
	    foreach($itemnames as $itemname){
	  	    $item = new Item($itemname);
		    $cells = new LinkedList;		 
	        $cells->add(new TCell($item->getImageURL(Model::GUI)));
		    $cells->add(new TCell($item->getCategory()));
		    $cells->add(new TCell($item->getItemname()));
		    $cells->add(new TCell($item->getDescription()));
		    $cells->add(new TCell($item->getPrice()));
		    $cells->add(new TCell($helper->getItemPurchaseForm($this->model, $item)));
		    $itemList->buildRow($cells);
        }	
        return $itemList;
    }           
}