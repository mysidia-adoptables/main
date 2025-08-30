<?php

namespace Service\Helper;
use Model\DomainModel\Adoptable;
use Model\DomainModel\AdoptNotfoundException;
use Model\DomainModel\Item;
use Model\DomainModel\Member;
use Model\DomainModel\OwnedAdoptable;
use Model\DomainModel\OwnedItem;
use Resource\Collection\ArrayList;
use Resource\Core\Model;
use Resource\GUI\Component\CheckBox;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Option;
use Resource\GUI\Component\PasswordField;
use Resource\GUI\Container\DropdownList;
use Resource\GUI\Container\SelectionList;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Division;

class TradeFormHelper extends Helper{

    private $lang;
	private $multiSelect;

	public function __construct($lang, $multiSelect = FALSE){
	    $this->lang = $lang;
		$this->multiSelect = $multiSelect;
	}
	
	public function getRecipient(Member|NULL $recipient = NULL){
	    $lang = $this->lang;
        $recipientDiv = new Division(NULL, "recipient");
        $recipientDiv->add(new Image("templates/icons/warning.gif"));

		if(!$recipient) $recipientDiv->add(new Comment($lang->recipient_none, TRUE, "b"));
		else{
            $recipientDiv->add(new Comment($lang->recipient . $recipient->getUsername(), TRUE, "b"));
            $recipientDiv->add(new PasswordField("hidden", "recipient", $recipient->getID()));
        }
        return $recipientDiv;
	}
	
	public function getAdoptOffered(ArrayList|NULL $adopts = NULL, $adoptOffered = NULL){
	    $lang = $this->lang;
        $adoptOfferedDiv = new Division(Null, "adoptoffered");

		if(!$adoptOffered) $adoptOfferedDiv->add(new Comment($lang->adopt_offered_none));
		else{
		    $list = $this->multiSelect ? new SelectionList("adoptOffered[]", TRUE) : new DropdownList("adoptOffered");
            $list->add(new Option("None Selected", "none"));          
            $list->fill($adoptOffered);
            if($adopts) $this->selectOptions($list, $adopts);

            $adoptOfferedDiv->add(new Image("templates/icons/next.gif"));
            $adoptOfferedDiv->add(new Comment($lang->adopt_offered));
            $adoptOfferedDiv->add($list);  			
		}
        return $adoptOfferedDiv;
	}
	
	public function getAdoptWanted(ArrayList|NULL $adopts = NULL, $adoptWanted = NULL){
	    $lang = $this->lang;
        $adoptWantedDiv = new Division(Null, "adoptwanted");

		if(!$adoptWanted) $adoptWantedDiv->add(new Comment($lang->adopt_wanted_none));
        else{
 		    $list = $this->multiSelect ? new SelectionList("adoptWanted[]", TRUE) : new DropdownList("adoptWanted");
            $list->add(new Option("None Selected", "none"));            
            $list->fill($adoptWanted);
            if($adopts) $this->selectOptions($list, $adopts); 

            $adoptWantedDiv->add(new Image("templates/icons/next.gif"));
            $adoptWantedDiv->add(new Comment($lang->adopt_wanted)); 
            $adoptWantedDiv->add($list);				      
        }
		return $adoptWantedDiv;     		
	}

	public function getAdoptOfferedPublic($recipient = NULL, $adoptOffered = NULL){
	    $lang = $this->lang;
        $adoptOfferedDiv = new Division(Null, "adoptoffered");

		if(!$recipient || !$adoptOffered) $adoptOfferedDiv->add(new Comment($lang->adopt_offered_none));
        else{
		    $list = $this->multiSelect ? new SelectionList("adoptOffered[]", TRUE) : new DropdownList("adoptOffered");
            $list->add(new Option("None Selected", "none"));          
            $list->fill($adoptOffered);

            $adoptOfferedDiv->add(new Image("templates/icons/next.gif"));
            $adoptOfferedDiv->add(new Comment($lang->adopt_offered));
            $adoptOfferedDiv->add($list); 			      
        }
		return $adoptOfferedDiv;     		
	}

