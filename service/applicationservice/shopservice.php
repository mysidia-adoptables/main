<?php

namespace Service\ApplicationService;

use Model\DomainModel\Adoptshop;
use Model\DomainModel\Itemshop;
use Resource\Collection\ArrayList;
use Resource\Core\Registry;
use Resource\Native\MysObject;

class ShopService extends MysObject
{

    public function getShop($sid, $eagerLoad = false)
    {
        $mysidia = Registry::get("mysidia");
        $shoptype = $mysidia->db->select("shops", ["shoptype"], "sid = :sid", ["sid" => $sid])->fetchColumn();
        return ($shoptype == "adoptshop") ? new Adoptshop($sid, null, $eagerLoad) : new Itemshop($sid, null, $eagerLoad);
    }

    public function countTotalShops($type = null, $fetchMode = null)
    {
        $mysidia = Registry::get("mysidia");
        $whereClause = "shoptype = '{$type}' AND {$this->formatFetchMode($fetchMode)}";
        return $mysidia->db->select("shops", ["sid"], $whereClause)->rowCount();
    }

    public function getShops($type = null, $fetchMode = null)
    {
        $mysidia = Registry::get("mysidia");
        $shopModel = "\\Model\\DomainModel\\{$type}";
        $whereClause = "shoptype = '{$type}' AND {$this->formatFetchMode($fetchMode)}";
        $stmt = $mysidia->db->select("shops", [], $whereClause);
        $shops = new ArrayList;
        while ($dto = $stmt->fetchObject()) {
            $shops->add(new $shopModel($dto->sid, $dto));
        }
        return $shops;
    }

    public function purchaseItem($shopID, $itemID, $quantity = 1)
    {
        if (!is_numeric($quantity) || $quantity < 1) throw new InvalidActionException("invalid_quantity");
        $shop = new Itemshop($shopID);
        return $shop->purchase($itemID, (int)$quantity);
    }

    public function purchaseAdopt($shopID, $adoptID)
    {
        $shop = new Adoptshop($shopID);
        return $shop->purchase($adoptID);
    }

    private function formatFetchMode($fetchMode = null)
    {
        if ($fetchMode == "visible") return "status != 'invisible'";
        if ($fetchMode == "open") return "status = 'open'";
        return "1";
    }
}
