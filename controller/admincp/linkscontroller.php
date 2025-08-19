<?php

namespace Controller\AdminCP;

use Exception;
use Model\DomainModel\Link;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;

class LinksController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $mysidia = Registry::get("mysidia");
        if ($mysidia->usergroup->getpermission("canmanagesettings") != "yes") {
            throw new NoPermissionException("You do not have permission to manage links.");
        }
    }

    public function index()
    {
        parent::index();
        $mysidia = Registry::get("mysidia");
        $total = $mysidia->db->select("links")->rowCount();
        $pagination = new Pagination($total, $mysidia->settings->pagination, "admincp/links", $mysidia->input->get("page"));
        $prefix = constant("PREFIX");
        $stmt = $mysidia->db->query("SELECT subcat.*,parentcat.linktext as parentname FROM {$prefix}links as subcat LEFT JOIN {$prefix}links as parentcat ON parentcat.id=subcat.linkparent ORDER BY subcat.id ASC LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
        $links = new ArrayList();
        while ($dto = $stmt->fetchObject()) {
            $links->add(new Link($dto->id, $dto));
        }
        $this->setField("pagination", $pagination);
        $this->setField("links", $links);
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            if (!$mysidia->input->post("linktext") || !$mysidia->input->post("linkurl")) {
                throw new BlankFieldException("global_blank");
            }
            $mysidia->db->insert("links", ["id" => null, "linktype" => $mysidia->input->post("linktype"), "linktext" => $mysidia->input->post("linktext"), "linkurl" => $mysidia->input->post("linkurl"), "linkparent" => (int)$mysidia->input->post("linkparent"), "linkorder" => (int)$mysidia->input->post("linkorder")]);
        }
    }

    public function edit($lid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$lid) {
            return $this->index();
        }
        try {
            $link = new Link($lid);
            if ($mysidia->input->post("submit")) {
                $mysidia->db->update("links", ["linktype" => $mysidia->input->post("linktype"), "linktext" => $mysidia->input->post("linktext"), "linkurl" => $mysidia->input->post("linkurl"),
                    "linkparent" => (int)$mysidia->input->post("linkparent"), "linkorder" => (int)$mysidia->input->post("linkorder")], "id = '{$link->getID()}'");
            }
            $this->setField("link", $link);
        } catch (Exception) {
            throw new InvalidIDException("global_id");
        }
    }

    public function delete($lid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$lid) {
            $this->index();
        } else {
            $link = new Link($lid);
            $mysidia->db->delete("links", "id = '{$link->getID()}'");
        }
        $this->setField("link", $lid ? $link : null);
    }
}
