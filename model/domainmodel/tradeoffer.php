<?php

namespace Model\DomainModel;

use Resource\Collection\ArrayList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Native\Integer;
use Resource\Utility\Date;

class TradeOffer extends Model
{

    const IDKEY = "tid";
    protected $tid;
    protected $type;
    protected $sender;
    protected $recipient;
    protected $adoptoffered;
    protected $adoptwanted;
    protected $itemoffered;
    protected $itemwanted;
    protected $cashoffered;
    protected $message;
    protected $status;
    protected $date;

    public function __construct($tid, $dto = null, $new = false)
    {
        $mysidia = Registry::get("mysidia");
        if ($new) $this->tid = 0;
        else {
            if (!$dto) {
                $dto = $mysidia->db->select("trade", [], "tid = :tid", ["tid" => $tid])->fetchObject();
                if (!is_object($dto)) throw new TradeException("Trade Offer id: {$tid} does not exist...");
            }
            parent::__construct($dto);
        }
    }

    protected function createFromDTO($dto)
    {
        parent::createFromDTO($dto);
        $this->date = new Date($dto->date);
    }

    public function getID()
    {
        return $this->tid;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getSender($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) return new Member($this->sender);
        return $this->sender;
    }

    public function getSenderName()
    {
        if (!$this->sender) return null;
        return $this->getSender(Model::MODEL)->getUsername();
    }

