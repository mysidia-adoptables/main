<?php

namespace Controller\Main;

use Model\DomainModel\ItemException;
use Model\DomainModel\OwnedItem;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;
use Resource\Native\MysString;

class InventoryController extends AppController
{

    public function __construct()
    {
        parent::__construct("member");
    }

    public function index()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->systems->items != "enabled") throw new NoPermissionException("The admin has turned off item/inventory feature for this site, please contact him/her for detailed information.");
        $stmt = $mysidia->db->join("items", "items.id = inventory.item")
            ->select("inventory", [], "owner = '{$mysidia->user->getID()}'");
        if ($stmt->rowCount() == 0) throw new InvalidIDException("inventory_empty");
        $inventory = new ArrayList;
        while ($dto = $stmt->fetchObject()) {
            $inventory->add(new OwnedItem($dto->item, $dto->owner, $dto));
        }
        $this->setField("inventory", $inventory);
    }

    public function uses()
    {
        $mysidia = Registry::get("mysidia");
        $item = new OwnedItem($mysidia->input->post("item"), $mysidia->user->getID());
        if (!$item->inInventory()) throw new ItemException("use_none");

        if ($mysidia->input->post("aid")) {
            if (!$item->checkTarget($mysidia->input->post("aid")) || $mysidia->input->post("validation") != "valid") {
                throw new ItemException("use_fail");
            } elseif (!$item->randomChance()) {
                $item->remove();
                throw new ItemException("use_effect");
            } else {
                $message = $item->apply($mysidia->input->post("aid"));
                $this->setField("message", new MysString($message));
            }
            return;
        }

        $stmt = $mysidia->db->select("owned_adoptables", ["aid", "name"], "owner = '{$mysidia->user->getID()}'");
        $map = $mysidia->db->fetchMap($stmt);
        $this->setField("item", $item);
        $this->setField("petMap", $map);
    }

    public function sell()
    {
        $mysidia = Registry::get("mysidia");
        $item = new OwnedItem($mysidia->input->post("item"), $mysidia->user->getID());
        if (!$item->inInventory()) throw new ItemException("sell_none");

        if (!$mysidia->input->post("quantity")) throw new ItemException("sell_empty");
        elseif (!$item->isSellable((int)$mysidia->input->post("quantity"))) throw new ItemException("sell_quantity");
        else $item->sell((int)$mysidia->input->post("quantity"));
        $this->setField("item", $item);
    }

    public function toss($confirm = null)
    {
        $mysidia = Registry::get("mysidia");
        $item = new OwnedItem($mysidia->input->post("item"), $mysidia->user->getID());
        if (!$item->inInventory()) throw new ItemException("toss_none");

        if ($confirm) {
            $item->toss();
        }
        $this->setField("item", $item);
        $this->setField("confirm", $confirm ? new MysString("confirm") : null);
    }
}
