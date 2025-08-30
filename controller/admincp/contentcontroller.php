<?php

namespace Controller\AdminCP;

use Exception;
use Model\DomainModel\Content;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;
use Resource\Utility\Date;

class ContentController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $mysidia = Registry::get("mysidia");
        if ($mysidia->usergroup->getpermission("canmanagecontent") != "yes") {
            throw new NoPermissionException("You do not have permission to manage users.");
        }
    }

    public function index()
    {
        parent::index();
        $mysidia = Registry::get("mysidia");
        $total = $mysidia->db->select("content")->rowCount();
        if ($total == 0) {
            throw new InvalidIDException("default_none");
        }
        $pagination = new Pagination($total, $mysidia->settings->pagination, "admincp/content", $mysidia->input->get("page"));
        $stmt = $mysidia->db->select("content", [], "1 LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
        $contents = new ArrayList();
        while ($dto = $stmt->fetchObject()) {
            $contents->add(new Content($dto->cid, $dto));
        }
        $this->setField("pagination", $pagination);
        $this->setField("contents", $contents);
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            if (!$mysidia->input->post("page")) {
                throw new BlankFieldException("url");
            }
            $this->dataValidate();
            $contentText = ($mysidia->user->getUsergroup() == 1) ? $mysidia->input->rawPost("content") : $mysidia->format($mysidia->input->rawPost("content"));
            $date = new Date();
            $mysidia->db->insert("content", ["cid" => null, "page" => $mysidia->input->post("page"), "title" => $mysidia->input->post("title"), "date" => $date->format('Y-m-d'), "content" => $contentText,
                                             "level" => null, "code" => $mysidia->input->post("promocode"), "item" => (int)$mysidia->input->post("item"), "time" => $mysidia->input->post("time"), "group" => (int)$mysidia->input->post("group")]);
        }
    }

    public function edit($cid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$cid) {
            return $this->index();
        }
        try {
            $content = new Content($cid);
            if ($mysidia->input->post("submit")) {
                $this->dataValidate();
                $contentText = ($mysidia->user->getUsergroup() == 1) ? $mysidia->input->rawPost("content") : $mysidia->format($mysidia->input->rawPost("content"));
                $mysidia->db->update("content", ["title" => $mysidia->input->post("title"), "content" => $contentText, "code" => $mysidia->input->post("promocode"), "item" => (int)$mysidia->input->post("item"),
                                                 "time" => $mysidia->input->post("time"), "group" => (int)$mysidia->input->post("group")], "cid = '{$content->getID()}'");
            }
            $this->setField("content", $content);
        } catch (Exception) {
            throw new InvalidIDException("nonexist");
        }
    }

    public function delete($cid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$cid) {
            $this->index();
        } else {
            $content = new Content($cid);
            if ($content->getPage() == "index" || $content->getPage() == "tos") {
                throw new InvalidIDException("special");
            }
            $mysidia->db->delete("content", "cid = '{$content->getID()}'");
        }
        $this->setField("content", $cid ? $content : null);
    }

    private function dataValidate()
    {
        $mysidia = Registry::get("mysidia");
        if (!$mysidia->input->post("title")) {
            throw new BlankFieldException("title");
        }
        if (!$mysidia->input->post("content")) {
            throw new BlankFieldException("content");
        }
    }
}
