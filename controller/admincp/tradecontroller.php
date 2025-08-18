<?php

namespace Controller\AdminCP;
use Exception;
use Model\DomainModel\TradeOffer;
use Model\Settings\TradeSettings;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;
use Resource\Native\MysString;
use Service\ApplicationService\TradeService;

class TradeController extends AppController{
    
	private $settings;
	
	public function __construct(){
        parent::__construct();
		$mysidia = Registry::get("mysidia");
        $this->settings = new TradeSettings($mysidia->db);
		if($mysidia->usergroup->getpermission("canmanagesettings") != "yes"){
		    throw new NoPermissionException("You do not have permission to manage trade.");
		}		
    }
	
	public function index(){
	    parent::index();
	    $mysidia = Registry::get("mysidia");		
        $total = $mysidia->db->select("trade")->rowCount();
        if($total == 0) throw new InvalidIDException("default_none");
		$pagination = new Pagination($total, $mysidia->settings->pagination, "admincp/trade", $mysidia->input->get("page"));
        $stmt = $mysidia->db->select("trade", [], "1 LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
		$tradeOffers = new ArrayList;
        while($dto = $stmt->fetchObject()){
            $tradeOffers->add(new TradeOffer($dto->tid, $dto));
        }
        $this->setField("pagination", $pagination);
		$this->setField("tradeOffers", $tradeOffers);
	}
	
	public function add(){
	    $mysidia = Registry::get("mysidia");		
	    if($mysidia->input->post("submit")){
		    $this->dataValidate();			
		    $mysidia->db->insert("trade", ["tid" => NULL, "type" => $mysidia->input->post("type"), "sender" => $mysidia->input->post("sender"), "recipient" => $mysidia->input->post("recipient"), 
		                                   "adoptoffered" => $mysidia->input->post("adoptOffered"), "adoptwanted" => $mysidia->input->post("adoptWanted"), "itemoffered" => $mysidia->input->post("itemOffered"), "itemwanted" => $mysidia->input->post("itemWanted"), 
										   "cashoffered" => $mysidia->input->post("cashOffered"), "message" => stripslashes($mysidia->input->post("message")), "status" => $mysidia->input->post("status"), "date" => $mysidia->input->post("date")]);
		}
	}
	
	public function edit($tid = NULL){
	    $mysidia = Registry::get("mysidia");		
	    if(!$tid) return $this->index();
        try{
           $tradeOffer = new TradeOffer($tid);
		   if($mysidia->input->post("submit")){
		       $this->dataValidate();
			   $mysidia->db->update("trade", ["type" => $mysidia->input->post("type"), "sender" => $mysidia->input->post("sender"), "recipient" => $mysidia->input->post("recipient"), 
		                                      "adoptoffered" => $mysidia->input->post("adoptOffered"), "adoptwanted" => $mysidia->input->post("adoptWanted"), "itemoffered" => $mysidia->input->post("itemOffered"), "itemwanted" => $mysidia->input->post("itemWanted"), 
										      "cashoffered" => $mysidia->input->post("cashOffered"), "message" => stripslashes($mysidia->input->post("message")), "status" => $mysidia->input->post("status"), "date" => $mysidia->input->post("date")], "tid = '{$tid}'");
		   }
        }
        catch(BlankFieldException $bfe){
            throw $bfe;
        }
        catch(Exception $e){
            throw new InvalidIDException("nonexist");		
        }
		$this->setField("tradeOffer", $tid ? $tradeOffer : NULL);
	}

	public function delete($tid = NULL){
	    $mysidia = Registry::get("mysidia");	
        if(!$tid) $this->index();
		else{
            $tradeOffer = new TradeOffer($tid);
            $mysidia->db->delete("trade", "tid = '{$tradeOffer->getID()}'");
            if($tradeOffer->getType() == "public"){
                $mysidia->db->delete("trade_associations", "publicid = '{$tradeOffer->getID()}'");
            }
            else $mysidia->db->delete("trade_associations", "privateid = '{$tradeOffer->getID()}'");
        }
        $this->setField("tradeOffer", $tid ? $tradeOffer : NULL);
	}
	
	public function moderate($tid = NULL){
	    $mysidia = Registry::get("mysidia");
	    if($tid){
		    // A trade offer has been select for moderation, let's go over it!
			$tradeOffer = new TradeOffer($tid);				
		    if($mysidia->input->post("submit")){
			    $status = $mysidia->input->post("status");
                $tradeService = new TradeService($this->settings);
                $tradeService->moderateTrade($tradeOffer, $status);	
                $this->setField("status", new MysString($status));			
			}		
			$this->setField("tradeOffer", $tradeOffer);
			return;
		}				
		$stmt = $mysidia->db->select("trade", [], "status = 'moderate'");
        $tradeOffers = new ArrayList;
        while($dto = $stmt->fetchObject()){
            $tradeOffers->add(new TradeOffer($dto->tid, $dto));
        }
        $this->setField("tradeOffers", $tradeOffers);
	}
	
	public function settings(){
	    $mysidia = Registry::get("mysidia");
		if($mysidia->input->post("submit")){
		    $settings = ['system', 'multiple', 'partial', 'public', 'species', 'interval', 'number', 'duration', 'tax', 'usergroup', 'item', 'moderate'];
			foreach($settings as $name){			
				if($mysidia->input->post($name) != ($this->settings->{$name})){
                    $mysidia->db->update("trade_settings", ["value" => $mysidia->input->post($name)], "name = :name", ["name" => $name]);	 			
                }      
            }
		}		
		$this->setField("tradeSettings", $this->settings);
	}
	
	private function dataValidate(){
	    $mysidia = Registry::get("mysidia");
		if(!$mysidia->input->post("sender")) throw new BlankFieldException("sender");
		if(!$mysidia->input->post("recipient") && $mysidia->input->post("type") != "public") throw new BlankFieldException("recipient");	
		if($mysidia->input->post("recipient") && $mysidia->input->post("type") == "public") throw new BlankFieldException("public");			
		if(!$mysidia->input->post("adoptOffered") && !$mysidia->input->post("adoptWanted") && !$mysidia->input->post("itemOffered") and !$mysidia->input->post("itemWanted") and !$mysidia->input->post("cashOffered")) throw new BlankFieldException("blank"); 		
	}
}