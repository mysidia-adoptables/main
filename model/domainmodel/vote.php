<?php

namespace Model\DomainModel;

use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;
use Resource\Utility\Date;

class Vote extends Model
{
    public const IDKEY = "void";
    protected $void;
    protected $adoptableid;
    protected $userid;
    protected $ip;
    protected $date;

    protected $user;

    public function __construct($adopt = null, $user = null, $ip = null, Date|null $date = null, $dto = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$dto) {
            $whereClause = "1";
            if ($adopt) {
                $whereClause .= " AND adoptableid = '{$adopt}'";
            }
            if ($user) {
                $whereClause .= " AND userid = '{$user}'";
            }
            if ($ip) {
                $whereClause .= "AND ip = '{$ip}'";
            }
            if ($date) {
                $whereClause .= "AND date = {$date->format('Y-m-d')}";
            }
            $dto = $mysidia->db->select("vote_voters", [], $whereClause)->fetchObject();
            if (!is_object($dto)) {
                throw new InvalidIDException("The adoptable's vote/click record does not exist...");
            }
        }
        parent::__construct($dto);
    }

    protected function createFromDTO($dto)
    {
        parent::createFromDTO($dto);
        $this->date = new Date($dto->date);
    }

    public function getAdopt($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) {
            return new OwnedAdoptable($this->adoptableid);
        } else {
            return $this->adoptableid;
        }
    }

    public function getUser($fetchMode = "")
    {
        if (!$this->userid) {
            return null;
        }
        if ($fetchMode == Model::MODEL) {
            if (!$this->user) {
                $this->user = new Member($this->userid);
            }
            return $this->user;
        }
        return $this->userid;
    }

    public function getUserID()
    {
        return $this->userid;
    }

    public function getUsername()
    {
        if (!$this->userid) {
            return "Guest";
        }
        return $this->getUser(Model::MODEL)->getUsername();
    }

    public function getDate($format = null)
    {
        return $format ? $this->date->format($format) : $this->date;
    }

    public function getIP()
    {
        return $this->ip;
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("vote_voters", [$field => $value], "void='{$this->id}'");
    }
}
