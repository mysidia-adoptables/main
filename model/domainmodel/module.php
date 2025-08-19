<?php

namespace Model\DomainModel;

use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;
use Resource\Exception\UnsupportedOperationException;
use Resource\Native\MysString;
use Resource\Native\Objective;
use Resource\Utility\Comparable;

class Module extends Model implements Comparable
{
    public const IDKEY = "moid";
    protected $moid;
    protected $widget;
    protected $name;
    protected $subtitle;
    protected $userlevel;
    protected $html;
    protected $php;
    protected $order;
    protected $status;

    public function __construct($moduleinfo, $dto = null)
    {
        $mysidia = Registry::get("mysidia");
        if ($moduleinfo instanceof MysString) {
            $moduleinfo = $moduleinfo->getValue();
        }
        if (!$dto) {
            $whereClause = is_numeric($moduleinfo) ? "moid = :moduleinfo" : "name = :moduleinfo";
            $dto = $mysidia->db->select("modules", [], $whereClause, ["moduleinfo" => $moduleinfo])->fetchObject();
            if (!is_object($dto)) {
                throw new InvalidIDException("Module {$moduleinfo} does not exist...");
            }
        }
        parent::__construct($dto);
    }

    public function getWidget($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) {
            return new Widget($this->widget);
        } else {
            return $this->widget;
        }
    }

    public function getWidgetName()
    {
        return $this->getWidget(Model::MODEL)->getName();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSubtitle()
    {
        return $this->subtitle;
    }

    public function getUserLevel()
    {
        return $this->userlevel;
    }

    public function getHTML()
    {
        return $this->html;
    }

    public function getPHP()
    {
        return $this->php;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function compareTo(Objective $object)
    {
        if (!($object instanceof Module)) {
            throw new UnsupportedOperationException("Modules can only be compared with another module.");
        }
        return $this->order - $object->getOrder();
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("modules", [$field => $value], "moid='{$this->moid}'");
    }
}
