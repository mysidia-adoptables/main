<?php

namespace Service\ApplicationService;
use ArrayObject;
use Model\DomainModel\Member;
use Model\DomainModel\OwnedAdoptable;
use Model\DomainModel\OwnedItem;
use Model\DomainModel\PrivateMessage;
use Model\DomainModel\TradeException;
use Model\DomainModel\TradeOffer;
use Model\Settings\TradeSettings;
use Resource\Collection\ArrayList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Native\Integer;
use Resource\Native\MysObject;
use Resource\Utility\Date;
use Service\Validator\TradeValidator;

class TradeService extends MysObject{
    
    private $settings;
    
    public function __construct(TradeSettings $settings){
        $this->settings = $settings;
    }
    
    public function getValidator(TradeOffer $offer = NULL){
	    if(func_num_args() == 0) throw new InvalidActionException("global_action");
        if(!$offer) throw new InvalidIDException("invalid");
        $validations = $this->getValidations($offer->getType());
	    return new TradeValidator($offer, $this->settings, $validations); 
    }
    
    private function getValidations($type){
        if($type == "public") return new ArrayObject(["public", "offered", "wanted", "adoptOffered", "adoptPublic", "itemOffered", "itemPublic", "cashOffered", "species", "interval", "number", "duration", "status", "usergroup", "item"]);
        if($type == "partial") return new ArrayObject(["recipient", "partial", "adoptOffered", "adoptWanted", "itemOffered", "itemWanted", "cashOffered", "species", "interval", "number", "duration", "usergroup", "item"]);
        return new ArrayObject(["recipient", "offered", "wanted", "adoptOffered", "adoptWanted", "itemOffered", "itemWanted", "cashOffered", "species", "interval", "number", "duration", "status", "usergroup", "item"]);
    }
     
    public function createTrade(ArrayObject $form, $tid = 0){
	    $mysidia = Registry::get("mysidia");
		$offer = new TradeOffer($tid, NULL, ($tid == 0));
        if($form->offsetExists("public") && $form["public"] == "yes") $offer->setType("public");
        elseif($form->offsetExists("partial") && $form["partial"] == "yes") $offer->setType("partial");
        else $offer->setType("private");

		$offer->setSender($mysidia->user->getID());
		$offer->setRecipient($form["recipient"]);
		$offer->setAdoptOffered(($form["adoptOffered"] == "none") ? NULL : $form["adoptOffered"]);
		$offer->setAdoptWanted(($form["adoptWanted"] == "none") ? NULL : $form["adoptWanted"]);
		$offer->setItemOffered(($form["itemOffered"] == "none") ? NULL : $form["itemOffered"]);
		$offer->setItemWanted(($form["itemWanted"] == "none") ? NULL : $form["itemWanted"]);
		$offer->setCashOffered((int)$form["cashOffered"]);
		$offer->setMessage($mysidia->secure($form["message"]));
		$offer->setStatus(($this->settings->moderate == "enabled") ? "moderate" : "pending");
		$offer->setDate(new Date);
		return $offer;        
    }
    
    public function createFromPublicTrade(ArrayObject $form, $tid){
        $mysidia = Registry::get("mysidia");
        $publicOffer = new TradeOffer($tid);
        if($tid == 0 || $publicOffer->getType() != "public") throw new TradeException("invalid");
        $offer = new TradeOffer(0, NULL, TRUE);
        $offer->setType("private");
        $offer->setSender($mysidia->user->getID());
        $offer->setRecipient($publicOffer->getSender());
        $offer->setAdoptOffered(($form["adoptOffered"] == "none") ? NULL : $form["adoptOffered"]);
        $offer->setAdoptWanted($publicOffer->getAdoptOfferedInfo());
        $offer->setItemOffered(($form["itemOffered"] == "none") ? NULL : $form["itemOffered"]);
        $offer->setItemWanted($publicOffer->getItemOfferedInfo());
        $offer->setCashOffered(0);
		$offer->setMessage($mysidia->secure($form["message"]));
		$offer->setStatus(($this->settings->moderate == "enabled") ? "moderate" : "pending");
		$offer->setDate(new Date);
		return $offer;     
    }
    
