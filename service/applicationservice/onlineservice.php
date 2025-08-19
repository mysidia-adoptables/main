<?php

namespace Service\ApplicationService;

use Exception;
use Model\DomainModel\Member;
use Resource\Collection\ArrayList;
use Resource\Core\Registry;
use Resource\Native\MysObject;
use Resource\Utility\Date;

class OnlineService extends MysObject
{

    protected $members;
    protected $visitors;

    public function __construct($fetchmode = "all")
    {
        switch ($fetchmode) {
            case "members":
                $this->countOnlineMembers();
                break;
            case "visitors":
                $this->countOnlineVisitors();
                break;
            case "all":
                $this->countOnlineMembers();
                $this->countOnlineVisitors();
                break;
            default:
                throw new Exception("Undefined fetchmode specified");
        }
    }

    public function getOnlineMembers()
    {
        $mysidia = Registry::get("mysidia");
        $prefix = constant("PREFIX");
        $stmt = $mysidia->db->join("users", "users.username = online.username")
            ->join("users_profile", "users_profile.uid = users.uid")
            ->select("online", [], "{$prefix}online.username != 'Guest'");
        $members = new ArrayList;
        while ($dto = $stmt->fetchObject()) {
            $members->add(new Member($dto->uid, $dto, true));
        }
        return $members;
    }

    public function countOnlineMembers()
    {
        $mysidia = Registry::get("mysidia");
        if (empty($this->members)) {
            $this->members = $mysidia->db->select("online", ["username"], "username != 'Guest'")->rowCount();
        }
        return $this->members;
    }

    public function countOnlineVisitors()
    {
        $mysidia = Registry::get("mysidia");
        if (empty($this->visitors)) {
            $this->visitors = $mysidia->db->select("online", ["username"], "username = 'Guest'")->rowCount();
        }
        return $this->visitors;
    }

    public function getTotal()
    {
        return $this->countOnlineMembers() + $this->countOnlineVisitors();
    }

    public function update()
    {
        $mysidia = Registry::get("mysidia");
        $session = $mysidia->session->getid();
        $date = new Date;
        $currenttime = $date->getTimestamp();
        $expiretime = $date->modify("-5 minutes")->getTimestamp();
        $username = $mysidia->user->isloggedin() ? $mysidia->user->getUsername() : "Guest";
        $ip = $_SERVER['REMOTE_ADDR'];
        $userexist = $mysidia->db->select("online", ["username"], "username = '{$username}' AND ip = '{$ip}'")->fetchColumn();
        if (!$userexist) $mysidia->db->insert("online", ["username" => $username, "ip" => $ip, "session" => $session, "time" => $currenttime]);
        else $mysidia->db->update("online", ["time" => $currenttime, "session" => $session, "username" => $username], "username = '{$username}' and ip = '{$ip}'");
        $mysidia->db->delete("online", "time < {$expiretime}");
    }
}
