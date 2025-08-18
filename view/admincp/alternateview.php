<?php

namespace View\AdminCP;
use Model\DomainModel\AdoptAlternate;
use Model\DomainModel\Level;
use Resource\Collection\LinkedList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Component\PasswordField;
use Resource\GUI\Component\RadioButton;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\RadioList;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;

class AlternateView extends View{
	
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
		$alternateForm = new FormBuilder("addform", "add", "post");
		$title = new Comment("Create a New Alt-Form:");
		$title->setBold();
		$title->setUnderlined();
		$alternateForm->add($title);
		
		$alternateForm->add(new Comment("<br><b>Adoptable Type</b>: ", FALSE));
		$alternateForm->add(new Comment($this->lang->adopt_explain));	
		$alternateForm->buildDropdownList("adopt", "AdoptTypeList");
		$alternateForm->add(new Comment("<br><br><b>Alt-Form Image</b>: ", FALSE));
		$alternateForm->add(new Comment($this->lang->image_explain));	
		$alternateForm->add(new TextField("imageurl"));
		$alternateForm->add(new Comment("<b>Or select an existing image</b>: "));
		$alternateForm->buildDropdownList("existingimageurl", "ImageList"); 
		
		$alternateForm->add(new Comment("<br><br><b>Starting Level</b>: ", FALSE));
		$alternateForm->add(new Comment($this->lang->level_explain));
		$alternateForm->add(new TextField("level", 1));
		$alternateForm->add(new Comment("<br><br><b>Item</b>: ", FALSE));
		$alternateForm->add(new Comment($this->lang->item_explain));
		$alternateForm->buildDropdownList("item", "ItemNameList");

	    $genderList = new RadioList("gender");
	    $genderList->add(new RadioButton("Male", "gender", "male"));
	    $genderList->add(new RadioButton("Female", "gender", "female"));
	    $genderList->add(new RadioButton("Both", "gender", "both"));
	    $genderList->check("both");
	    $alternateForm->add(new Comment("<br><br><b>Gender</b>: ", FALSE));
		$alternateForm->add(new Comment($this->lang->gender_explain));
	    $alternateForm->add($genderList);