	public function getAdoptWantedPublic($recipient = NULL, $adoptWanted = NULL){
	    $lang = $this->lang;
        $adoptWantedDiv = new Division(Null, "adoptwanted");

		if(!$recipient ||  !$adoptWanted) $adoptWantedDiv->add(new Comment($lang->adopt_wanted_none));
        else{
            $adoptWantedDiv->add(new Image("templates/icons/next.gif"));
            $adoptWantedDiv->add(new Comment($lang->adopt_wanted_public));
 	        $adoptIterator = $adoptWanted->iterator();
            while($adoptIterator->hasNext()){
		        $aid = $adoptIterator->next();
                $adopt = new OwnedAdoptable($aid->getValue());
			    $adoptWantedDiv->add($adopt->getImage(Model::GUI));
                $adoptWantedDiv->add(new PasswordField("hidden", "adoptWanted[]", $aid));
            }      		    			      
        }
        $adoptWantedDiv->add(new Comment("<br>"));   
		return $adoptWantedDiv;     		
	}
	
	public function getItemOffered(ArrayList|NULL $items = NULL, $itemOffered = NULL){
	    $lang = $this->lang;
        $itemOfferedDiv = new Division(Null, "itemoffered");

		if(!$itemOffered) $itemOfferedDiv->add(new Comment($lang->item_offered_none));
		else{
		    $list = $this->multiSelect ? new SelectionList("itemOffered[]", TRUE) : new DropdownList("itemOffered");
            $list->add(new Option("None Selected", "none"));            
            $list->fill($itemOffered);
            if($items) $this->selectOptions($list, $items); 

            $itemOfferedDiv->add(new Image("templates/icons/next.gif"));
            $itemOfferedDiv->add(new Comment($lang->item_offered)); 
            $itemOfferedDiv->add($list);				
		}
		return $itemOfferedDiv;
	}
	
	public function getItemWanted(ArrayList|NULL $items = NULL, $itemWanted = NULL){
	    $lang = $this->lang;
        $itemWantedDiv = new Division(Null, "itemwanted");

        if(!$itemWanted) $itemWantedDiv->add(new Comment($lang->item_wanted_none));
        else{
 		    $list = $this->multiSelect ? new SelectionList("itemWanted[]", TRUE) : new DropdownList("itemWanted");
            $list->add(new Option("None Selected", "none"));            
            $list->fill($itemWanted);
            if($items) $this->selectOptions($list, $items); 

            $itemWantedDiv->add(new Image("templates/icons/next.gif"));
            $itemWantedDiv->add(new Comment($lang->item_wanted));
            $itemWantedDiv->add($list); 			        
        }
		return $itemWantedDiv;   		
	}

	public function getItemOfferedPublic($recipient = NULL, $itemOffered = NULL){
	    $lang = $this->lang;
        $itemOfferedDiv = new Division(Null, "itemwanted");

		if(!$recipient || !$itemOffered) $itemOfferedDiv->add(new Comment($lang->item_offered_none));
        else{
		    $list = $this->multiSelect ? new SelectionList("itemOffered[]", TRUE) : new DropdownList("itemOffered");
            $list->add(new Option("None Selected", "none"));          
            $list->fill($itemOffered);

            $itemOfferedDiv->add(new Image("templates/icons/next.gif"));
            $itemOfferedDiv->add(new Comment($lang->item_offered));
            $itemOfferedDiv->add($list); 			      
        }
		return $itemOfferedDiv;     		
	}

	public function getItemWantedPublic($recipient = NULL, $itemWanted = NULL){
	    $lang = $this->lang;
        $itemWantedDiv = new Division(Null, "itemwanted");

		if(!$recipient || !$itemWanted) $itemWantedDiv->add(new Comment($lang->item_wanted_none));
        else{
            $itemWantedDiv->add(new Image("templates/icons/next.gif"));
            $itemWantedDiv->add(new Comment($lang->item_wanted_public));
 	        $itemIterator = $itemWanted->iterator();
            while($itemIterator->hasNext()){
		        $iid = $itemIterator->next();
                $item = new OwnedItem($iid->getValue());
			    $itemWantedDiv->add($item->getImageURL(Model::GUI));
                $itemWantedDiv->add(new PasswordField("hidden", "itemWanted[]", $iid));
            }        		    			      
        }
        $itemWantedDiv->add(new Comment("<br>")); 
		return $itemWantedDiv;     		
	}

