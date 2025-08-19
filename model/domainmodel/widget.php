<?php

namespace Model\DomainModel;

use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;
use Resource\Exception\UnsupportedOperationException;
use Resource\Native\MysString;
use Resource\Native\Objective;
use Resource\Utility\Comparable;

class Widget extends Model implements Comparable
{
    public const IDKEY = "wid";
    protected $wid;
    protected $name;
    protected $controller;
    protected $order;
    protected $status;

    public function __construct($widgetinfo, $dto = null)
    {
        $mysidia = Registry::get("mysidia");
        if ($widgetinfo instanceof MysString) {
            $widgetinfo = $widgetinfo->getValue();
        }
        if (!$dto) {
            $whereClause = is_numeric($widgetinfo) ? "wid = :widgetinfo" : "name = :widgetinfo";
            $dto = $mysidia->db->select("widgets", [], $whereClause, ["widgetinfo" => $widgetinfo])->fetchObject();
            if (!is_object($dto)) {
                throw new InvalidIDException("Widget {$widgetinfo} does not exist...");
            }
        }
        parent::__construct($dto);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getController()
    {
        return $this->controller;
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
        if (!($object instanceof Widget)) {
            throw new UnsupportedOperationException("Widgets can only be compared with another widget.");
        }
        return $this->order - $object->getOrder();
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("widgets", [$field => $value], "wid='{$this->wid}'");
    }
}
