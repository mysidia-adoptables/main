<?php

namespace View\Main;
use Resource\Core\Registry;
use Resource\Core\View;
use Service\Builder\FormBuilder;

class PromoView extends View{
	
	public function index(){
	    $mysidia = Registry::get("mysidia");
		$document = $this->document;
		
	    if($mysidia->input->post("promocode")){
            $type = (string)$this->getField("type");
            $reward = (string)$this->getField("reward");
		    $document->setTitle($this->lang->success);
            $document->addLangvar($this->lang->avail, TRUE);
            if($type == "Adopt") $document->addLangvar("Congrats, you have acquired the adoptable {$reward} by entering promocode.");
		    elseif($type == "Item") $document->addLangvar("Congrats, you have acquired the item {$reward} by entering promocode.");
			return;
		}
        
        $document->setTitle($this->lang->title);
        $document->addLangvar($this->lang->default, TRUE);		
        $promoForm = new FormBuilder("promoform", "", "post");
        $promoForm->buildComment("Your Promo Code: ", FALSE)
		          ->buildTextField("promocode")	
                  ->buildButton("Enter Code", "submit", "submit");
        $document->add($promoForm);				  
	}              
}