    public function getPublicOffer($recipient = NULL){
	    $lang = $this->lang;
        $checkbox = new CheckBox("This is a public trade offer", "public", "yes");
        if(!$recipient) $checkbox->setChecked(TRUE);

        $publicDiv = new Division(NULL, "publics");
		$publicDiv->add($checkbox);
        $publicDiv->add(new Image("templates/icons/warning.gif"));
		$publicDiv->add(new Comment($lang->public_offer));
        return $publicDiv;
    }

    public function getPartialOffer($recipient = NULL){
	    $lang = $this->lang;
        if(!$recipient) return new Comment;
        $checkbox = new CheckBox("This is a partial trade offer", "partial", "yes");

        $partialDiv = new Division(NULL, "partials");
		$partialDiv->add($checkbox);
        $partialDiv->add(new Image("templates/icons/warning.gif"));
		$partialDiv->add(new Comment($lang->partial_offer));
        return $partialDiv;
    }
	
	public function getAdoptImages(ArrayList|NULL $adopts = NULL, $resize = TRUE){
	    $adoptsDiv = new Division(NULL, "adopts");
		if(!$adopts){
		    $adoptsDiv->add(new Comment("N/A"));
			return $adoptsDiv;
		}
	
		$size = $adopts->size();	
		$rows = round(sqrt($size));
		$columns = ceil($size / $rows);
	    $adoptIterator = $adopts->iterator();
        while($adoptIterator->hasNext()){
		     $aid = $adoptIterator->next();
             try{
                 $adopt = new OwnedAdoptable($aid->getValue());
			     $image = $adopt->getImage(Model::GUI);
                 $image->setDimension(40);
             }
             catch(AdoptNotfoundException $ane){
                 $image = new Image("templates/icons/no.gif");
             }
			 if($resize) $image->resize(1 / $columns, TRUE);
			 $adoptsDiv->add($image);
        }
        return $adoptsDiv;		
	}
	
	public function getItemImages(ArrayList|NULL $items = NULL, $resize = TRUE){
	    $itemsDiv = new Division(NULL, "items");
		if(!$items){
		    $itemsDiv->add(new Comment("N/A"));
			return $itemsDiv;
		}
	
		$size = $items->size();
        $rows = round(sqrt($size));
		$columns = ceil($size/$rows);
	    $itemIterator = $items->iterator();
        while($itemIterator->hasNext()){
		     $iid = $itemIterator->next();
             $item = new OwnedItem($iid->getValue());
			 $image = ($item->isNew()) ? new Image("templates/icons/no.gif") : $item->getImageURL(Model::GUI);
             $image->setDimension(40);
			 if($resize) $image->resize(1 / $columns, TRUE);
			 $itemsDiv->add($image);
        }
        return $itemsDiv;		
	}

	public function getAdoptList(ArrayList|NULL $adopts = NULL){
	    $adoptsDiv = new Division(NULL, "adopts");
		if(!$adopts){
		    $adoptsDiv->add(new Comment("N/A"));
			return $adoptsDiv;
		}	
		
	    $adoptIterator = $adopts->iterator();
        while($adoptIterator->hasNext()){
		     $id = $adoptIterator->next();
             $adopt = new Adoptable($id->getValue());
			 $adoptsDiv->add(new Comment($adopt->getType()));
        }
        return $adoptsDiv;		
	}
	
	public function getItemList(ArrayList|NULL $items = NULL){
	    $itemsDiv = new Division(NULL, "items");
		if(!$items){
		    $itemsDiv->add(new Comment("N/A"));
			return $itemsDiv;
		}	
		
	    $itemIterator = $items->iterator();
        while($itemIterator->hasNext()){
		     $id = $itemIterator->next();
             $item = new Item($id->getValue());
			 $itemsDiv->add(new Comment($item->getItemname()));
        }
        return $itemsDiv;		
	}

    private function selectOptions(DropdownList $list, ArrayList $options){
        $optionsIterator = $options->iterator();
		while($optionsIterator->hasNext()){
		    $option = $optionsIterator->next();
			$list->select($option);
		}
    }	
}