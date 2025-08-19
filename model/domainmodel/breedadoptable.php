<?php

namespace Model\DomainModel;

use Resource\Core\Model;
use Resource\Core\Registry;

class BreedAdoptable extends Adoptable
{
    public const IDKEY = "bid";
    protected $bid;
    protected $offspring;
    protected $parent;
    protected $mother;
    protected $father;
    protected $probability;
    protected $survival;
    protected $level;
    protected $available;

    public function __construct($bid, $dto = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$dto) {
            $dto = $mysidia->db->join("adoptables", "adoptables.id = breeding.offspring")
                ->select("breeding", [], "bid = :bid", ["bid" => $bid])->fetchObject();
            if (!is_object($dto)) {
                throw new AdoptNotfoundException("Adoptable's breeding scenario does not exist...");
            }
        }
        $this->createFromDTO($dto);
    }

    public function getBreedID()
    {
        return $this->bid;
    }

    public function getOffspring($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) {
            return new Adoptable($this->offspring);
        }
        return $this->offspring;
    }

    public function getOffspringType()
    {
        $offspring = $this->getOffspring(Model::MODEL);
        return $offspring ? $offspring->getType() : null;
    }

    public function getParent($fetchMode = "")
    {
        if ($this->parent && $fetchMode == Model::MODEL) {
            return new Adoptable($this->parent);
        } else {
            return $this->parent;
        }
    }

    public function getParentType()
    {
        $parent = $this->getParent(Model::MODEL);
        return $parent ? $parent->getType() : null;
    }

    public function getMother($fetchMode = "")
    {
        if ($this->mother && $fetchMode == Model::MODEL) {
            return new Adoptable($this->mother);
        } else {
            return $this->mother;
        }
    }

    public function getMotherType()
    {
        $mother = $this->getMother(Model::MODEL);
        return $mother ? $mother->getType() : null;
    }

    public function getFather($fetchMode = "")
    {
        if ($this->father && $fetchMode == Model::MODEL) {
            return new Adoptable($this->father);
        } else {
            return $this->father;
        }
    }

    public function getFatherType()
    {
        $father = $this->getFather(Model::MODEL);
        return $father ? $father->getType() : null;
    }

    public function getProbability()
    {
        return $this->probability;
    }

    public function getSurvivalRate()
    {
        return $this->survival;
    }

    public function getRequiredLevel()
    {
        return $this->level;
    }

    public function isAvailable()
    {
        return $this->available;
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("breeding", [$field => $value], "bid='{$this->bid}'");
    }
}
