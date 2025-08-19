<?php

namespace Model\DomainModel;

use Resource\Core\Model;

abstract class Message extends Model implements Messagable
{
    protected $fromuser;
    protected $sender;
    protected $touser;
    protected $recipient;
    protected $datesent;

    public function getSender($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) {
            if (!$this->sender) {
                $this->sender = new Member($this->fromuser);
            }
            return $this->sender;
        } else {
            return $this->fromuser;
        }
    }

    public function getSenderID()
    {
        $sender = $this->getSender("model");
        return $sender ? $sender->getID() : 0;
    }

    public function getSenderName()
    {
        $sender = $this->getSender("model");
        return $sender ? $sender->getUsername() : "SYSTEM";
    }

    public function getSenderProfile()
    {
        return new UserProfile($this->fromuser);
    }

    public function isSender(User $user)
    {
        return ($user->getID() != 0 && $this->fromuser == $user->getID());
    }

    public function getRecipient($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) {
            if (!$this->recipient) {
                $this->recipient = new Member($this->touser);
            }
            return $this->recipient;
        } else {
            return $this->touser;
        }
    }

    public function getRecipientID()
    {
        $recipient = $this->getRecipient("model");
        return $recipient ? $recipient->getID() : 0;
    }

    public function getRecipientName()
    {
        $recipient = $this->getRecipient("model");
        return $recipient ? $recipient->getUsername() : "Unknown";
    }

    public function getRecipientProfile()
    {
        return new UserProfile($this->touser);
    }

    public function isRecipient(User $user)
    {
        return ($user->getID() != 0 && $this->touser == $user->getID());
    }

    public function getDateSent($format = null)
    {
        return $format ? $this->datesent->format($format) : $this->datesent;
    }

    public function setSender($sender)
    {
        $this->fromuser = ($sender instanceof Member) ? $sender->getID() : $sender;
    }

    public function setRecipient($recipient)
    {
        $this->touser = ($recipient instanceof Member) ? $recipient->getID() : $recipient;
    }

    abstract public function getTitle();

    abstract public function getContent();
}
