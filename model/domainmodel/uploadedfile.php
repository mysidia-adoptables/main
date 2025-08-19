<?php

namespace Model\DomainModel;

use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;
use Resource\Native\MysString;

class UploadedFile extends Model
{

    protected $id;
    protected $serverpath;
    protected $wwwpath;
    protected $friendlyname;

    public function __construct($fileinfo, $dto = null)
    {
        $mysidia = Registry::get("mysidia");
        if ($fileinfo instanceof MysString) $fileinfo = $fileinfo->getValue();
        if (!$dto) {
            $whereClause = is_numeric($fileinfo) ? "id = :fileinfo" : "friendlyname = :fileinfo";
            $dto = $mysidia->db->select("filesmap", [], $whereClause, ["fileinfo" => $fileinfo])->fetchObject();
            if (!is_object($dto)) throw new InvalidIDException("Uploaded File {$fileinfo} does not exist...");
        }
        parent::__construct($dto);
    }

    public function getServerPath()
    {
        return $this->serverpath;
    }

    public function getWWWPath()
    {
        return $this->wwwpath;
    }

    public function getFriendlyName()
    {
        return $this->friendlyname;
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("filesmap", [$field => $value], "id='{$this->id}'");
    }
}
