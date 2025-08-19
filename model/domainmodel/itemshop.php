<?php

namespace Model\DomainModel;

use Resource\Core\Registry;
use Resource\Exception\NoPermissionException;

class Itemshop extends Shop
{

    protected $items;

    public function __construct($shopinfo, $dto = null, $loadItems = false)
    {
        parent::__construct($shopinfo, $dto);
        if ($loadItems) {
            $this->items = $this->getItemnames();
            $this->total = is_array($this->items) ? count($this->items) : 0;
        }
    }

    public function getItemnames()
    {
        if (!$this->items) {
            $mysidia = Registry::get("mysidia");
            $stmt = $mysidia->db->select("items", ["itemname"], "shop ='{$this->sid}'");
            $this->items = [];

            while ($item = $stmt->fetchColumn()) {
                $this->items[] = $item;
            }
        }
        return $this->items;
    }

    public function getItem($itemname, $owner)
    {
        if (!$this->hasItem($itemname)) throw new ShopException("The item is not sold from this shop.");
        return new OwnedItem($itemname, $owner);
    }

    public function hasItem($itemname)
    {
        $mysidia = Registry::get("mysidia");
        if ($this->sid == 0 || empty($this->shopname)) return false;
        $stmt = $mysidia->db->select("items", [], "itemname = '{$itemname}' AND shop ='{$this->sid}'");
        $exist = ($row = $stmt->fetchObject());
        return $exist;
    }

    public function getTotalCost(OwnedItem $item, $quantity)
    {
        return $item->getCost($quantity) * (1 + $this->salestax / 100);
    }

    public function purchase($iteminfo, $quantity)
    {
        $mysidia = Registry::get("mysidia");
        $item = new OwnedItem($iteminfo, $mysidia->user->getID());
        if ($item->getShop() != $this->sid) throw new NoPermissionException("shop_belong");
        if ($item->isOverCap($quantity)) throw new ItemException("full_quantity");
        $totalcost = $this->getTotalCost($item, $quantity);
        $mysidia->user->payMoney($totalcost);
        $item->add($quantity, $mysidia->user->getID());
        return $totalcost;
    }
}
