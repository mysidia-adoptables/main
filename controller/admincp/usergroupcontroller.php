<?php

namespace Controller\AdminCP;

use Model\DomainModel\Usergroup;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\DuplicateIDException;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;

class UsergroupController extends AppController
{

    public function __construct()
    {
        parent::__construct();
        $mysidia = Registry::get("mysidia");
        if ($mysidia->usergroup->getpermission("canmanageusers") != "yes") {
            throw new NoPermissionException("You do not have permission to manage users.");
        }
    }

    public function index()
    {
        parent::index();
        $mysidia = Registry::get("mysidia");
        $total = $mysidia->db->select("groups")->rowCount();
        $pagination = new Pagination($total, $mysidia->settings->pagination, "admincp/usergroup", $mysidia->input->get("page"));
        $stmt = $mysidia->db->select("groups", [], "1 LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
        $usergroups = new ArrayList;
        while ($dto = $stmt->fetchObject()) {
            $usergroups->add(new Usergroup($dto->gid, $dto));
        }
        $this->setField("pagination", $pagination);
        $this->setField("usergroups", $usergroups);
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit") && $mysidia->input->post("group")) {
            $groupExist = $mysidia->db->select("groups", ["groupname"], "groupname = :groupname", ["groupname" => $mysidia->input->post("group")])->fetchColumn();
            if ($groupExist) throw new DuplicateIDException("duplicate");
            $mysidia->db->insert("groups", ["gid" => null, "groupname" => $mysidia->input->post("group"), "canadopt" => 'yes', "canpm" => 'yes', "cancp" => 'no',
                "canmanageadopts" => 'no', "canmanagecontent" => 'no', "canmanageads" => 'no', "canmanagesettings" => 'no', "canmanageusers" => 'no']);
        }
    }

    public function edit($gid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$gid) return $this->index();
        $usergroup = new Usergroup($gid);
        if (!is_object($usergroup)) throw new InvalidIDException("global_id");
        $permissions = ["canadopt", "canpm", "cancp", "canmanageusers", "canmanageadopts", "canmanagecontent", "canmanagesettings", "canmanageads"];
        if ($mysidia->input->post("submit")) {
            foreach ($permissions as $permKey) {
                $permValue = ($mysidia->input->post($permKey) == "yes") ? "yes" : "no";
                $usergroup->setPermission([$permKey => $permValue]);
            }
        }
        $this->setField("usergroup", $gid ? $usergroup : null);
    }

    public function delete($gid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$gid) $this->index();
        else {
            $usergroup = new Usergroup($gid);
            $mysidia->db->delete("groups", "gid = '{$usergroup->getID()}'");
        }
        $this->setField("usergroup", $gid ? $usergroup : null);
    }

    public function admin(): never
    {
        throw new InvalidActionException("global_action");
    }
}
