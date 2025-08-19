<?php

namespace Controller\AdminCP;

use Exception;
use Model\DomainModel\Promocode;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\DuplicateIDException;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;

class PromoController extends AppController
{

    public function __construct()
    {
        parent::__construct();
        $mysidia = Registry::get("mysidia");
        if ($mysidia->usergroup->getpermission("canmanagesettings") != "yes") {
            throw new NoPermissionException("You do not have permission to manage promocode.");
        }
    }

    public function index()
    {
        parent::index();
        $mysidia = Registry::get("mysidia");
        $total = $mysidia->db->select("promocodes")->rowCount();
        if ($total == 0) throw new InvalidIDException("default_none");
        $pagination = new Pagination($total, $mysidia->settings->pagination, "admincp/promo", $mysidia->input->get("page"));
        $stmt = $mysidia->db->select("promocodes", [], "1 LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
        $promocodes = new ArrayList;
        while ($dto = $stmt->fetchObject()) {
            $promocodes->add(new Promocode($dto->code, $dto));
        }
        $this->setField("pagination", $pagination);
        $this->setField("promocodes", $promocodes);
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            $this->dataValidate();
            $oldcode = $mysidia->db->select("promocodes", ["code"], "code = :code", ["code" => $mysidia->input->post("code")])->fetchColumn();
            if ($oldcode) throw new DuplicateIDException("code_duplicate");
            $mysidia->db->insert("promocodes", ["pid" => null, "type" => $mysidia->input->post("type"), "user" => (int)$mysidia->input->post("user"), "code" => $mysidia->input->post("code"), "availability" => (int)$mysidia->input->post("availability"),
                "fromdate" => $mysidia->input->post("fromdate"), "todate" => $mysidia->input->post("todate"), "reward" => (int)$mysidia->input->post("reward")]);
        }
    }

    public function edit($pid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$pid) return $this->index();
        try {
            $promocode = new Promocode($pid);
            if ($mysidia->input->post("submit")) {
                $this->dataValidate();
                $mysidia->db->update("promocodes", ["type" => $mysidia->input->post("type"), "user" => (int)$mysidia->input->post("user"), "code" => $mysidia->input->post("code"), "availability" => (int)$mysidia->input->post("availability"),
                    "fromdate" => $mysidia->input->post("fromdate"), "todate" => $mysidia->input->post("todate"), "reward" => (int)$mysidia->input->post("reward")], "pid = '{$promocode->getID()}'");
            }
            $this->setField("promocode", $promocode);
        } catch (BlankFieldException $bfe) {
            throw $bfe;
        } catch (Exception $e) {
            throw new InvalidIDException($e->getMessage());
        }
    }

    public function delete($pid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$pid) $this->index();
        else {
            $promocode = new Promocode($pid);
            $mysidia->db->delete("promocodes", "pid = '{$promocode->getID()}'");
        }
        $this->setField("promocode", $pid ? $promocode : null);
    }

    private function dataValidate()
    {
        $mysidia = Registry::get("mysidia");
        if (!$mysidia->input->post("type")) throw new BlankFieldException("type");
        if (!$mysidia->input->post("code")) throw new BlankFieldException("code_none");
        if (is_numeric($mysidia->input->post("code"))) throw new BlankFieldException("code_numeric");
        if ($mysidia->input->post("fromdate") && $mysidia->input->post("todate") && (strtotime((string) $mysidia->input->post("fromdate")) > strtotime((string) $mysidia->input->post("todate")))) throw new InvalidActionException("date");
        if (!is_numeric($mysidia->input->post("availability"))) throw new BlankFieldException("availability");
        if ($mysidia->input->post("type") != "Page" && !$mysidia->input->post("reward")) throw new BlankFieldException("reward");
    }
}
