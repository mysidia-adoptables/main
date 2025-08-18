<?php

namespace Controller\Main;
use Model\DomainModel\OwnedAdoptable;
use Resource\Collection\LinkedList;
use Resource\Core\AppController;
use Resource\Core\Registry;

class StatsController extends AppController{
	
	public function index(){
	    $mysidia = Registry::get("mysidia");
		$stmt = $mysidia->db->join("adoptables", "adoptables.id = owned_adoptables.adopt")
                           ->select("owned_adoptables", [], "1 ORDER BY totalclicks DESC LIMIT 10");
		$top10 = new LinkedList;
		while($dto = $stmt->fetchObject()){
		    $top10->add(new OwnedAdoptable($dto->aid, $dto));    
		}
        $this->setField("top10", $top10);
		
		$stmt2 = $mysidia->db->join("adoptables", "adoptables.id = owned_adoptables.adopt")
                           ->select("owned_adoptables", [], "1 ORDER BY RAND() DESC LIMIT 5");
		$rand5 = new LinkedList;
		while($dto = $stmt2->fetchObject()){
		    $rand5->add(new OwnedAdoptable($dto->aid, $dto));    
		}
		$this->setField("rand5", $rand5);
	}              
}