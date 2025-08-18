<?php

namespace View\AdminCP;
use Resource\Collection\LinkedHashMap;
use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Container\TCell;
use Resource\GUI\Element\Align;
use Resource\Native\MysString;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\AdoptTableHelper;

class BreedingView extends View{

    public function index(){
	    parent::index();
		$document = $this->document;
        $helper = new AdoptTableHelper;
        $breedAdopts = $this->getField("breedAdopts");								
		$breedAdoptTable = new TableBuilder("breedadopt");
		$breedAdoptTable->setAlign(new Align("center", "middle"));
		$breedAdoptTable->buildHeaders("Breed ID", "Offspring", "Parents", "Mother", "Father", "Edit", "Delete");
		$breedAdoptTable->setHelper($helper);	
		
        $iterator = $breedAdopts->iterator();
        while($iterator->hasNext()){
            $breedAdopt = $iterator->next();
		    $cells = new LinkedList;
		    $cells->add(new TCell($breedAdopt->getID()));
		    $cells->add(new TCell($breedAdopt->getOffspringType()));
		    $cells->add(new TCell($breedAdopt->getParentType()));
		    $cells->add(new TCell($breedAdopt->getMotherType()));
		    $cells->add(new TCell($breedAdopt->getFatherType()));
		    $cells->add(new TCell($helper->getEditLink($breedAdopt->getID())));
		    $cells->add(new TCell($helper->getDeleteLink($breedAdopt->getID())));
		    $breedAdoptTable->buildRow($cells);            
        }
        $document->add($breedAdoptTable);	

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
		$breedAdoptForm = new FormBuilder("addform", "add", "post");
		$breedAdoptForm->buildComment("<u><strong>Create A New Baby Adoptable:</strong></u>")
		               ->buildComment("Baby Adoptable: ", FALSE)->buildDropdownList("offspring", "AdoptTypeList")
					   ->buildComment("Parent Adoptable(s): ", FALSE)->buildDropdownList("parent", "AdoptTypeList")
					   ->buildComment("<b>If both parents are specified in the above field, separate them by comma.</b>")
					   ->buildComment("Mother Adoptable: ", FALSE)->buildDropdownList("mother", "AdoptTypeList")
		               ->buildComment("Father Adoptable: ", FALSE)->buildDropdownList("father", "AdoptTypeList")
					   ->buildComment("<b>The two fields above should be left empty if the parent field is entered.</b>")
					   ->buildComment("Probability for Baby Adoptable to appear: ", FALSE)->buildTextField("probability")
					   ->buildComment("<b>The total probability for all baby possible adoptables is normalized to 100, so this number can be any positive integers.</b>")
					   ->buildComment("Baby Adoptable Survival Rate(0-100 scale): ", FALSE)->buildTextField("survival")
					   ->buildComment("Level Requirement: ", FALSE)->buildTextField("level")
					   ->buildCheckBox(" Make this baby adopt available now.", "available", "yes")
					   ->buildButton("Create a Baby Adopt", "submit", "submit");
		$document->add($breedAdoptForm);		
    }

    public function edit(){
	    $mysidia = Registry::get("mysidia");
		$document = $this->document;
        $breedAdopt = $this->getField("breedAdopt");
	    if(!$breedAdopt) $this->index();
        elseif(!$mysidia->input->post("submit")){
	        $document->setTitle($this->lang->edit_title);
	    	$document->addLangvar($this->lang->edit);
            $breedAdoptForm = new FormBuilder("editform", $breedAdopt->getID(), "post");
		    $breedAdoptForm->buildComment("<u><strong>Create A New Baby Adoptable:</strong></u>")
		                   ->buildComment("Baby Adoptable: ", FALSE)->buildDropdownList("offspring", "AdoptTypeList", $breedAdopt->getOffspring())
					       ->buildComment("Parent Adoptable(s): ", FALSE)->buildDropdownList("parent", "AdoptTypeList", $breedAdopt->getParent())
					       ->buildComment("<b>If both parents are specified in the above field, separate them by comma.</b>")
					       ->buildComment("Mother Adoptable: ", FALSE)->buildDropdownList("mother", "AdoptTypeList", $breedAdopt->getMother())
		                   ->buildComment("Father Adoptable: ", FALSE)->buildDropdownList("father", "AdoptTypeList", $breedAdopt->getFather())
					       ->buildComment("<b>The two fields above should be left empty if the parent field is entered.</b>")
					       ->buildComment("Probability for Baby Adoptable to appear: ", FALSE)->buildTextField("probability", $breedAdopt->getProbability())
					       ->buildComment("<b>The total probability for all baby possible adoptables is normalized to 100, so this number can be any positive integers.</b>")
					       ->buildComment("Baby Adoptable Survival Rate(0-100 scale): ", FALSE)->buildTextField("survival", $breedAdopt->getSurvivalRate())
					       ->buildComment("Level Requirement: ", FALSE)->buildTextField("level", $breedAdopt->getRequiredLevel())
					       ->buildCheckBox(" Make this baby adopt available now.", "available", "yes", $breedAdopt->isAvailable())
					       ->buildButton("Update Baby Adopt", "submit", "submit");
		    $document->add($breedAdoptForm);            
        }		
	    else{
      		$document->setTitle($this->lang->edited_title);
			$document->addLangvar($this->lang->edited);
		}		
    }

