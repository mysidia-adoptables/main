<?php

namespace Controller\AdminCP;

use Exception;
use Model\DomainModel\Module;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;

class ModuleController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $mysidia = Registry::get("mysidia");
        if ($mysidia->usergroup->getpermission("canmanagesettings") != "yes") {
            throw new NoPermissionException("You do not have permission to manage modules.");
        }
    }

    public function index()
    {
        parent::index();
        $mysidia = Registry::get("mysidia");
        $total = $mysidia->db->select("modules")->rowCount();
        $pagination = new Pagination($total, $mysidia->settings->pagination, "admincp/module", $mysidia->input->get("page"));
        $stmt = $mysidia->db->select("modules", [], "1 LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
        $modules = new ArrayList();
        while ($dto = $stmt->fetchObject()) {
            $modules->add(new Module($dto->moid, $dto));
        }
        $this->setField("pagination", $pagination);
        $this->setField("modules", $modules);
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            if (!$mysidia->input->post("widget") || !$mysidia->input->post("name")) {
                throw new BlankFieldException("global_blank");
            }
            $userLevel = $mysidia->input->post("userlevel") ?: "user";
            $html = ($mysidia->user->getUsergroup() == 1) ? $mysidia->input->rawPost("html") : $mysidia->format($mysidia->input->rawPost("html"));
            $php = ($mysidia->user->getUsergroup() == 1) ? $mysidia->input->rawPost("php") : $mysidia->format($mysidia->input->rawPost("php"));
            $mysidia->db->insert("modules", ["moid" => null, "widget" => (int)$mysidia->input->post("widget"), "name" => $mysidia->input->post("name"), "subtitle" => $mysidia->input->post("subtitle"),
                "userlevel" => $userLevel, "html" => $html, "php" => $php, "order" => (int)$mysidia->input->post("order"), "status" => $mysidia->input->post("status")]);
        }
    }

    public function edit($moid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$moid) {
            return $this->index();
        }
        try {
            $module = new Module($moid);
            if ($mysidia->input->post("submit")) {
                $userLevel = $mysidia->input->post("userlevel") ?: "user";
                $html = ($mysidia->user->getUsergroup() == 1) ? $mysidia->input->rawPost("html") : $mysidia->format($mysidia->input->rawPost("html"));
                $php = ($mysidia->user->getUsergroup() == 1) ? $mysidia->input->rawPost("php") : $mysidia->format($mysidia->input->rawPost("php"));
                $mysidia->db->update("modules", ["widget" => (int)$mysidia->input->post("widget"), "name" => $mysidia->input->post("name"), "subtitle" => $mysidia->input->post("subtitle"), "userlevel" => $userLevel,
                    "html" => $html, "php" => $php, "order" => (int)$mysidia->input->post("order"), "status" => $mysidia->input->post("status")], "moid = '{$module->getID()}'");
            }
            $this->setField("module", $module);
        } catch (Exception) {
            throw new InvalidIDException("global_id");
        }
    }

    public function delete($moid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$moid) {
            $this->index();
        } else {
            $module = new Module($moid);
            $mysidia->db->delete("modules", "moid = '{$module->getID()}'");
        }
        $this->setField("module", $moid ? $module : null);
    }
}
