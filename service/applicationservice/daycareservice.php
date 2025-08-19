<?php

namespace Service\ApplicationService;

use Model\DomainModel\OwnedAdoptable;
use Model\Settings\DaycareSettings;
use Resource\Collection\ArrayList;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Native\MysObject;

class DaycareService extends MysObject
{

    private $adopts;
    private $total;
    private $settings;
    private $pagination = false;

    public function __construct()
    {
        $mysidia = Registry::get("mysidia");
        $this->settings = new DaycareSettings($mysidia->db);
        if ($this->settings->system == "disabled") throw new DaycareException("system");
    }

    public function getAdopts()
    {
        if (!$this->adopts) {
            $mysidia = Registry::get("mysidia");
            $conditions = $this->getConditions();
            $fetchMode = $this->getFetchMode($conditions);
            $stmt = $mysidia->db->join("adoptables", "adoptables.id = owned_adoptables.adopt")
                ->select("owned_adoptables", [], $conditions . $fetchMode);
            $this->total = $stmt->rowCount();
            if ($this->total == 0) throw new DaycareException("empty");
            $this->adopts = new ArrayList;
            while ($dto = $stmt->fetchObject()) {
                $this->adopts->add(new OwnedAdoptable($dto->aid, $dto));
            }
        }
        return $this->adopts;
    }

    private function getConditions()
    {
        $mysidia = Registry::get("mysidia");
        $conditions = "isfrozen != 'yes'";
        if (is_numeric($this->settings->level)) $conditions .= " AND currentlevel <= '{$this->settings->level}'";
        if ($this->settings->species) {
            foreach ($this->settings->species as $species) {
                $conditions .= " AND type != '{$species}'";
            }
        }
        if ($this->settings->owned != "yes") $conditions .= " AND owner != '{$mysidia->user->getID()}'";
        return $conditions;
    }

    private function getFetchMode($conditions)
    {
        $mysidia = Registry::get("mysidia");
        if ($this->settings->display == "all") {
            $total = $mysidia->db->select("owned_adoptables", ["aid"], $conditions)->rowCount();
            $this->pagination = new Pagination($total, $this->settings->number, "levelup/daycare", $mysidia->input->get("page"));
            $fetchMode = " ORDER BY currentlevel LIMIT {$this->pagination->getLimit()},{$this->pagination->getRowsperPage()}";
        } else $fetchMode = " ORDER BY RAND() DESC LIMIT {$this->settings->number}";
        return $fetchMode;
    }

    public function getTotalAdopts()
    {
        if (!$this->total) $this->getAdopts();
        return $this->total;
    }

    public function getTotalRows()
    {
        return ceil($this->total / $this->settings->columns);
    }

    public function getTotalColumns()
    {
        return ($this->total < $this->settings->columns) ? $this->total : $this->settings->columns;
    }

    public function getPagination()
    {
        return $this->pagination;
    }

    public function getStats($adopt)
    {
        $stats = "";
        foreach ($this->settings->info as $info) {
            $method = "get{$info}";
            $stats .= "{$info}: {$adopt->$method()}<br>";
        }
        return $stats;
    }
}