    public function saveTrade(TradeOffer $offer){
	    $mysidia = Registry::get("mysidia");
        if(!$offer->isNew()) throw new InvalidActionException("The trade offer already exists in database.");
		$adoptOffered = $offer->getAdoptOfferedInfo();
		$adoptWanted = $offer->getAdoptWantedInfo();
		$itemOffered = $offer->getItemOfferedInfo();
		$itemWanted = $offer->getItemWantedInfo();
		$mysidia->db->insert("trade", ["tid" => NULL, "type" => $offer->getType(), "sender" => $offer->getSender(), "recipient" => $offer->getRecipient(), 
		                               "adoptoffered" => $adoptOffered, "adoptwanted" => $adoptWanted, "itemoffered" => $itemOffered, "itemwanted" => $itemWanted, 
									   "cashoffered" => (int)$offer->getCashOffered(), "message" => $mysidia->secure($offer->getMessage()), "status" => $offer->getStatus(), "date" => $offer->getDate("Y-m-d")]);
        if($this->settings->moderate != "enabled"){
            $senderName = $offer->getSenderName();
            $offer->sendTradeMessage("You have received a trade request from {$senderName}!", 
                                     "You have received a trade request from {$senderName}! To see the details of this trade request and to accept or reject it, please visit your trade requests page to check out your trade offer.");
        }  
    }
    
    public function associate($publicID, $privateID){
        $mysidia = Registry::get("mysidia");
        $mysidia->db->insert("trade_associations", ["publicid" => $publicID, "privateid" => $privateID]);  
    }
    
    public function syncronize(TradeOffer $offer){
        $mysidia = Registry::get("mysidia");
        $publicID = $mysidia->db->select("trade_associations", ["publicid"], "privateid = '{$offer->getID()}'")->fetchColumn();
        if($publicID){
            $publicOffer = new TradeOffer($publicID);
            $publicOffer->setStatus("complete", Model::MODEL);
            $stmt = $mysidia->db->join("trade", "trade_associations.privateid = trade.tid")
                                ->select("trade_associations", [], "publicid = '{$publicID}' AND privateid != '{$offer->getID()}'");             
            while($dto = $stmt->fetchObject()){
                $privateOffer = new TradeOffer($dto->tid, $dto);
                if($privateOffer->isPending()){
                    $privateOffer->setStatus("canceled", Model::UPDATE);
                    $privateOffer->sendTradeMessage("Your trade offer has been canceled.", 
                                                    "The public trade you have subscribed to has become unavailable, and thus your trade offer id:{$privateOffer->getID()} has been canceled.", 
                                                    TRUE);
                }	
            } 
        }
    }
    
    public function completeTrade(TradeOffer $offer){
		if($offer->hasCashOffered()) $this->tradeCashOffered($offer);		
		if($offer->hasAdoptOffered()) $this->tradeAdoptOffered($offer);
		if($offer->hasAdoptWanted()) $this->tradeAdoptWanted($offer);
		if($offer->hasItemOffered()) $this->tradeItemOffered($offer);
		if($offer->hasItemWanted()) $this->tradeItemWanted($offer);
		return $offer->accept();
    }

    private function tradeCashOffered(TradeOffer $offer){
	    $cashOffered = $offer->getCashOffered();
        $offer->getSender(Model::MODEL)->changeMoney(-($cashOffered + $this->settings->tax));
        $offer->getRecipient(Model::MODEL)->changeMoney($cashOffered);     
    }
    
    private function tradeAdoptOffered(TradeOffer $offer){
	    $adoptOffered = $offer->getAdoptOffered();
		$adoptIterator = $adoptOffered->iterator();
		while($adoptIterator->hasNext()){
		    $aid = $adoptIterator->next();
			$adopt = new OwnedAdoptable($aid->getValue());
            if(!$adopt->isOwnerID($offer->getSender())) throw new TradeException("adoptoffered");
			$adopt->setOwner($offer->getRecipient(), Model::UPDATE);
		}        
    }
    
	private function tradeAdoptWanted(TradeOffer $offer){
	    $adoptWanted = $offer->getAdoptWanted();
		$adoptIterator = $adoptWanted->iterator();
		while($adoptIterator->hasNext()){
		    $aid = $adoptIterator->next();
			$adopt = new OwnedAdoptable($aid->getValue());
			$adopt->setOwner($offer->getSender(), Model::UPDATE);
		}
	}
    
