<?php

namespace Model\DomainModel;

use Resource\Core\Registry;
use Resource\Exception\UnsupportedOperationException;
use Resource\Utility\Date;

class Visitor extends User
{
    public function __construct()
    {
        $this->uid = 0;
        $this->username = "Guest";
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->usergroup = new Usergroup("visitors");
        $this->lastActivity = new Date();
    }

    public function isCurrentUser()
    {
        $mysidia = Registry::get("mysidia");
        return ($this->ip == $mysidia->user->getIP());
    }

    public function isLoggedIn()
    {
        return false;
    }

    public function isAdmin()
    {
        return false;
    }

    public function isBanned()
    {
        return false;
    }

    public function getTheme()
    {
        $mysidia = Registry::get("mysidia");
        return $mysidia->settings->theme;
    }

    public function getVotes(Date|null $time = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$time) {
            $time = new Date();
        }
        $numVotes = $mysidia->db->select("vote_voters", ["void"], "ip = '{$this->ip}' and date = '{$time->format('Y-m-d')}'")->rowCount();
        return $numVotes;
    }

    public function reset($resetCode, $tempLength = 12)
    {
        $mysidia = Registry::get("mysidia");
        $tempPass = $this->generateCode($tempLength);
        $mysidia->db->delete("passwordresets", "code= :code", ["code" => $resetCode]);
        return $tempPass;
    }

    protected function save($field, $value): never
    {
        throw new UnsupportedOperationException("Cannot save user data, this is not a member.");
    }
}
