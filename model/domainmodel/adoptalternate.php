<?php

namespace Model\DomainModel;

use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\GUI\Component\Image;

class AdoptAlternate extends Model
{
    public const IDKEY = "alid";
    protected $alid;
    protected $adopt;
    protected $image;
    protected $level;
    protected $item;
    protected $gender;
    protected $lastalt;
    protected $chance;

    public function __construct($altInfo)
    {
        $mysidia = Registry::get("mysidia");
        $dto = is_object($altInfo) ? $altInfo : $mysidia->db->select("alternates", [], "alid = :alid", ["alid" => $altInfo])->fetchObject();
        if (!is_object($dto)) {
            throw new AlternateNotfoundException("The alternate form {$altInfo} does not exist...");
        }
        parent::__construct($dto);
    }

    public function getALID()
    {
        return $this->alid;
    }

    public function getAdopt($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) {
            return new Adoptable($this->adopt);
        } else {
            return $this->adopt;
        }
    }

    public function getImage($fetchMode = "")
    {
        if ($fetchMode == Model::GUI) {
            return new Image($this->image);
        }
        return $this->image;
    }

    public function getLevel($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) {
            return new Level($this->adopt, $this->level);
        } else {
            return $this->level;
        }
    }

    public function getItem()
    {
        return $this->item;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function getLastAlt($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) {
            return new static($this->lastalt);
        }
        return $this->lastalt;
    }

    public function getChance()
    {
        return $this->chance;
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("alternates", [$field => $value], "alid='{$this->alid}'");
    }
}
