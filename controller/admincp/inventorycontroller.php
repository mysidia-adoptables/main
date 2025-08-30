<?php

namespace Controller\AdminCP;

use Exception;
use Model\DomainModel\OwnedItem;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;

class InventoryController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $mysidia = Registry::get("mysidia");
        if ($mysidia->usergroup->getpermission("canmanagesettings") != "yes") {
            throw new NoPermissionException("You do not have permission to manage item inventory.");
        }
    }

    public function index()
    {
        parent::index();
        $mysidia = Registry::get("mysidia");
        $total = $mysidia->db->select("inventory")->rowCount();
        if ($total == 0) {
            throw new InvalidIDException("default_none");
        }
        $pagination = new Pagination($total, $mysidia->settings->pagination, "admincp/inventory", $mysidia->input->get("page"));
        $stmt = $mysidia->db->join("items", "items.id = inventory.item")
                            ->select("inventory", [], "1 LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
        $ownedItems = new ArrayList();
        while ($dto = $stmt->fetchObject()) {
            $ownedItems->add(new OwnedItem($dto->iid, null, $dto));
        }
        $this->setField("pagination", $pagination);
        $this->setField("ownedItems", $ownedItems);
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            $this->dataValidate();
            $ownedItem = new OwnedItem($mysidia->input->post("item"), $mysidia->input->post("owner"));
            if ($ownedItem->inInventory()) {
                $ownedItem->add((int)$mysidia->input->post("quantity"));
            } else {
                $mysidia->db->insert("inventory", ["iid" => null, "item" => $mysidia->input->post("item"), "owner" => $mysidia->input->post("owner"),
                                                   "quantity" => (int)$mysidia->input->post("quantity"), "status" => 'Available']);
            }
        }
    }

    public function edit($iid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$iid) {
            return $this->index();
        }
        try {
            $ownedItem = new OwnedItem($iid);
            if ($mysidia->input->post("submit")) {
                $this->dataValidate();
                $mysidia->db->update("inventory", ["item" => $mysidia->input->post("item"), "owner" => $mysidia->input->post("owner"), "quantity" => (int)$mysidia->input->post("quantity")], "iid = '{$ownedItem->getInventoryID()}'");
            }
            $this->setField("ownedItem", $ownedItem);
        } catch (BlankFieldException $bfe) {
            throw new InvalidActionException($bfe->getMessage());
        } catch (Exception) {
            throw new InvalidIDException("nonexist");
        }
    }

    public function delete($iid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$iid) {
            $this->index();
        } else {
            $ownedItem = new OwnedItem($iid);
            $mysidia->db->delete("inventory", "iid='{$ownedItem->getInventoryID()}'");
        }
        $this->setField("ownedItem", $iid ? $ownedItem : null);
    }

    private function dataValidate()
    {
        $mysidia = Registry::get("mysidia");
        if (!$mysidia->input->post("item")) {
            throw new BlankFieldException("item");
        }
        if (!$mysidia->input->post("owner")) {
            throw new BlankFieldException("owner");
        }
        if (!is_numeric($mysidia->input->post("quantity")) || $mysidia->input->post("quantity") < 1) {
            throw new BlankFieldException("quantity");
        }
    }
}
