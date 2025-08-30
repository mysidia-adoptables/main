<?php

namespace Controller\AdminCP;

use Exception;
use Model\DomainModel\Widget;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;

class WidgetController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $mysidia = Registry::get("mysidia");
        if ($mysidia->usergroup->getpermission("canmanagesettings") != "yes") {
            throw new NoPermissionException("You do not have permission to manage widgets.");
        }
    }

    public function index()
    {
        parent::index();
        $mysidia = Registry::get("mysidia");
        $total = $mysidia->db->select("widgets")->rowCount();
        $pagination = new Pagination($total, $mysidia->settings->pagination, "admincp/widget", $mysidia->input->get("page"));
        $stmt = $mysidia->db->select("widgets", [], "1 LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
        $widgets = new ArrayList();
        while ($dto = $stmt->fetchObject()) {
            $widgets->add(new Widget($dto->wid, $dto));
        }
        $this->setField("pagination", $pagination);
        $this->setField("widgets", $widgets);
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            if (!$mysidia->input->post("name")) {
                throw new BlankFieldException("global_blank");
            }
            $controller = $mysidia->input->post("controllers") ?: "main";
            $mysidia->db->insert("widgets", ["wid" => null, "name" => $mysidia->input->post("name"), "controller" => $controller,
                                             "order" => (int)$mysidia->input->post("order"), "status" => $mysidia->input->post("status")]);
        }
    }

    public function edit($wid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$wid) {
            return $this->index();
        }
        try {
            $widget = new Widget($wid);
            if ($mysidia->input->post("submit")) {
                if (!$mysidia->input->post("name")) {
                    throw new BlankFieldException("global_blank");
                }
                $controller = $mysidia->input->post("controllers") ?: "main";
                $mysidia->db->update("widgets", ["name" => $mysidia->input->post("name"), "controller" => $controller, "order" => (int)$mysidia->input->post("order"),
                                                 "status" => $mysidia->input->post("status")], "wid='{$widget->getID()}'");
            }
            $this->setField("widget", $widget);
        } catch (BlankFieldException $bfe) {
            throw $bfe;
        } catch (Exception) {
            throw new InvalidIDException("global_id");
        }
    }

    public function delete($wid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$wid) {
            $this->index();
        } else {
            if ($wid < 6) {
                throw new InvalidActionException("internal");
            }
            $widget = new Widget($wid);
            $mysidia->db->delete("widgets", "wid = '{$widget->getID()}'");
        }
        $this->setField("widget", $wid ? $widget : null);
    }
}
