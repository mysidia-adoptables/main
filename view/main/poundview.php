<?php

namespace View\Main;
use Resource\Collection\LinkedList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Container\Form;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Division;
use Resource\GUI\Element\Align;
use Service\Builder\TableBuilder;
use Service\Helper\AdoptTableHelper;

class PoundView extends View{
	
	public function index(){
		$document = $this->document;	
		$document->setTitle($this->lang->title);		
        $document->addLangvar($this->lang->default);
		
		$readoptForm = new Form("readoptform", "pound/adopt", "post");
		$readoptHeader = new Comment("Pounded Adoptables for adoption");
		$readoptHeader->setHeading(3);
		$readoptForm->add($readoptHeader);
		
        $helper = new AdoptTableHelper;
		$readoptTable = new TableBuilder("readopttable");
		$readoptTable->setAlign(new Align("center", "middle"));
		$readoptTable->buildHeaders("Select", "Image", "Basic Info", "Additional Info");
		$readoptTable->setHelper($helper);
		
		$poundMap = $this->getField("poundMap");		
		$iterator = $poundMap->iterator();		
		while($iterator->hasNext()){
		    $entry = $iterator->next();
			$adopt = $entry->getKey();
            $cost = $entry->getValue();

            $cells = new LinkedList;			
			$cells->add(new TCell($helper->getPoundButton($adopt->getAdoptID())));
			$cells->add(new TCell($adopt->getImage(Model::GUI)));
			$cells->add(new TCell($helper->getBasicInfo($adopt->getName(), $cost->getValue())));
			$cells->add(new TCell($helper->getAdditionalInfo($adopt)));
		    $readoptTable->buildRow($cells);
		}

        $notice = new Comment("Select an adoptable from above to become its new owner.");
        $notice->setHeading(3);		
		$readoptForm->add($readoptTable);
		$readoptForm->add($notice);
		$readoptForm->add(new Button("Adopt Me", "submit", "submit"));
		$document->add($readoptForm);
	}
	
	public function pound(){
		$mysidia = Registry::get("mysidia");
		$document = $this->document;
		$adopt = $this->getField("adopt");
		$confirm = $this->getField("confirm");
        
		if($confirm){
		    $cost = $this->getField("cost");
	        $document->setTitle($this->lang->pound_complete); 
		    $document->addLangvar($this->lang->pound_success);
			$document->addLangvar(" at a cost of {$cost->getValue()} {$mysidia->settings->cost}");
			$document->addLangvar($this->lang->afterwards);
			return;
		}
		
		$document->setTitle($this->lang->pound_title);
		$document->add($adopt->getImage(Model::GUI));
		$document->add(new Comment("<br>{$this->lang->pound}<br><br>{$this->lang->pound_warning}<br>"));		
		$options = new Division("pound");
		$options->setAlign(new Align("center"));
		
		$options->add(new Image("templates/icons/delete.gif", "Pound"));
		$options->add(new Link("pound/pound/{$adopt->getAdoptID()}/confirm", "Pound {$adopt->getName()} - I don't want it anymore!", TRUE));
	    $options->add(new Image("templates/icons/yes.gif", "Do not pound"));
		$options->add(new Link("myadopts/manage/{$adopt->getAdoptID()}", "DO NOT Pound {$adopt->getName()}"));
		$document->add($options);
	}
	
	public function adopt(){
		$mysidia = Registry::get("mysidia");
		$document = $this->document;			
		if($mysidia->input->post("submit")){
			$document->setTitle($this->lang->global_action_complete);
			$document->addLangvar($this->lang->readopt_success);			
		    $cost = $this->getField("cost");
			if($cost) $document->addLangvar(" at a cost of {$cost->getValue()} {$mysidia->settings->cost}");
		}
	}
}