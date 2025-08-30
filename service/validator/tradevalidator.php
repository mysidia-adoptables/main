<?php

namespace Service\Validator;

use ArrayObject;
use Model\DomainModel\Adoptable;
use Model\DomainModel\AdoptNotfoundException;
use Model\DomainModel\Item;
use Model\DomainModel\ItemException;
use Model\DomainModel\OwnedAdoptable;
use Model\DomainModel\OwnedItem;
use Model\DomainModel\TradeException;
use Model\DomainModel\TradeOffer;
use Model\Settings\TradeSettings;
use Resource\Collection\ArrayList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\Validator;
use Resource\Utility\Date;

class TradeValidator extends Validator
{
    public function __construct(private readonly TradeOffer $offer, private readonly TradeSettings $settings, ArrayObject $validations)
    {
        parent::__construct($validations);
    }

    protected function checkRecipient()
    {
        $recipientID = $this->offer->getRecipient();
        if (!$recipientID) {
            throw new TradeException("recipient_empty");
        } else {
            $sender = $this->offer->getSender(Model::MODEL);
            if ($recipientID == $sender->getID()) {
                throw new TradeInvalidException("recipient_duplicate");
            }
            $recipient = $this->offer->getRecipient(Model::MODEL);
            if ($recipient->getOption()->getTradeStatus() == 1) {
                if (!$recipient->isFriend($sender)) {
                    throw new TradeException("recipient_privacy");
                }
            }
        }
    }

    protected function checkPublic()
    {
        if ($this->offer->getRecipient()) {
            throw new TradeException("recipient_public");
        }
    }

    protected function checkPartial()
    {
        if (!$this->offer->hasAdoptOffered() && !$this->offer->hasAdoptWanted() &&
           !$this->offer->hasItemOffered() && !$this->offer->hasItemWanted() && !$this->offer->hasCashOffered()) {
            throw new TradeException("recipient_partial");
        }
    }

    protected function checkOffered()
    {
        if (!$this->offer->hasAdoptOffered() && !$this->offer->hasItemOffered() && !$this->offer->hasCashOffered()) {
            throw new TradeException("offers");
        }
    }

    protected function checkWanted()
    {
        if (!$this->offer->hasAdoptWanted() && !$this->offer->hasItemWanted()) {
            throw new TradeException("wanted");
        }
    }

    protected function checkAdoptOffered()
    {
        if (!$this->offer->hasAdoptOffered()) {
            return;
        }
        try {
            $senderID = $this->offer->getSender();
            $adoptOffered = $this->offer->getAdoptOffered();
            $adoptIterator = $adoptOffered->iterator();
            while ($adoptIterator->hasNext()) {
                $aid = $adoptIterator->next();
                $adopt = new OwnedAdoptable($aid->getValue());
                if (!$adopt->isOwnerID($senderID)) {
                    throw new TradeException("adoptoffered");
                }
            }
        } catch (AdoptNotfoundException) {
            throw new TradeException("adoptoffered");
        }
    }

    protected function checkAdoptWanted()
    {
        if (!$this->offer->hasAdoptWanted()) {
            return;
        }
        try {
            $recipientID = $this->offer->getRecipient();
            $adoptWanted = $this->offer->getAdoptWanted();
            $adoptIterator = $adoptWanted->iterator();
            while ($adoptIterator->hasNext()) {
                $aid = $adoptIterator->next();
                $adopt = new OwnedAdoptable($aid->getValue());
                if (!$adopt->isOwnerID($recipientID)) {
                    throw new TradeException("adoptwanted");
                }
            }
        } catch (AdoptNotfoundException) {
            throw new TradeException("adoptwanted");
        }
    }

    protected function checkAdoptPublic()
    {
        if (!$this->offer->hasAdoptWanted()) {
            return;
        }
        try {
            $adoptWanted = $this->offer->getAdoptWanted();
            $adoptIterator = $adoptWanted->iterator();
            while ($adoptIterator->hasNext()) {
                $id = $adoptIterator->next();
                $adopt = new Adoptable($id->getValue());
            }
        } catch (AdoptNotfoundException) {
            throw new TradeException("public_adopt");
        }
    }

    protected function checkItemOffered()
    {
        if (!$this->offer->hasItemOffered()) {
            return;
        }
        $itemOffered = $this->offer->getItemOffered();
        $itemIterator = $itemOffered->iterator();
        while ($itemIterator->hasNext()) {
            $iid = $itemIterator->next();
            $item = new OwnedItem($iid->getValue());
            if ($item->isNew()) {
                throw new TradeException("itemoffered");
            }
            if (!$item->isTradable()) {
                throw new TradeException("item");
            }
        }
    }

