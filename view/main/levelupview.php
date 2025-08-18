<?php

namespace View\Main;
use Resource\Collection\ArrayList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Link;
use Resource\GUI\Container\Table;
use Resource\GUI\Container\TCell;
use Resource\GUI\Container\TRow;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Division;
use Resource\GUI\Element\Align;

class LevelupView extends View{
	
	public function click(){
	    $mysidia = Registry::get("mysidia");
		$document = $this->document;				
        $adopt = $this->getField("adopt");			
		$reward = $this->getField("reward");
		$document->setTitle("{$this->lang->gave} {$adopt->getName()} one {$this->lang->unit}");

		$image = $adopt->getImage(Model::GUI);        
		$image->setLineBreak(TRUE);		
		$summary = new Division;
		$summary->setAlign(new Align("center"));
        $summary->add($image);
        $summary->add(new Comment("{$this->lang->gave}{$adopt->getName()} one {$this->lang->unit}."));
        $summary->add(new Comment($this->lang->encourage));
        if($mysidia->user->isLoggedIn()){
            $summary->add(new Comment("<br> You have earned {$reward} {$mysidia->settings->cost} for leveling up this adoptable. "));
            $summary->add(new Comment("You now have {$mysidia->user->getMoney()} {$mysidia->settings->cost}"));
        }
        $document->add($summary);			
	}

	public function siggy(){}
	
	public function daycare(){
		$document = $this->document;	
        $document->setTitle($this->lang->daycare_title);
        $document->addLangvar($this->lang->daycare, TRUE);
		
		$daycare = $this->getField("daycare");
        $adopts = $this->getField("adopts");
		$daycareTable = new Table("daycare", "", FALSE);
        $daycareTable->setAlign(new Align("center"));
		$total = $daycare->getTotalAdopts();
        $index = 0;

		for($row = 0; $row < $daycare->getTotalRows(); $row++){
	        $daycareRow = new TRow("row{$row}");
            for($column = 0; $column < $daycare->getTotalColumns(); $column++){
			    $adopt = $adopts->get($index);
				$cell = new ArrayList;
				$cell->add(new Link("levelup/click/{$adopt->getAdoptID()}", $adopt->getImage(Model::GUI), TRUE));
				$cell->add(new Comment($daycare->getStats($adopt)));
				$daycareCell = new TCell($cell, "cell{$index}");
                $daycareCell->setAlign(new Align("center", "center"));
				$daycareRow->add($daycareCell);
				$index++;
				if($index == $total) break;
            }
            $daycareTable->add($daycareRow);			
		}
		
        $document->add($daycareTable);
        $pagination = $daycare->getPagination(); 
		if($pagination) $document->addLangvar($pagination->showPage());
	}
}