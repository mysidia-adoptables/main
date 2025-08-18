<?php

namespace View\Main;
use Resource\Collection\LinkedList;
use Resource\Core\View;
use Resource\GUI\Container\TCell;
use Resource\GUI\Element\Align;
use Service\Builder\TableBuilder;
use Service\Helper\AdoptTableHelper;

class StatsView extends View{
	
	public function index(){
		$document = $this->document;		
	    $document->setTitle($this->lang->title);
		$document->addLangvar($this->lang->default . $this->lang->top10 . $this->lang->top10_text);
		$document->add($this->getTable("top10", $this->getField("top10")));
		$document->addLangvar($this->lang->random . $this->lang->random_text);
        $document->add($this->getTable("rand5", $this->getField("rand5")));		
	} 

    private function getTable($name, LinkedList $list){
        $helper = new AdoptTableHelper;
		$table = new TableBuilder($name);
		$table->setAlign(new Align("center", "middle"));
	    $table->buildHeaders("Adoptable Image", "Adoptable Name", "Adoptable Owner", "Total Clicks", "Current Level");	
	    $table->setHelper($helper);
		
		$iterator = $list->iterator();
		while($iterator->hasNext()){
		    $adopt = $iterator->next();
			$cells = new LinkedList;
			$cells->add(new TCell($helper->getLevelupLink($adopt)));
		    $cells->add(new TCell($adopt->getName()));
			$cells->add(new TCell($helper->getOwnerProfile($adopt->getOwner(), $adopt->getOwnerName())));
			$cells->add(new TCell($adopt->getTotalClicks()));
			$cells->add(new TCell($adopt->getCurrentLevel()));
            $table->buildRow($cells);			
		}
		return $table;
    }	
}