    protected function checkItemWanted()
    {
        if (!$this->offer->hasItemWanted()) {
            return;
        }
        $itemWanted = $this->offer->getItemWanted();
        $itemIterator = $itemWanted->iterator();
        while ($itemIterator->hasNext()) {
            $iid = $itemIterator->next();
            $item = new OwnedItem($iid->getValue());
            if ($item->isNew()) {
                throw new TradeException("itemwanted");
            }
            if (!$item->isTradable()) {
                throw new TradeException("item");
            }
        }
    }

    protected function checkItemPublic()
    {
        if (!$this->offer->hasItemWanted()) {
            return;
        }
        try {
            $itemWanted = $this->offer->getItemWanted();
            $itemIterator = $itemWanted->iterator();
            while ($itemIterator->hasNext()) {
                $id = $itemIterator->next();
                $item = new Item($id->getValue());
            }
        } catch (ItemException) {
            throw new TradeException("public_item");
        }
    }

    protected function checkCashOffered()
    {
        if (!$this->offer->hasCashOffered()) {
            return;
        }
        $cashOffered = $this->offer->getCashOffered();
        $cashLeft = $this->offer->getSender("model")->getMoney() - ($cashOffered + $this->settings->tax);
        if ($cashLeft < 0) {
            throw new TradeException("cashoffered");
        }
    }

    protected function checkStatus()
    {
        $status = $this->offer->getStatus();
        if ($status != "pending" && $status != "moderate") {
            throw new TradeException("status");
        }
    }

    protected function checkSpecies()
    {
        if (empty($this->settings->species)) {
            return;
        }
        if ($this->offer->hasAdoptOffered()) {
            $this->checkMultipleSpecies($this->offer->getAdoptOffered());
        }
        if ($this->offer->hasAdoptWanted()) {
            $this->checkMultipleSpecies($this->offer->getAdoptWanted());
        }
    }

    private function CheckMultipleSpecies(ArrayList $adopts)
    {
        foreach ($this->settings->species as $species) {
            $adoptIterator = $adopts->iterator();
            while ($adoptIterator->hasNext()) {
                $aid = $adoptIterator->next();
                $adopt = new OwnedAdoptable($aid->getValue());
                if ($adopt->getType() == $species) {
                    throw new TradeException("species");
                }
            }
        }
    }

    protected function checkInterval()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->action() != "offer") {
            return;
        }
        $today = new Date();
        $validDate = $today->modify("-{$this->settings->interval} day");
        $lastTime = $mysidia->db->select("trade", ["date"], "sender = '{$this->offer->getSender()}' ORDER BY date DESC LIMIT 3")->fetchColumn();
        if (!$lastTime) {
            return;
        }
        $lastDate = new Date($lastTime);
        if ($lastDate > $validDate) {
            throw new TradeException("interval");
        }
    }

    protected function checkNumber()
    {
        if ($this->settings->number == 0) {
            throw new TradeException("number");
        }
        if ($this->offer->hasAdoptOffered()) {
            $this->checkNumbers($this->offer->getAdoptOffered());
        }
        if ($this->offer->hasAdoptWanted()) {
            $this->checkNumbers($this->offer->getAdoptWanted());
        }
        if ($this->offer->hasItemOffered()) {
            $this->checkNumbers($this->offer->getItemOffered());
        }
        if ($this->offer->hasItemWanted()) {
            $this->checkNumbers($this->offer->getItemWanted());
        }
    }

    private function checkNumbers(ArrayList $list)
    {
        if ($this->settings->number < $list->size()) {
            throw new TradeException("number");
        }
    }

    protected function checkDuration()
    {
        $today = new Date();
        $expirationDate = $this->offer->getDate()->modify("+{$this->settings->duration} day");
        if ($today > $expirationDate) {
            throw new TradeException("duration");
        }
    }

    protected function checkUsergroup()
    {
        if ($this->settings->usergroup == "all") {
            return;
        }
        $senderGroup = $this->offer->getSender(Model::MODEL)->getUsergroup();
        $recipientGroup = $this->offer->getRecipient(Model::MODEL)->getUsergroup();
        foreach ($this->settings->usergroup as $usergroup) {
            if ($senderGroup == $usergroup || $recipientGroup == $usergroup) {
                return;
            }
        }
        throw new TradeException("usergroup");
    }

    protected function checkItem()
    {
        if (!$this->settings->item) {
            return;
        }
        if ($this->offer->hasItemOffered()) {
            $this->checkMultipleItem($this->offer->getItemOffered(), $this->offer->getSender());
        }
        if ($this->offer->hasItemWanted()) {
            $this->checkMultipleItem($this->offer->getItemWanted(), $this->offer->getRecipient());
        }
    }

    private function checkMultipleItem(ArrayList $items, $owner)
    {
        foreach ($this->settings->item as $item) {
            $itemIterator = $items->iterator();
            while ($itemIterator->hasNext()) {
                $iid = $itemIterator->next();
                $item = new OwnedItem($iid->getValue(), $owner);
                if ($item->isNew() || !$item->isTradable()) {
                    throw new TradeException("item");
                }
            }
        }
    }
}
