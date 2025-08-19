<?php

namespace Model\DomainModel;

use Resource\Core\Registry;
use Resource\Exception\NoPermissionException;

class Adoptshop extends Shop
{

    protected $adopts;

    public function __construct($shopinfo, $dto = null, $loadAdopts = false)
    {
        parent::__construct($shopinfo, $dto);
        if ($loadAdopts) {
            $this->adopts = $this->getAdoptTypes();
            $this->total = is_array($this->adopts) ? count($this->adopts) : 0;
        }
    }

    public function getAdoptTypes()
    {
        if (!$this->adopts) {
            $mysidia = Registry::get("mysidia");
            $stmt = $mysidia->db->select("adoptables", ["type"], "shop ='{$this->sid}'");
            $this->adopts = [];
            while ($adopt = $stmt->fetchColumn()) {
                $this->adopts[] = $adopt;
            }
        }
        return $this->adopts;
    }

    public function getTotalCost(Adoptable $adopt)
    {
        return $adopt->getCost() * (1 + $this->salestax / 100);
    }

    public function purchase($adoptinfo)
    {
        $mysidia = Registry::get("mysidia");
        $adopt = new Adoptable($adoptinfo);
        if ($adopt->getShop() != $this->sid) throw new NoPermissionException("shop_belong");
        $totalcost = $this->getTotalCost($adopt);
        $mysidia->user->payMoney($totalcost);
        $adopt->makeOwnedAdopt($mysidia->user->getID());
        return $totalcost;
    }
}