	private function tradeItemOffered(TradeOffer $offer){
	    $itemOffered = $offer->getItemOffered();
		$itemIterator = $itemOffered->iterator();
		while($itemIterator->hasNext()){
		    $iid = $itemIterator->next();
			$item = new OwnedItem($iid->getValue());
			$item->remove();
            $newItem = new OwnedItem($item->getItemID(), $offer->getRecipient());
            $newItem->add(1, $offer->getRecipient());
		}
	}
    
    private function tradeItemWanted(TradeOffer $offer){
	    $itemWanted = $offer->getItemWanted();
		$itemIterator = $itemWanted->iterator();
		while($itemIterator->hasNext()){
		    $iid = $itemIterator->next();
			$item = new OwnedItem($iid->getValue());
			$item->remove();
			$newItem = new OwnedItem($item->getItemID(), $offer->getSender());
			$newItem->add(1, $offer->getSender());
		}
	}
    
    public function moderateTrade(TradeOffer $offer, $status = "pending"){
        $offer->setStatus($status, Model::UPDATE);
        if($status == "pending"){
            $senderName = $offer->getSenderName();
		    $this->sendModerateMessage($offer->getSender(),
			                          "Your Trade Offer has been approved!",
									  "Congratulations, your trade offer has been moderated and it's approved immediately. You may now wait for the response of your recipient(s).");
            $offer->sendTradeMessage("You have received a trade request from {$senderName}!", 
                                     "You have received a trade request from {$senderName}! To see the details of this trade request and to accept or reject it, please visit your trade requests page to check out your trade offer.");
        }
        else{
		    $this->sendModerateMessage($offer->getSender(),
			                           "Your Trade Offer has been disapproved!",
									   "Unfortunately, your trade offer has been moderated and it cannot be approved. We are terribly sorry about this, perhaps you should consider modifying your trade proposal a bit?");
        }		
	}
    
    private function sendModerateMessage($recipient, $title, $content){
	    $mysidia = Registry::get("mysidia");
		$pm = new PrivateMessage;
	    $pm->setSender($mysidia->user->getID());
	    $pm->setRecipient($recipient);
	    $pm->setMessage($title, $content);
	    $pm->post();        
    }
    
    public function getFormFields($action, $type = NULL, $id = NULL){
        if($type == "tid"){
            if($action == "publics") return $this->fetchPublicTradeFields($id);
            elseif($action == "partials") return $this->fetchPartialTradeFields($id);
            else return $this->fetchPrivateTradeFields($id);
        }	
		elseif($type == "user") return $this->fetchUserFields($id);
		elseif($type == "adopt") return $this->fetchAdoptFields($id);
		elseif($type == "item") return $this->fetchItemFields($id);
		else return $this->fetchDefaultFields();	        
    }
    
    public function getParamsField($id){
        $params = new ArrayList;
        $params->add(new Integer($id));
        return $params;
    }
    
    private function fetchOwnedAdoptFields($owner){
        $mysidia = Registry::get("mysidia");
	    $stmt = $mysidia->db->select("owned_adoptables", ["name", "aid"], "owner = :owner AND tradestatus = 'fortrade'", ["owner" => $owner]);
        $adoptOffered = ($stmt->rowcount() == 0) ? NULL : $mysidia->db->fetchMap($stmt);
        return $adoptOffered;
    }
    
    private function fetchOwnedItemFields($owner){
        $mysidia = Registry::get("mysidia");
	    $stmt = $mysidia->db->join("items", "items.id = inventory.item")
                            ->select("inventory", ["itemname", "iid"], "owner = :owner", ["owner" => $owner]);
        $itemOffered = ($stmt->rowcount() == 0) ? NULL : $mysidia->db->fetchMap($stmt);  
        return $itemOffered;
    }
    
    private function fetchCommonFields($recipient){
        $mysidia = Registry::get("mysidia");
        $adoptOffered = $this->fetchOwnedAdoptFields($mysidia->user->getID());
        $adoptWanted = $this->fetchOwnedAdoptFields($recipient);      
        $itemOffered = $this->fetchOwnedItemFields($mysidia->user->getID());
        $itemWanted = $this->fetchOwnedItemFields($recipient);
        return new ArrayObject(["adoptOffered" => $adoptOffered, "adoptWanted" => $adoptWanted, "itemOffered" => $itemOffered, "itemWanted" => $itemWanted]);
    }
    
