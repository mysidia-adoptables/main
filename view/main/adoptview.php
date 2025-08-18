<?php

namespace View\Main;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Component\RadioButton;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\Form;
use Resource\GUI\Container\Table;
use Resource\GUI\Container\TCell;
use Resource\GUI\Container\TRow;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;

class AdoptView extends View{
	
	public function index(){
	    $mysidia = Registry::get("mysidia");
		$document = $this->document;
		
	    if($mysidia->input->post("submit")){
            $ownedAdopt = $this->getField("ownedAdopt");
			$image = $ownedAdopt->getEggImage(Model::GUI);
			$image->setLineBreak(TRUE);	
			
            $document->setTitle("{$ownedAdopt->getName()} adopted successfully");			
			$document->add($image);
			$document->addLangvar("Congratulations!  You just adopted {$ownedAdopt->getName()}.  You can now manage {$ownedAdopt->getName()} on the ");
			$document->add(new Link("myadopts", "Myadopts Page."));
			$document->add(new Comment(""));
			$document->add(new Link("myadopts/manage/{$ownedAdopt->getID()}", "Click Here to Manage {$ownedAdopt->getName()}"));
			$document->add(new Comment(""));
			$document->add(new Link("myadopts/bbcode/{$ownedAdopt->getID()}", "Click Here to get BBCodes/HTML Codes for {$ownedAdopt->getName()}"));
			$document->add(new Comment(""));
			$document->addLangvar("Be sure and");
			$document->add(new Link("levelup/{$ownedAdopt->getID()}", "feed "));
			$document->addLangvar("{$ownedAdopt->getName()} with clicks so that they grow!");
		    return;
		}
		
		$document->setTitle($mysidia->lang->title);
        $document->addLangvar($mysidia->user->isLoggedIn() ? $mysidia->lang->member : $mysidia->lang->guest);  		
        $adoptForm = new Form("form", "adopt", "post");
		$adoptTitle = new Comment("Available Adoptables");
		$adoptTitle->setHeading(3);
		$adoptForm->add($adoptTitle);
		$adoptTable = new Table("table", "", FALSE);
 		
		$adopts = $this->getField("adopts");
		for($i = 0; $i < $adopts->length(); $i++){
		    $row = new TRow;
		    $idCell = new TCell(new RadioButton("", "id", $adopts[$i]->getID()));				
			$imageCell = new TCell(new Image($adopts[$i]->getEggImage(), $adopts[$i]->getType()));
			$imageCell->setAlign(new Align("center"));
				
			$type = new Comment($adopts[$i]->getType());
			$type->setBold();
            $description = new Comment($adopts[$i]->getDescription(), FALSE);
			$typeCell = new TCell;
            $typeCell->add($type);
            $typeCell->add($description);			

		    $row->add($idCell);
			$row->add($imageCell);
			$row->add($typeCell);
            $adoptTable->add($row);
		}
		
		$adoptForm->add($adoptTable);		
		$adoptSubtitle = new Comment("Adopt");
		$adoptSubtitle->setHeading(3);
		$adoptForm->add($adoptSubtitle);
		$adoptForm->add(new Comment("Adoptable Name: ", FALSE));
		$adoptForm->add(new TextField("name"));
		$adoptForm->add(new Comment(""));
        $adoptForm->add(new Button("Adopt Me", "submit", "submit"));
        $document->add($adoptForm);	
	}
}