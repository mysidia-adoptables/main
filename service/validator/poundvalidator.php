<?php

namespace Service\Validator;

use ArrayObject;
use Model\DomainModel\OwnedAdoptable;
use Model\DomainModel\PoundException;
use Model\Settings\PoundSettings;
use Resource\Core\Registry;
use Resource\Core\Validator;
use Resource\Utility\Date;

class PoundValidator extends Validator
{
    public function __construct(private readonly OwnedAdoptable $adopt, private $cost, private readonly PoundSettings $settings, ArrayObject $validations)
    {
        parent::__construct($validations);
    }

    protected function checkAdopt()
    {
        if ($this->settings->specieslimit) {
            if (in_array($this->adopt->getSpeciesID(), $this->settings->specieslimit)) {
                throw new PoundException("species");
            }
        }
    }

    protected function checkOwner()
    {
        $mysidia = Registry::get("mysidia");
        if (!$this->adopt->isOwner($mysidia->user)) {
            $mysidia->user->ban();
            throw new PoundException("owner");
        }
    }

    protected function checkNumber()
    {
        $mysidia = Registry::get("mysidia");
        $today = new Date();
        $whereClause = "lastowner = '{$mysidia->user->getID()}' AND currentowner = 0";
        if ($this->settings->date == "yes") {
            $whereClause .= " AND datepound = '{$today->format('Y-M-D')}'";
        }
        $total = $mysidia->db->select("pounds", ["aid"], $whereClause)->rowCount();
        if ($total >= $this->settings->number[0]) {
            throw new PoundException(($this->settings->date == "yes") ? "pound_time1" : "pound_time2");
        }
    }

    protected function checkRecurrence()
    {
        $mysidia = Registry::get("mysidia");
        $recurrence = $mysidia->db->select("pounds", ["recurrence"], "aid ='{$this->adopt->getAdoptID()}'")->fetchColumn();
        if ($this->settings->recurrence && $this->settings->recurrence == $recurrence) {
            throw new PoundException("recurrence");
        }
    }

    protected function checkMoney()
    {
        $mysidia = Registry::get("mysidia");
        if ($this->settings->cost && $this->cost > $mysidia->user->getMoney()) {
            throw new PoundException("money");
        }
    }
}