    private function fetchDefaultFields(){
        $mysidia = Registry::get("mysidia");
        $adoptOffered = $this->fetchOwnedAdoptFields($mysidia->user->getID());
        $stmt = $mysidia->db->select("adoptables", ["type", "id"]);
        $adoptWanted = ($stmt->rowcount() == 0) ? NULL : $mysidia->db->fetchMap($stmt);
        
        $itemOffered = $this->fetchOwnedItemFields($mysidia->user->getID());
        $stmt2 = $mysidia->db->select("items", ["itemname", "id"]);
        $itemWanted = ($stmt2->rowcount() == 0) ? NULL : $mysidia->db->fetchMap($stmt2);
        return new ArrayObject(["recipient" => NULL, "adoptOffered" => $adoptOffered, "adoptWanted" => $adoptWanted,
                                "itemOffered" => $itemOffered, "itemWanted" => $itemWanted]);
    }
    
    private function fetchUserFields($uid){
        $fields = $this->fetchCommonFields($uid);
        $fields["recipient"] = new Member($uid);
        return $fields;
    }
    
    private function fetchAdoptFields($aid){
        $mysidia = Registry::get("mysidia");
        $uid = $mysidia->db->select("owned_adoptables", ["owner"], "aid = :aid", ["aid" => $aid])->fetchColumn();
        $fields = $this->fetchCommonFields($uid);
        $fields["recipient"] = new Member($uid);
        $fields["adoptSelected"] = new ArrayList;
        $fields["adoptSelected"]->add(new Integer($aid));
        return $fields;        
    }
    
    private function fetchItemFields($iid){
        $mysidia = Registry::get("mysidia");
        $uid = $mysidia->db->select("inventory", ["owner"], "iid = :iid", ["iid" => $iid])->fetchColumn();
        $fields = $this->fetchCommonFields($uid);
        $fields["recipient"] = new Member($uid);
        $fields["itemSelected"] = new ArrayList;
        $fields["itemSelected"]->add(new Integer($iid));
        return $fields;
    }
    
    private function fetchPublicTradeFields($tid){
        $mysidia = Registry::get("mysidia");
        $offer = new TradeOffer($tid);
        $recipient = $offer->getSender(Model::MODEL);
        $stmt = $mysidia->db->join("adoptables", "adoptables.id = owned_adoptables.adopt")
                            ->select("owned_adoptables", ["type", "aid"], $this->buildPublicQueries($mysidia->user->getID(), $offer->getAdoptWanted()));
        $adoptOffered = ($stmt->rowcount() == 0) ? NULL: $mysidia->db->fetchMap($stmt);

	    $stmt2 = $mysidia->db->join("items", "items.id = inventory.item")
                             ->select("inventory", ["itemname", "iid"], $this->buildPublicQueries($mysidia->user->getID(), $offer->getItemWanted()));
        $itemOffered = ($stmt2->rowcount() == 0) ? NULL : $mysidia->db->fetchMap($stmt2);

        return new ArrayObject(["recipient" => $recipient, "adoptOffered" => $adoptOffered, "adoptWanted" => $offer->getAdoptWanted(),
                                "itemOffered" => $itemOffered, "itemWanted" => $offer->getItemWanted()]);
    }
    
    private function fetchPartialTradeFields($tid){
        $offer = new TradeOffer($tid);
        $recipient = $offer->getSender(Model::MODEL);
        $fields = $this->fetchCommonFields($recipient->getID());
        $fields["recipient"] = $recipient;
        return $fields;
    }
    
    private function fetchPrivateTradeFields($tid){
        $offer = new TradeOffer($tid);
        $recipient = $offer->getRecipient(Model::MODEL);
        $fields = $this->fetchCommonFields($recipient->getID());
        $fields["recipient"] = $recipient;
        return $fields;
    }
    
    private function buildPublicQueries($owner, ArrayList $list = NULL){
        $whereClause = "owner = '{$owner}'";
        if(!$list) return $whereClause;
        
        $whereClause .= " AND (";
        $iterator = $list->iterator();
        while($iterator->hasNext()){
            $id = $iterator->next();
            $whereClause .= "id = '{$id->getValue()}' OR ";
        }
        $whereClause .= "0)";
        return $whereClause;       
    }
}