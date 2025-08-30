<?php

namespace Model\DomainModel;

use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;
use Resource\Utility\Date;

class ShoutComment extends Model
{
    protected $id;
    protected $user;
    protected $date;
    protected $comment;

    public function __construct($id, $dto = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$dto) {
            $dto = $mysidia->db->select("shoutbox", [], "id = :id", ["id" => $id])->fetchObject();
            if (!is_object($dto)) {
                throw new InvalidIDException("Shoutbox Comment {$id} does not exist...");
            }
        }
        parent::__construct($dto);
    }

    protected function createFromDTO($dto)
    {
        parent::createFromDTO($dto);
        $this->date = new Date($dto->date);
    }

    public function getUser($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) {
            return new Member($this->user);
        } else {
            return $this->user;
        }
    }

    public function getUsername()
    {
        if (!$this->user) {
            return "Guest";
        }
        return $this->getUser(Model::MODEL)->getUsername();
    }

    public function getDate($format = null)
    {
        return $format ? $this->date->format($format) : $this->date;
    }

    public function getComment()
    {
        return $this->comment;
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("shoutbox", [$field => $value], "id='{$this->id}'");
    }
}
