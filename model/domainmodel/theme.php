<?php

namespace Model\DomainModel;

use Exception;
use SplFileObject;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Utility\Date;

class Theme extends Model
{
    protected $id;
    protected $themename;
    protected $themefolder;
    protected $usergroup;
    protected $fromdate;
    protected $todate;

    public function __construct($themeInfo, $dto = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$dto) {
            $whereClause = is_numeric($themeInfo) ? "id = :themeinfo" : "themename = :themeinfo";
            $dto = $mysidia->db->select("themes", [], $whereClause, ["themeinfo" => $themeInfo])->fetchObject();
            if (!is_object($dto)) {
                throw new InvalidIDException("The specified theme does not exist...");
            }
        }
        parent::__construct($dto);
    }

    protected function createFromDTO($dto)
    {
        parent::createFromDTO($dto);
        if ($this->fromdate) {
            $this->fromdate = new Date($this->fromdate);
        }
        if ($this->todate) {
            $this->todate = new Date($this->todate);
        }
    }


    public function getThemename()
    {
        return $this->themename;
    }

    public function getThemeFolder()
    {
        return $this->themefolder;
    }

    public function getUserGroup()
    {
        return $this->usergroup;
    }

    public function getDateFrom($format = null)
    {
        if (!$this->fromdate) {
            return null;
        }
        return $format ? $this->fromdate->format($format) : $this->fromdate;
    }

    public function getDateTo($format = null)
    {
        if (!$this->todate) {
            return null;
        }
        return $format ? $this->todate->format($format) : $this->todate;
    }

    public function isDisplayble()
    {
        $mysidia = Registry::get("mysidia");
        $displayable = true;
        if ($this->usergroup && $mysidia->user->getUserGroupID() != $this->usergroup) {
            $displayable = false;
        }
        $today = new Date();
        if ($this->fromdate && $today < $this->fromdate) {
            $displayable = false;
        }
        if ($this->todate && $today > $this->todate) {
            $displayable = false;
        }
        return $displayable;
    }

    public function getThemeText($filename)
    {
        $mysidia = Registry::get("mysidia");
        $file = new SplFileObject("{$mysidia->path->getRoot()}templates/{$this->themefolder}/{$filename}");
        $text = "";
        while (!$file->eof()) {
            $text .= $file->fgets();
        }
        return $text;
    }

    public function updateThemeText($filename, $text, $folder = null)
    {
        $mysidia = Registry::get("mysidia");
        if ($folder) {
            $this->themefolder = $folder;
        }
        $path = "{$mysidia->path->getRoot()}templates/{$this->themefolder}";
        if (!is_dir($path)) {
            mkdir($path);
        }
        $file = new SplFileObject("{$path}/{$filename}", "w");
        $file->fwrite($mysidia->format($text, false));
        $file->fflush();
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("themes", [$field => $value], "id='{$this->id}'");
    }
}
