<?php

namespace Model\DomainModel;

use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Native\MysString;

class OwnedItem extends Item
{
    // The OwnedItem class, which defines functionalities for items that belong to specific users

    public const IDKEY = "iid";
    protected $iid;
    protected $item;
    protected $owner;
    protected $quantity;
    protected $status;

    public function __construct($iteminfo, $itemowner = "", $dto = null)
    {
        // the item is an owned item in user inventory, so retrieve database info to assign properties
        $mysidia = Registry::get("mysidia");
        if ($iteminfo instanceof MysString) {
            $iteminfo = $iteminfo->getValue();
        }
        if (!$dto) {
            $fetchmode = $itemowner ? "item" : "iid";
            $whereclause = ($fetchmode == "iid") ? "{$fetchmode} = :iteminfo" : "{$fetchmode} = :iteminfo AND owner = '{$itemowner}'";
            $dto = $mysidia->db->join("items", "items.id = inventory.item")
                ->select("inventory", [], $whereclause, ["iteminfo" => $iteminfo])->fetchObject();
            if (!is_object($dto)) {
                if ($fetchmode == "iid") {
                    return;
                }
                $this->iid = 0;
                $this->quantity = 0;
                parent::__construct($iteminfo);
                return;
            }
        }
        $this->createFromDTO($dto);
    }

    public function getInventoryID()
    {
        return $this->iid;
    }

    public function getItemID()
    {
        return $this->id;
    }

    public function getItem($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) {
            return new Item($this->id);
        } else {
            return $this->id;
        }
    }

    public function inInventory()
    {
        return ($this->iid > 0);
    }

    public function getOwner($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) {
            return new Member($this->owner);
        } else {
            return $this->owner;
        }
    }

    public function getOwnerName()
    {
        return $this->getOwner(Model::MODEL)->getUsername();
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function hasItem()
    {
        // This method checks if the item exists in inventory or not, not to be confused with parent class' getitem() class.
        $mysidia = Registry::get("mysidia");
        $stmt = $mysidia->db->select("inventory", [], "item ='{$this->item}' and owner ='{$this->owner}'");
        return $stmt->fetchObject();
    }

    public function isOverCap($quantity)
    {
        return ($this->quantity + $quantity > $this->cap);
    }

    public function isSellable($quantity)
    {
        return ($quantity > 0 && $this->price > 0 && $quantity <= $this->quantity);
    }

    public function getCost($quantity = 0, $discount = 0)
    {
        // This method returns the cost of items.
        return $this->price * $quantity * (1 - $discount);
    }

    public function apply($adopt = "", $user = "")
    {
        if (is_numeric($adopt)) {
            $target = new OwnedAdoptable($adopt);
        }
        if (!empty($user)) {
            $target = new Member($user);
        }
        if (!$target) {
            throw new ItemException("Cannot apply item to invalid target.");
        }

        // Now we decide which function to call...
        $itemFunction = new ItemFunction($this->function);
        $message = $itemFunction->apply($this, $target);
        if ($this->consumable) {
            $this->remove();
        }
        return $message;
    }

    public function append()
    {
        return $this->add(1);
    }

    public function add($quantity = 1, $owner = null)
    {
        $mysidia = Registry::get("mysidia");
        $this->owner = empty($owner) ? $this->owner : $owner;
        $this->quantity += $quantity;
        if ($this->iid) {
            $mysidia->db->update("inventory", ["quantity" => $this->quantity], "iid = '{$this->iid}'");
        } else {
            $mysidia->db->insert("inventory", ["iid" => null, "item" => $this->id, "owner" => $this->owner, "quantity" => $this->quantity, "status" => "Available"]);
            $this->iid = $mysidia->db->lastInsertId();
        }
        return $this->iid;
    }

    public function sell($quantity = 1, $owner = null)
    {
        // This method sells items from user inventory
        $this->owner = empty($owner) ? $this->owner : $owner;
        if ($this->remove($quantity)) {
            $owner = $this->getOwner(Model::MODEL);
            $profit = $this->getCost($quantity, 0.5);
            $owner->changeMoney($profit);
            return true;
        } else {
            return false;
        }
    }

    public function toss()
    {
        $this->remove($this->quantity);
        return true;
    }

    public function remove($quantity = 1, $owner = null)
    {
        // This method removes items from user inventory

        $mysidia = Registry::get("mysidia");
        $this->owner = empty($owner) ? $this->owner : $owner;
        $this->quantity -= $quantity;
        if (!$quantity || $this->quantity < 0) {
            return false;
        } else {
            match ($this->quantity) {
                0 => $mysidia->db->delete("inventory", "iid = '{$this->iid}'"),
                default => $mysidia->db->update("inventory", ["quantity" => $this->quantity], "iid = '{$this->iid}'"),
            };
            return true;
        }
    }
}