    public function delete(){
		$document = $this->document;
        $breedAdopt = $this->getField("breedAdopt");
        if(!$breedAdopt) $this->index();
        else{
		    $document->setTitle($this->lang->delete_title);
		    $document->addLangvar($this->lang->delete);
            header("Refresh:3; URL='../index'");
        }
    }
	
	public function settings(){
	    $mysidia = Registry::get("mysidia");
		$document = $this->document;			
		if($mysidia->input->post("submit")){
			$document->setTitle($this->lang->settings_changed_title);
            $document->addLangvar($this->lang->settings_changed);
		    return;
		}
		
        $breedingSettings = $this->getField("breedingSettings");			
		$document->setTitle($this->lang->settings_title);
		$document->addLangvar($this->lang->settings);
		$settingsForm = new FormBuilder("settingsform", "settings", "post");
		$breedingSystem = new LinkedHashMap;
		$breedingSystem->put(new MysString("Enabled"), new MysString("enabled"));
		$breedingSystem->put(new MysString("Disabled"), new MysString("disabled"));
		$breedingMethod = new LinkedHashMap;
		$breedingMethod->put(new MysString("Heuristic"), new MysString("heuristic"));
		$breedingMethod->put(new MysString("Advanced"), new MysString("advanced"));		

		$settingsForm->buildComment("Breeding System Enabled:   ", FALSE)->buildRadioList("system", $breedingSystem, $breedingSettings->system)
					 ->buildComment("Breeding Method(heuristic or advanced):   ", FALSE)->buildRadioList("method", $breedingMethod, $breedingSettings->method)
					 ->buildComment("Ineligible Species(separate by comma):   ", FALSE)->buildTextField("species", ($breedingSettings->species) ? implode(",", $breedingSettings->species) : "")
		             ->buildComment("Interval/wait-time(days) between successive attempts:	 ", FALSE)->buildTextField("interval", $breedingSettings->interval)
					 ->buildComment("Minimum Level Requirement:	 ", FALSE)->buildTextField("level", $breedingSettings->level)
					 ->buildComment("Maximum Breeding Attempts for each adopt:	", FALSE)->buildTextField("capacity", $breedingSettings->capacity)
					 ->buildComment("Maximum Number of Offsprings per Breeding attempt:   ", FALSE)->buildTextField("number", $breedingSettings->number)
					 ->buildComment("Chance for successful Breeding attempt:   ", FALSE)->buildTextField("chance", $breedingSettings->chance)
		             ->buildComment("Cost for each Breeding attempt:	 ", FALSE)->buildTextField("cost", $breedingSettings->cost)
					 ->buildComment("Usergroup(s) permitted to breed(separate by comma):	", FALSE)->buildTextField("usergroup", ($breedingSettings->usergroup == "all") ? $breedingSettings->usergroup : implode(",", $breedingSettings->usergroup))
					 ->buildComment("Item(s) required to breed(separate by comma):	", FALSE)->buildTextField("item", ($breedingSettings->item) ? implode(",", $breedingSettings->item) : "")
					 ->buildButton("Change Breeding Settings", "submit", "submit");
		$document->add($settingsForm);	
	}
}