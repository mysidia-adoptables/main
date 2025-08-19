<?php

namespace Model\DomainModel;

use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;

class FriendRequest extends Message
{
    // The core class for Mysidia Adoptables, it loads basic site/user info to initiate the script

    const IDKEY = "fid";
    protected $fid;
    protected $fromuser;
    protected $offermessage;
    protected $touser;
    protected $status;
    protected $notifier;

    public function __construct($fid = 0, $dto = null, $notifier = false)
    {
        // Fetch the basic member properties for private messages

        $mysidia = Registry::get("mysidia");
        if ($fid == 0) {
            //This is a new friend request not yet exist in database
            $this->fid = $fid;
            $this->fromuser = $mysidia->user->getID();
        } else {
            // The friend request exists in database, let's load its information
            if (!$dto) {
                $dto = $mysidia->db->select("friend_requests", [], "fid = :fid", ["fid" => $fid])->fetchObject();
                if (!is_object($dto)) throw new InvalidIDException("The friend request ID does not exist in database.");
            }
            parent::__construct($dto);
            if ($notifier == true) $this->getNotifier();
        }
    }

    public function getTitle()
    {
        // This is a Friend Request, so there is not a title property for now. May consider adding in future.
        return "N/A";
    }

    public function getContent()
    {
        if (!empty($this->offermessage)) return $this->offermessage;
        else return false;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getNotifier()
    {
        if (is_object($this->notifier)) throw new InvalidActionException("A FR Notifier already exists...");
        else $this->notifier = new FRNotifier;
    }

    public function setMessage($offer = "")
    {
        if (empty($offer)) throw new InvalidActionException("The offer message cannot be empty.");
        else $this->offermessage = $offer;
    }

    public function view()
    {
        if ($this->fid == 0) return false;
        else return $this->offermessage;
    }

    public function post($user = "")
    {
        $mysidia = Registry::get("mysidia");
        if ($user) $this->touser = $user;
        if ($this->fid != 0) return false;
        $mysidia->db->insert("friend_requests", ["fid" => null, "fromuser" => $this->fromuser, "offermessage" => $this->offermessage, "touser" => $this->touser, "status" => 'pending']);
        return true;
    }

    public function edit()
    {
        // This feature is currently not available...
        return false;
    }

    public function remove()
    {
        // For a friend request, this method works slightly different than it otherwisw should
        $this->setStatus("canceled");
    }

    public function setStatus($status)
    {
        $validstatus = ["pending", "accepted", "declined", "canceled"];
        if (!in_array($status, $validstatus)) throw new InvalidActionException("Cannot set an empty status.");
        else {
            $this->status = $status;
            $this->save("status", $this->status);
        }
        return true;
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("friend_requests", [$field => $value], "fid='{$this->fid}'");
    }
}
