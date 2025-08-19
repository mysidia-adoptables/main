<?php

namespace Model\DomainModel;

use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;
use Resource\Native\MysString;

class Usergroup extends Model
{

    // The usergroup class, what should I say? umm... Its temporary anyway as the usergroup system will be revised in Mys v1.4.0's new ACP project.
    const IDKEY = "gid";
    protected $gid = 0;
    protected $groupname;
    protected $canadopt;
    protected $canpm;
    protected $cancp;
    protected $canmanageadopts;
    protected $canmanagecontent;
    protected $canmanageads;
    protected $canmanagesettings;
    protected $canmanageusers;

    public function __construct($group, $dto = null)
    {
        // Fetch the basic properties for usergroup

        $mysidia = Registry::get("mysidia");
        if (empty($group)) $group = "visitors";
        elseif ($group instanceof MysString) $group = $group->getValue();

        if (!$dto) {
            $whereclause = is_numeric($group) ? "gid = :group" : "groupname = :group";
            $dto = $mysidia->db->select("groups", [], $whereclause, ["group" => $group])->fetchObject();
            if (!is_object($dto)) return;
        }
        parent::__construct($dto);
    }

    public function getGroupname()
    {
        return $this->groupname;
    }

    public static function fetchgroup($groupname)
    {
        $mysidia = Registry::get("mysidia");
        $usergroup = $mysidia->db->select("groups", [], "groupname ='{$groupname}'")->fetchObject();
        return $usergroup;
    }

    public function getPermission($perms)
    {
        if (isset($this->$perms)) return $this->$perms;
        else throw new InvalidIDException('The permission name does not exist, something must be very very wrong');
    }

    public function setPermission($fields = [])
    {
        $mysidia = Registry::get("mysidia");
        if (!$this->isAssoc($fields)) throw new InvalidIDException('The parameter must be an associative array...');
        $mysidia->db->update("groups", $fields, "gid ='{$this->gid}'");
    }

    public function hasPermission($perms)
    {
        return ($this->getPermission($perms) == "yes");
    }

    public function deletegroup()
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->delete("groups", "gid ='{$this->gid}'");
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("groups", [$field => $value], "gid='{$this->id}'");
    }

    public function __toString(): string
    {
        return (string) $this->groupname;
    }
}