    public function setSender($sender, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) $this->save("sender", $sender);
        $this->sender = $sender;
    }

    public function getRecipient($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) return new Member($this->recipient);
        return $this->recipient;
    }

    public function getRecipientName()
    {
        if (!$this->recipient) return null;
        return $this->getRecipient(Model::MODEL)->getUsername();
    }

    public function setRecipient($recipient, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) $this->save("recipient", $recipient);
        $this->recipient = $recipient;
    }

    public function hasAdoptOffered()
    {
        return ($this->adoptoffered != null);
    }

    public function getAdoptOffered()
    {
        if (!($this->adoptoffered instanceof ArrayList)) {
            if (!$this->hasAdoptOffered()) return null;
            $adoptOffered = is_string($this->adoptoffered) ? explode(",", $this->adoptoffered) : $this->adoptoffered;
            $this->adoptoffered = new ArrayList;
            foreach ($adoptOffered as $aid) {
                $this->adoptoffered->add(new Integer($aid));
            }
        }
        return $this->adoptoffered;
    }

    public function getAdoptOfferedInfo()
    {
        if (!$this->hasAdoptOffered()) return null;
        $info = $this->getAdoptOffered()->toArray();
        return implode(",", $info);
    }

    public function setAdoptOffered($adoptOffered, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) $this->save("adoptoffered", $adoptOffered);
        $this->adoptoffered = $adoptOffered;
    }

    public function hasAdoptWanted()
    {
        return ($this->adoptwanted != null);
    }

    public function getAdoptWanted()
    {
        if (!($this->adoptwanted instanceof ArrayList)) {
            if (!$this->hasAdoptWanted()) return null;
            $adoptWanted = (is_string($this->adoptwanted)) ? explode(",", $this->adoptwanted) : $this->adoptwanted;
            $this->adoptwanted = new ArrayList;
            foreach ($adoptWanted as $aid) {
                $this->adoptwanted->add(new Integer($aid));
            }
        }
        return $this->adoptwanted;
    }

    public function getAdoptWantedInfo()
    {
        if (!$this->hasAdoptWanted()) return null;
        $info = $this->getAdoptWanted()->toArray();
        return implode(",", $info);
    }

    public function setAdoptWanted($adoptWanted, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) $this->save("adoptwanted", $adoptWanted);
        $this->adoptwanted = $adoptWanted;
    }

    public function hasItemOffered()
    {
        return ($this->itemoffered != null);
    }

    public function getItemOffered()
    {
        if (!($this->itemoffered instanceof ArrayList)) {
            if (!$this->hasItemOffered()) return null;
            $itemOffered = (is_string($this->itemoffered)) ? explode(",", $this->itemoffered) : $this->itemoffered;
            $this->itemoffered = new ArrayList;
            foreach ($itemOffered as $iid) {
                $this->itemoffered->add(new Integer($iid));
            }
        }
        return $this->itemoffered;
    }

    public function getItemOfferedInfo()
    {
        if (!$this->hasItemOffered()) return null;
        $info = $this->getItemOffered()->toArray();
        return implode(",", $info);
    }

    public function setItemOffered($itemOffered, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) $this->save("itemoffered", $itemOffered);
        $this->itemoffered = $itemOffered;
    }

    public function hasItemWanted()
    {
        return ($this->itemwanted != null);
    }

    public function getItemWanted()
    {
        if (!($this->itemwanted instanceof ArrayList)) {
            if (!$this->hasItemWanted()) return null;
            $itemWanted = is_string($this->itemwanted) ? explode(",", $this->itemwanted) : $this->itemwanted;
            $this->itemwanted = new ArrayList;
            foreach ($itemWanted as $iid) {
                $this->itemwanted->add(new Integer($iid));
            }
        }
        return $this->itemwanted;
    }

    public function getItemWantedInfo()
    {
        if (!$this->hasItemWanted()) return null;
        $info = $this->getItemWanted()->toArray();
        return implode(",", $info);
    }

    public function setItemWanted($itemWanted, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) $this->save("itemwanted", $itemWanted);
        $this->itemwanted = $itemWanted;
    }

    public function hasCashOffered()
    {
        return ($this->cashoffered != null);
    }

    public function getCashOffered()
    {
        return $this->cashoffered;
    }

    public function setCashOffered($cashOffered, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) $this->save("cashoffered", $cashOffered);
        $this->cashoffered = $cashOffered;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) $this->save("message", $message);
        $this->message = $message;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) $this->save("status", $status);
        $this->status = $status;
    }

    public function isPending()
    {
        return ($this->status == "pending");
    }

    public function getDate($format = null)
    {
        return $format ? $this->date->format($format) : $this->date;
    }

    public function setDate(Date $date)
    {
        $this->date = $date;
    }


    public function revise()
    {
        if ($this->isNew()) throw new InvalidActionException("Cannot revise a new trade that does not exist in database.");
        $this->saveAll();
        $senderName = $this->getSenderName();
        return $this->sendTradeMessage("Your Trade Offer from {$senderName} has been updated!",
            "The trade offer with {$senderName} has been modified by the sender! Wanna take a look and see what have been changed?");
    }

    public function cancel()
    {
        $this->setStatus("canceled", Model::UPDATE);
        $senderName = $this->getSenderName();
        return $this->sendTradeMessage("Your trade request from {$senderName} is canceled.",
            "Unfortunately, your trade offer from {$senderName} has been canceled by the sender, you will have to give up on it...");
    }

    public function accept()
    {
        $this->setStatus("complete", Model::UPDATE);
        $recipientName = $this->getRecipientName();
        return $this->sendTradeMessage("Your trade request to {$recipientName} is sucessful.",
            "Congratulations, your trade offer sent to {$recipientName} has been accepted! You may now manage your new adoptables and items!",
            true);
    }

    public function decline()
    {
        $this->setStatus("declined", Model::UPDATE);
        $recipientName = $this->getRecipientName();
        $this->sendTradeMessage("Your trade request to {$recipientName} was declined.",
            "We're sorry, but {$recipientName} declined your recent trade request. Don't worry, there are lots of other users willing to trade. You may now search for users to trade with.",
            ($this->type != "partial"));
    }

    public function reverse($type = "")
    {
        $mysidia = Registry::get("mysidia");
        if ($this->isNew()) throw new InvalidActionException("Cannot return a new trade that does not exist in database.");
        $senderName = $this->getSenderName();
        if ($type) $this->save("type", $type);
        $this->save("sender", $this->recipient);
        $this->save("recipient", $this->sender);
        $this->save("adoptoffered", $this->getAdoptWantedInfo());
        $this->save("adoptwanted", $this->getAdoptOfferedInfo());
        $this->save("itemoffered", $this->getItemWantedInfo());
        $this->save("itemwanted", $this->getItemOfferedInfo());
        $this->save("cashoffered", $this->cashoffered);
        $this->save("message", $mysidia->secure($this->message));
        return $this->sendTradeMessage("Your Trade Offer from {$senderName} has been returned!",
            "The trade offer with {$senderName} has been modified by the sender! Now the sender/recipient roles have been reversed, wanna take a look and see what have been changed? Note the new trade's type is {$this->type}",
            true);
    }

    public function sendTradeMessage($title, $content, $reverse = false)
    {
        $message = new PrivateMessage;
        $message->setSender($reverse ? $this->recipient : $this->sender);
        $message->setRecipient($reverse ? $this->sender : $this->recipient);
        $message->setMessage($title, $content);
        $message->post();
        return $message;
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("trade", [$field => $value], "tid='{$this->tid}'");
    }

    protected function saveAll()
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("trade", ["adoptoffered" => $this->getAdoptOfferedInfo(), "adoptwanted" => $this->getAdoptWantedInfo(), "itemoffered" => $this->getItemOfferedInfo(),
            "itemwanted" => $this->getItemWantedInfo(), "cashoffered" => (int)$this->cashoffered, "message" => $mysidia->secure($this->message),
            "status" => $this->status, "date" => $this->date->format("Y-m-d")], "tid = {$this->tid}");
    }
}
