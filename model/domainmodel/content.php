<?php

namespace Model\DomainModel;

use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;
use Resource\Native\MysString;
use Resource\Utility\Date;

class Content extends Model
{

    const IDKEY = "cid";
    protected $cid;
    protected $page;
    protected $title;
    protected $date;
    protected $content;
    protected $level;
    protected $code;
    protected $item;
    protected $time;
    protected $group;

    public function __construct($contentinfo, $dto = null)
    {
        $mysidia = Registry::get("mysidia");
        if ($contentinfo instanceof MysString) $contentinfo = $contentinfo->getValue();
        if (!$dto) {
            $whereClause = is_numeric($contentinfo) ? "cid = :contentinfo" : "page = :contentinfo";
            $dto = $mysidia->db->select("content", [], $whereClause, ["contentinfo" => $contentinfo])->fetchObject();
            if (!is_object($dto)) throw new InvalidIDException("Custom Page {$contentinfo} does not exist...");
        }
        parent::__construct($dto);
    }

    protected function createFromDTO($dto)
    {
        parent::createFromDTO($dto);
        $this->date = new Date($dto->date);
        if ($this->time) $this->time = new Date($this->time);
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDate($format = null)
    {
        return $format ? $this->date->format($format) : $this->date;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function getCode($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) return new Promocode($this->code);
        else return $this->code;
    }

    public function getItem($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) return new Item($this->item);
        else return $this->item;
    }

    public function getTime($format = null)
    {
        if (!$this->time) return null;
        return $format ? $this->time->format($format) : $this->time;
    }

    public function getGroup($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) return new Usergroup($this->group);
        else return $this->group;
    }

    public function hasDisplayConditions()
    {
        return ($this->code || $this->item || $this->time || $this->group);
    }

    public function updateDocument($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("adoptables", [$field => $value], "cid='{$this->cid}'");
    }
}