		$alternateForm->add(new Comment("<br><br><b>Last Alt Form</b>: "));
		$alternateForm->add(new Comment($this->lang->lastalt_explain));
		$alternateForm->add(new TextField("lastalt"));
		$alternateForm->add(new Comment("<br><br><b>Chance for alt-form to be chosen</b>: "));
		$alternateForm->add(new Comment($this->lang->chance_explain));
		$alternateForm->add(new TextField("chance"));
		$alternateForm->add(new Button("Create Alt-Form", "submit", "submit"));
		$document->add($alternateForm);
	}

	public function edit(){
	    $mysidia = Registry::get("mysidia");
		$document = $this->document;			
        $alternate = $this->getField("alternate");	
        
        if($mysidia->input->post("submit")){ 
            $document->setTitle($this->lang->edited_title);
			$document->addLangvar($this->lang->edited);            
        }
        elseif($mysidia->input->post("select") || $alternate){
			if(!$alternate){
                $adopt = $this->getField("adopt");
		        $document->setTitle($this->lang->manage_alternate . $adopt->getType());
				$document->addLangvar($this->lang->manage_explain);
	         	$alternateTable = new TableBuilder("alternate");
	     	    $alternateTable->setAlign(new Align("center", "middle"));
		        $alternateTable->buildHeaders("AltID", "Image", "Level", "Gender", "Chance", "Edit", "Delete");
                $levels = $this->getField("levels");
                $alternates = $this->getField("alternates");
                
                for($i = 0; $i < $alternates->count(); $i++){
                    $level = $levels->get($i);
                    $alternateTable->buildRow($this->buildPrimaryCells($level));
                    $iterator = $alternates[$i]->iterator();
                    while($iterator->hasNext()){
                        $alternate = $iterator->next();
                        $alternateTable->buildRow($this->buildAlternateCells($alternate));
                    }
                }
                $document->add($alternateTable);
			}
            else{		
		        $document->setTitle($this->lang->edit_title);
			    $document->addLangvar($this->lang->edit);
		        $alternateForm = new FormBuilder("editform", $alternate->getID(), "post");
		        $title = new Comment("Edit an adoptable Alt-Form:");
		        $title->setBold();
		        $title->setUnderlined();
		        $alternateForm->add($title);
		
		        $alternateForm->add(new Comment("<br><b>Adoptable Type</b>: ", FALSE));
		        $alternateForm->add(new Comment($this->lang->adopt_explain));	
		        $alternateForm->buildDropdownList("adopt", "AdoptTypeList", $alternate->getAdopt());
		        $alternateForm->add(new Comment("<br><br><b>Alt-Form Image</b>: ", FALSE));
		        $alternateForm->add(new Comment($this->lang->image_explain));	
		        $alternateForm->add(new TextField("imageurl", $alternate->getImage()));
	        	$alternateForm->add(new Comment("<b>Or select an existing image</b>: "));
	    	    $alternateForm->buildDropdownList("existingimageurl", "ImageList", $alternate->getImage()); 
		
		        $alternateForm->add(new Comment("<br><br><b>Starting Level</b>: ", FALSE));
	         	$alternateForm->add(new Comment($this->lang->level_explain));
		        $alternateForm->add(new TextField("level", $alternate->getLevel()));
		        $alternateForm->add(new Comment("<br><br><b>Item</b>: ", FALSE));
		        $alternateForm->add(new Comment($this->lang->item_explain));
		        $alternateForm->buildDropdownList("item", "ItemNameList", $alternate->getItem());

	            $genderList = new RadioList("gender");
	            $genderList->add(new RadioButton("Male", "gender", "male"));
	            $genderList->add(new RadioButton("Female", "gender", "female"));
	            $genderList->add(new RadioButton("Both", "gender", "both"));
	            $genderList->check($alternate->getGender());
	            $alternateForm->add(new Comment("<br><br><b>Gender</b>: ", FALSE));
	         	$alternateForm->add(new Comment($this->lang->gender_explain));
	            $alternateForm->add($genderList);

		        $alternateForm->add(new Comment("<br><br><b>Last Alt Form</b>: "));
		        $alternateForm->add(new Comment($this->lang->lastalt_explain));
		        $alternateForm->add(new TextField("lastalt", $alternate->getLastAlt()));
		        $alternateForm->add(new Comment("<br><br><b>Chance for alt-form to be chosen</b>: "));
		        $alternateForm->add(new Comment($this->lang->chance_explain));
		        $alternateForm->add(new TextField("chance", $alternate->getChance()));
				$alternateForm->add(new PasswordField("hidden", "adopt", $alternate->getAdopt()));
		        $alternateForm->add(new Button("Edit Alt-Form", "submit", "submit"));
		        $document->add($alternateForm);	 			 
            }				            
        }
        else{ 
		    $document = $this->document;
		    $document->setTitle($this->lang->manage_title);
		    $document->add(new Comment($this->lang->manage));
	        $typeForm = new FormBuilder("typeform", "", "post");
		    $typeForm->buildDropdownList("adopt", "AdoptTypeList")->buildButton("Select", "select", "select");
		    $document->add($typeForm);            
        }
	}

    public function delete(){
		$document = $this->document;
        $alternate = $this->getField("alternate");
		if(!$alternate) $this->edit();
		else{
		    $document->setTitle($this->lang->delete_title);
		    $document->addLangvar($this->lang->delete);
		}       	
    }
    
    private function buildPrimaryCells(Level $level){
        $primaryCells = new LinkedList; 
		$primaryCells->add(new TCell("N/A"));
		$primaryCells->add(new TCell($level->getPrimaryImage(Model::GUI)));
		$primaryCells->add(new TCell($level->getLevel()));
		$primaryCells->add(new TCell("Both"));
		$primaryCells->add(new TCell("Default"));
		$primaryCells->add(new TCell(new Image("templates/icons/no.gif")));
		$primaryCells->add(new TCell(new Image("templates/icons/no.gif")));	
        return $primaryCells;
    }
    
    private function buildAlternateCells(AdoptAlternate $alternate){
	    $alternateCells = new LinkedList;
		$alternateCells->add(new TCell($alternate->getID()));
		$alternateCells->add(new TCell($alternate->getImage(Model::GUI)));
		$alternateCells->add(new TCell($alternate->getLevel()));
		$alternateCells->add(new TCell($alternate->getGender()));
        
        $chanceCell = new TCell(($alternate->getLastAlt()) ? "{$alternate->getChance()}%<br>(last alt ID: {$alternate->getLastAlt()})" : "{$alternate->getChance()}%");
		$alternateCells->add($chanceCell);
		$alternateCells->add(new TCell(new Link("admincp/alternate/edit/{$alternate->getID()}", new Image("templates/icons/cog.gif"))));
		$alternateCells->add(new TCell(new Link("admincp/alternate/delete/{$alternate->getID()}", new Image("templates/icons/delete.gif"))));					   
        return $alternateCells;                    
    }
}