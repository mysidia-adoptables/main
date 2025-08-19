<?php

namespace Model\DomainModel;

use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\GUI\Component\Image;
use Resource\Utility\Date;

class OwnedAdoptable extends Adoptable
{
    public const IDKEY = "aid";
    protected $aid;
    protected $name;
    protected $owner;
    protected $currentlevel;
    protected $totalclicks;
    protected $code;
    protected $imageurl;
    protected $alternate;
    protected $tradestatus;
    protected $isfrozen;
    protected $gender;
    protected $offsprings;
    protected $lastbred;

    protected $nextlevel;
    protected $voters;

    public function __construct($aid, $dto = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$dto) {
            $dto = $mysidia->db->join("adoptables", "adoptables.id = owned_adoptables.adopt")
                ->select("owned_adoptables", [], "aid = :aid", ["aid" => $aid])->fetchObject();
            if (!is_object($dto)) {
                throw new AdoptNotfoundException("Owned Adoptable ID {$aid} does not exist or does not belong to the owner specified...");
            }
        }
        $this->createFromDTO($dto);
    }

    protected function createFromDTO($dto)
    {
        parent::createFromDTO($dto);
        if ($this->lastbred) {
            $this->lastbred = new Date("@{$dto->lastbred}");
        }
    }

    public function getAdoptID()
    {
        return $this->aid;
    }

    public function getSpecies()
    {
        return new Adoptable($this->id);
    }

    public function getSpeciesID()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) {
            $this->save("name", $name);
        }
        $this->name = $name;
    }

    public function getOwner($fetchMode = "")
    {
        if (!$this->owner) {
            return null;
        }
        if ($fetchMode == Model::MODEL) {
            return new Member($this->owner);
        }
        return $this->owner;
    }

    public function setOwner($owner, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) {
            $this->save("owner", $owner);
        }
        $this->owner = $owner;
    }

    public function isOwner(User $user = null)
    {
        if (!$user || !($user instanceof Member)) {
            return false;
        }
        return ($this->owner == $user->getID());
    }

    public function isOwnerID($userID = 0)
    {
        if (!$userID) {
            return false;
        }
        return ($this->owner == $userID);
    }

    public function getOwnerName()
    {
        if (!$this->owner) {
            return null;
        }
        return $this->getOwner(Model::MODEL)->getUsername();
    }

    public function getCurrentLevel($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) {
            return new Level($this->id, $this->currentlevel);
        } else {
            return $this->currentlevel;
        }
    }

    public function setCurrentLevel($level, $assignMode = "")
    {
        $this->currentlevel = $level;
        if ($assignMode == Model::UPDATE) {
            $this->save("currentlevel", $this->currentlevel);
            $this->updateAlternate($assignMode);
        }
    }

    public function getTotalClicks()
    {
        return $this->totalclicks;
    }

    public function setTotalClicks($clicks, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) {
            $this->save("totalclicks", $clicks);
        }
        $this->totalclicks = $clicks;
    }

    public function setLevelAndClicks($level, $clicks, $assignMode = "")
    {
        $this->setCurrentLevel($level, $assignMode);
        $this->setTotalClicks($clicks, $assignMode);
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getImageURL($fetchMode = "")
    {
        if ($fetchMode == Model::GUI) {
            return new Image($this->imageurl);
        } else {
            return $this->imageurl;
        }
    }

    public function getAlternate($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) {
            return new AdoptAlternate($this->alternate);
        }
        return $this->alternate;
    }

    public function setAlternate(AdoptAlternate $alternate, $assignMode = "")
    {
        $this->imageurl = $alternate->getImage();
        $this->alternate = $alternate->getALID();
        if ($assignMode == Model::UPDATE) {
            $this->save("imageurl", $this->imageurl);
            $this->save("alternate", $this->alternate);
        }
    }

    public function updateAlternate($assignMode = "")
    {
        $alternate = $this->generateAlternate();
        if ($alternate) {
            $this->imageurl = $alternate->getImage();
            $this->alternate = $alternate->getALID();
        } else {
            $this->imageurl = null;
            $this->alternate = 0;
        }

        if ($assignMode == Model::UPDATE) {
            $this->save("imageurl", $this->imageurl);
            $this->save("alternate", $this->alternate);
        }
    }

    public function getTradeStatus()
    {
        return $this->tradestatus;
    }

    public function setTradeStatus($status, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) {
            $this->save("tradestatus", $status);
        }
        $this->tradestatus = $status;
    }

    public function isFrozen()
    {
        return $this->isfrozen;
    }

    public function setFrozen($frozen = true, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) {
            $this->save("isfrozen", $frozen);
        }
        $this->isfrozen = $frozen;
    }

    public function getGender($fetchMode = "")
    {
        if ($fetchMode == Model::GUI) {
            return new Image("picuploads/{$this->gender}.png");
        } else {
            return $this->gender;
        }
    }

    public function getOffsprings()
    {
        return $this->offsprings;
    }

    public function setOffsprings($offsprings = 1, $assignMode = "")
    {
        $this->offsprings = $offsprings;
        if ($assignMode == Model::UPDATE) {
            $this->save("offsprings", $this->offsprings);
        }
    }

    public function getLastBred($format = null)
    {
        return $format ? $this->lastbred->format($format) : $this->lastbred;
    }

    public function setLastBred($lastBred = 0, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) {
            $this->save("lastbred", $lastBred);
        }
        if ($lastBred > 0) {
            $this->lastbred = new Date("@{$lastBred}");
        }
    }

    public function getImage($fetchMode = "")
    {
        if ($this->imageurl) {
            return $this->getImageUrl($fetchMode);
        }
        if ($this->currentlevel == 0) {
            return $this->getEggImage($fetchMode);
        }

        if ($this->alternate == 0) {
            $level = $this->getCurrentLevel("model");
            return $level->getPrimaryImage($fetchMode);
        } else {
            $alternate = $this->getAlternate("model");
            return $alternate->getImage($fetchMode);
        }
    }

    public function hasNextLevel()
    {
        try {
            $this->nextlevel = new Level($this->id, $this->currentlevel + 1);
            return true;
        } catch (LevelNotfoundException) {
            return false;
        }
    }

    public function getNextLevel()
    {
        if (!$this->nextlevel) {
            return false;
        }
        return $this->nextlevel;
    }

    public function getLevelupClicks()
    {
        if (!$this->nextlevel) {
            return false;
        }
        return $this->nextlevel->getRequiredClicks() - $this->totalclicks;
    }

    public function generateAlternate()
    {
        if (!$this->canUseAlternate($this->currentlevel)) {
            return;
        }
        $alternateModels = $this->filterAlternates();
        $totalChance = 100;
        $currentNumber = 0;
        $winningNumber = random_int($currentNumber, $totalChance - 1);
        foreach ($alternateModels as $alternateModel) {
            $nextNumber = (int)$alternateModel->getChance() + $currentNumber;
            if ($winningNumber >= $currentNumber && $winningNumber < $nextNumber) {
                return $alternateModel;
            }
            $currentNumber = $nextNumber;
        }
    }

    private function filterAlternates()
    {
        $alternateModels = $this->getAlternatesForLevel($this->currentlevel);
        $filterGender = ($this->gender == "f") ? "female" : "male";
        $filterALID = $this->alternate;
        $filterClosure = (fn ($alternateModel) => !$alternateModel->getItem() && ($alternateModel->getGender() == "both" || $alternateModel->getGender() == $filterGender) && (!$alternateModel->getLastAlt() || $alternateModel->getLastAlt() == $filterALID));
        return array_filter($alternateModels->toArray(), $filterClosure);
    }

    public function hasVoter($user, Date $date = null)
    {
        if (!$date) {
            $date = new Date();
        }
        $mysidia = Registry::get("mysidia");

        if ($user instanceof Member) {
            $whereClause = "adoptableid = '{$this->aid}' AND userid = '{$user->getID()}' AND date = '{$date->format('Y-m-d')}'";
        } else {
            $ip = $mysidia->secure($_SERVER['REMOTE_ADDR']);
            $whereClause = "adoptableid = '{$this->aid}' AND ip = '{$ip}' AND date = '{$date->format('Y-m-d')}'";
        }

        $void = $mysidia->db->select("vote_voters", ["void"], $whereClause)->fetchColumn();
        if (is_numeric($void)) {
            return true;
        } else {
            return false;
        }
    }

    public function pound()
    {
        $mysidia = Registry::get("mysidia");
        if (!$this->owner) {
            throw new InvalidActionException("The adoptable is already pounded");
        }
        $this->updatePound();
        $this->owner = 0;
        $mysidia->db->update("owned_adoptables", ["owner" => $this->owner], "aid = '{$this->aid}'");
    }

    protected function updatePound()
    {
        $mysidia = Registry::get("mysidia");
        $today = new Date();
        $mysidia->db->insert("pounds", ["aid" => $this->aid, "firstowner" => $this->owner, "lastowner" => $this->owner,
            "currentowner" => 0, "recurrence" => 1, "datepound" => $today->format('Y-m-d'), "dateadopt" => null]);
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("owned_adoptables", [$field => $value], "aid = '{$this->aid}'");
    }
}
