<?php

namespace Model\DomainModel;

use Resource\Collection\ArrayList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\GUI\Component\Image;
use Resource\Native\MysString;

class Adoptable extends Model
{

    protected $id;
    protected $type;
    protected $class;
    protected $description;
    protected $eggimage;
    protected $whenisavail;
    protected $alternates;
    protected $altoutlevel;
    protected $shop;
    protected $cost;

    protected $conditions;
    protected $levels;

    public function __construct($adoptinfo, $dto = null)
    {
        $mysidia = Registry::get("mysidia");
        if ($adoptinfo instanceof MysString) $adoptinfo = $adoptinfo->getValue();
        if (!$dto) {
            $whereClause = is_numeric($adoptinfo) ? "id = :adoptinfo" : "type = :adoptinfo";
            $dto = $mysidia->db->select("adoptables", [], $whereClause, ["adoptinfo" => $adoptinfo])->fetchObject();
            if (!is_object($dto)) throw new AdoptNotfoundException("Adoptable {$adoptinfo} does not exist...");
        }
        parent::__construct($dto);
    }

    public function getType()
    {
        return $this->type;
    }

    public function getBreedId()
    {
        return $this->adopt;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getDescription()
    {
        return stripslashes((string) $this->description);
    }

    public function getEggImage($fetchMode = "")
    {
        if ($fetchMode == Model::GUI) return new Image($this->eggimage);
        return $this->eggimage;
    }

    public function getWhenAvailable()
    {
        return $this->whenisavail;
    }

    public function getAlternates()
    {
        return $this->alternates;
    }

    public function getAltLevel()
    {
        return $this->altoutlevel;
    }

    public function getShop($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) return new AdoptShop($this->shop);
        else return $this->shop;
    }

    public function getCost()
    {
        return $this->cost;
    }

    public function getConditions()
    {
        if (!$this->conditions) $this->conditions = new AdoptCondition($this);
        return $this->conditions;
    }

    public function getLevel($level)
    {
        if (!$this->levels) return new Level($this->id, $level);
        return $this->levels->get($level);
    }

    public function getLevels($includeEgg = true)
    {
        if (!$this->levels) {
            $mysidia = Registry::get("mysidia");
            $this->levels = new ArrayList;
            $whereClause = "adopt = '{$this->id}'";
            if (!$includeEgg) $whereClause .= " AND level != 0";
            $whereClause .= " ORDER BY level ASC";
            $num = $mysidia->db->select("levels", ["level"], $whereClause)->rowCount();
            $i = $includeEgg ? 0 : 1;
            while ($i <= $num) {
                $this->levels->add(new Level($this->id, $i));
                $i++;
            }
        }
        return $this->levels;
    }

    public function getAlternatesForLevel($level)
    {
        $mysidia = Registry::get("mysidia");
        $alternateModels = new ArrayList;
        $stmt = $mysidia->db->select("alternates", [], "adopt = :adopt AND level = :level", ["adopt" => $this->id, "level" => $level]);
        while ($alternate = $stmt->fetchObject()) {
            $alternateModels->add(new AdoptAlternate($alternate));
        }
        return $alternateModels;
    }

    public function countAlternatesForLevel($level)
    {
        $mysidia = Registry::get("mysidia");
        $stmt = $mysidia->db->select("alternates", [], "adopt = :adopt AND level = :level", ["adopt" => $this->id, "level" => $level]);
        return $stmt->rowCount();
    }

    protected function canUseAlternate($level)
    {
        if ($this->alternates == "enabled" && $level >= $this->altoutlevel) return true;
        if ($this->countAlternatesForLevel($level) > 0) return true;
        return false;
    }

    public function getMaxLevel()
    {
        return $this->levels->count();
    }

    public function getGender()
    {
        $genders = ['f', 'm'];
        return $genders[random_int(0, 1)];
    }

    public function makeOwnedAdopt($owner, $name = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$name) $name = $this->type;
        $code = $this->generateCode();
        $gender = $this->getGender();
        $mysidia->db->insert("owned_adoptables", ["aid" => null, "adopt" => $this->id, "name" => $name, "owner" => $owner, "currentlevel" => 0,
            "totalclicks" => 0, "code" => $code, "imageurl" => "", "alternate" => 0, "tradestatus" => "fortrade",
            "isfrozen" => "no", "gender" => $gender, "offsprings" => 0, "lastbred" => 0]);
        $aid = $mysidia->db->lastInsertId();
        if (!$aid) return null;
        return new OwnedAdoptable($aid);
    }

    public function updateEggImage($eggImage)
    {
        $this->eggimage = $eggImage;
        $this->save("eggimage", $this->eggimage);
    }

    public function resetDefaultConditions()
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("adoptables", ["whenisavail" => 'always'], "id = '{$this->id}'");
        $mysidia->db->update("adoptables_conditions", ["whenisavail" => 'always'], "id = '{$this->id}'");
    }

    public function enableDateConditions($date)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("adoptables", ["whenisavail" => "conditions"], "id = '{$this->id}'");
        $mysidia->db->update("adoptables_conditions", ["whenisavail" => "conditions", "datecond" => "enabled", "date" => $date], "id = '{$this->id}'");
    }

    public function changeShopSettings($shop, $cost = 0)
    {
        $this->shop = $shop;
        $this->cost = $cost;
        $this->save("shop", $this->shop);
        $this->save("cost", $this->cost);
    }

    public function changeAltSettings($altStatus, $altLevel)
    {
        $this->alternates = $altStatus;
        $this->altoutlevel = $altLevel;
        $this->save("alternates", $this->alternates);
        $this->save("altoutlevel", $this->altoutlevel);
    }

    public function delete($method)
    {
        return ($method == "hard") ? $this->hardDelete() : $this->softDelete();
    }

    private function softDelete()
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("owned_adoptables", ["currentlevel" => 1], "id = '{$this->id}' AND currentlevel = 0");
        $mysidia->db->delete("levels", "adopt = '{$this->id}' AND level = 0");
        $mysidia->db->update("adoptables", ["whenisavail" => "promo"], "id = '{$this->id}'");
        $mysidia->db->update("adoptables_conditions", ["whenisavail" => "promo"], "id = '{$this->id}'");
        return true;
    }

    private function hardDelete()
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->delete("adoptables", "id = '{$this->id}'");
        $mysidia->db->delete("adoptables_conditions", "id = '{$this->id}'");
        $mysidia->db->delete("alternates", "adopt = '{$this->id}'");
        $mysidia->db->delete("levels", "adopt = '{$this->id}'");
        $mysidia->db->delete("owned_adoptables", "adopt = '{$this->id}'");
        return true;
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("adoptables", [$field => $value], "id='{$this->id}'");
    }

    public function __toString(): string
    {
        return (string) $this->type;
    }
}
