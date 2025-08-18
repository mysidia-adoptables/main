<?php

namespace Controller\Main;
use Model\DomainModel\AdoptNotfoundException;
use Model\DomainModel\OwnedAdoptable;
use Model\DomainModel\PoundAdoptable;
use Model\DomainModel\Vote;
use Model\Settings\PoundSettings;
use Model\ViewModel\OwnedAdoptableViewModel;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Model;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\NoPermissionException;
use Resource\Native\MysString;

class MyadoptsController extends AppController{

	private $adopt;

    public function __construct(){
        parent::__construct("member");
		$mysidia = Registry::get("mysidia");
        if($mysidia->systems->adopts != "enabled") throw new NoPermissionException("The admin has turned off adoption feature for this site, please contact him/her for detailed information.");
    }
	
	public function index(){
	    $mysidia = Registry::get("mysidia");
        $total = $mysidia->user->countOwnedAdopts();
        if($total == 0) throw new AdoptNotfoundException($mysidia->lang->empty);
		$pagination = new Pagination($total, $mysidia->settings->pagination, 
                                     "myadopts", $mysidia->input->get("page"));
		$stmt = $mysidia->db->join("adoptables", "adoptables.id = owned_adoptables.adopt")
                           ->select("owned_adoptables", [], "owner ='{$mysidia->user->getID()}' ORDER BY totalclicks LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
        $ownedAdopts = new ArrayList;
        while($dto = $stmt->fetchObject()){ 
            $ownedAdopts->add(new OwnedAdoptableViewModel(new OwnedAdoptable($dto->aid, $dto)));
        }
		$this->setField("pagination", $pagination);
        $this->setField("ownedAdopts", $ownedAdopts);
	}
	
	public function manage($aid){
        $this->initOwnedAdopt($aid);
        $this->setField("ownedAdopt", $this->adopt);	
		$this->setField("image", $this->adopt->getImage(Model::GUI));		
	}
	
	public function stats($aid){
		$mysidia = Registry::get("mysidia");
        $this->initOwnedAdopt($aid);
        $stmt = $mysidia->db->select("vote_voters", [], "adoptableid='{$this->adopt->getAdoptID()}' ORDER BY date DESC LIMIT 10");
        $votes = new ArrayList;
        while($dto = $stmt->fetchObject()){ 
            $votes->add(new Vote(NULL, NULL, NULL, NULL, $dto));
        }
        $this->setField("ownedAdopt", new OwnedAdoptableViewModel($this->adopt));	
		$this->setField("votes", $votes);
	}
	
	public function bbcode($aid){
        $this->initOwnedAdopt($aid);
		$this->setField("adopt", $this->adopt);	
	}
	
	public function rename($aid){
		$mysidia = Registry::get("mysidia");
        $this->initOwnedAdopt($aid);
		if($mysidia->input->post("submit")){
            if(!$mysidia->input->post("adoptname")){ 
				$this->setFlags("rename_error", "rename_empty");
                return;	
            }
            
		    $poundsettings = new PoundSettings($mysidia->db);
		    $isPounded = $mysidia->db->select("pounds", ["aid"], "aid='{$this->adopt->getAdoptID()}'")->fetchColumn();
			if($isPounded && $poundsettings->rename == "yes"){
                $poundAdopt = new PoundAdoptable($aid);
			    if($poundAdopt->getFirstOwner() != $mysidia->user->getID()){
				    $this->setFlags("rename_error", "rename_owner");
                    return;	
                }				
            }			
			$this->adopt->setName($mysidia->input->post("adoptname"), Model::UPDATE);
		}
        $this->setField("adopt", $this->adopt);		
		$this->setField("image", $this->adopt->getImage(Model::GUI));			
	}
	
	public function trade($aid, $confirm = NULL){
        $this->initOwnedAdopt($aid);
		if($confirm){
            $tradeStatus = ($this->adopt->getTradeStatus() == "fortrade") ? "notfortrade" : "fortrade";
            $this->adopt->setTradeStatus($tradeStatus, Model::UPDATE);
		}
        $this->setField("adopt", $this->adopt);
		$this->setField("image", $this->adopt->getImage(Model::GUI));	
        $this->setField("confirm", $confirm ? new MysString($confirm) : NULL);				
	}
	
	public function freeze($aid, $confirm = NULL){
        $this->initOwnedAdopt($aid);
		if($confirm){
            $frozen = ($this->adopt->isFrozen() == "yes") ? "no" : "yes";
            $this->adopt->setFrozen($frozen, Model::UPDATE);		       
	    }	 
        $this->setField("adopt", $this->adopt);
		$this->setField("image", $this->adopt->getImage(Model::GUI));	
        $this->setField("confirm", $confirm ? new MysString($confirm) : NULL);
	}
    
    private function initOwnedAdopt($aid){
        $mysidia = Registry::get("mysidia");
		$this->adopt = new OwnedAdoptable($aid);	
        if(!$this->adopt->isOwner($mysidia->user)) throw new NoPermissionException("permission");		       
    }
}