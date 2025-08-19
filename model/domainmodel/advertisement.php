<?php

namespace Model\DomainModel;

use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;
use Resource\Native\MysString;
use Resource\Utility\Date;

class Advertisement extends Model
{

    protected $id;
    protected $adname;
    protected $text;
    protected $page;
    protected $impressions;
    protected $actualimpressions;
    protected $date;
    protected $status;
    protected $user;
    protected $extra;

    public function __construct($adinfo, $dto = null)
    {
        $mysidia = Registry::get("mysidia");
        if ($adinfo instanceof MysString) $adinfo = $adinfo->getValue();
        if (!$dto) {
            $whereClause = is_numeric($adinfo) ? "id = :adinfo" : "adname = :adname";
            $dto = $mysidia->db->select("ads", [], $whereClause, ["adinfo" => $adinfo])->fetchObject();
            if (!is_object($dto)) throw new InvalidIDException("Ad {$adinfo} does not exist...");
        }
        parent::__construct($dto);
    }

    protected function createFromDTO($dto)
    {
        parent::createFromDTO($dto);
        $this->date = new Date($dto->date);
    }

    public function getAdName()
    {
        return $this->adname;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getImpressions()
    {
        return $this->impressions;
    }

    public function getActualImpressions()
    {
        return $this->actualimpressions;
    }

    public function getDate($format = null)
    {
        return $format ? $this->date->format($format) : $this->date;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getUser($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) return new Member($this->user);
        else return $this->user;
    }

    public function isExtra()
    {
        return $this->extra;
    }

    public function addImpressions($numImpression)
    {
        $this->impressions += $numImpression;
    }

    public function addActualImpressions($numImpression)
    {
        $this->actualimpressions += $numImpression;
    }

    public function syncImpressions()
    {
        $this->impressions = $this->actualimpressions;
    }

    public function isOnUser($userID)
    {
        return ($this->user == $userID);
    }

    public function isActive()
    {
        return ($this->actualimpressions < $this->impressions || $this->impressions == 0);
    }

    public function updateImpressions()
    {
        if (!$this->impressions) $this->impressions = 0;
        $this->actualimpressions++;
        $this->save("actualimpressions", $this->actualimpressions);
        if (!$this->isActive()) {
            $this->status = "inactive";
            $this->save("status", $this->status);
        }
    }

    public function updateStatus($status)
    {
        $this->status = $status;
        $this->save("status", $this->status);
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("ads", [$field => $value], "id='{$this->id}'");
    }
}
