<?php

namespace Model\DomainModel;

use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Utility\Date;

class VisitorMessage extends Message
{
    public const IDKEY = "vid";
    protected $vid;
    protected $fromuser;
    protected $touser;
    protected $datesent;
    protected $vmtext;
    protected $notifier;

    public function __construct($vid = 0, $dto = null, $notifier = false)
    {
        // Fetch the basic member properties for visitor messages

        $mysidia = Registry::get("mysidia");
        if ($vid == 0) {
            //This is a new visitor message not yet exist in database
            $this->vid = $vid;
            $this->fromuser = $mysidia->user->getID();
        } else {
            // The visitor message is not being composed, so fetch the information from database
            $dto = $mysidia->db->select("visitor_messages", [], "vid = :vid", ["vid" => $vid])->fetchObject();
            if (!is_object($dto)) {
                throw new InvalidIDException("view_nonexist");
            }
        }
        parent::__construct($dto);
        if ($notifier == true) {
            $this->getNotifier();
        }
    }

    protected function createFromDTO($dto)
    {
        parent::createFromDTO($dto);
        $this->datesent = new Date($dto->datesent);
    }

    public function getTitle()
    {
        // This is a Visitor Message, so there is not a title property for now. May consider adding in future.
        return "N/A";
    }

    public function getContent()
    {
        if (!empty($this->vmtext)) {
            return $this->vmtext;
        } else {
            return "";
        }
    }

    public function getnotifier()
    {
        if (is_object($this->notifier)) {
            throw new InvalidActionException("A VM Notifier already exists...");
        } else {
            $this->notifier = new VMNotifier();
        }
    }

    public function post(Member $recipient, $text)
    {
        $mysidia = Registry::get("mysidia");
        if ($this->vid != 0) {
            return false;
        } else {
            $date = new Date();
            $mysidia->db->insert("visitor_messages", ["vid" => null, "fromuser" => $this->fromuser, "touser" => $recipient->getID(), "datesent" => $date->format("Y-m-d H:i:s"), "vmtext" => $text]);
        }
        return true;
    }

    public function edit($text)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("visitor_messages", ["vmtext" => $text], "vid='{$this->vid}' AND fromuser='{$this->fromuser}'");
    }

    public function remove()
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->delete("visitor_messages", "vid='{$this->vid}' AND touser='{$this->touser}'");
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("visitor_messages", [$field => $value], "id='{$this->vid}'");
    }
}
