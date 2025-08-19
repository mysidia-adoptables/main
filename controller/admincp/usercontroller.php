<?php

namespace Controller\AdminCP;

use Model\DomainModel\Member;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Model;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Exception\NoPermissionException;
use Resource\Utility\Password;
use Service\ApplicationService\AccountService;


class UserController extends AppController
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
        $total = $mysidia->db->select("users")->rowCount();
        $pagination = new Pagination($total, $mysidia->settings->pagination, "admincp/user", $mysidia->input->get("page"));
        $stmt = $mysidia->db->select("users", [], "1 LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
        $users = new ArrayList;
        while ($dto = $stmt->fetchObject()) {
            $users->add(new Member($dto->uid, $dto));
        }
        $this->setField("pagination", $pagination);
        $this->setField("users", $users);
    }

    public function add(): never
    {
        throw new InvalidActionException("global_action");
    }

    public function edit($uid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$uid) return $this->index();
        $user = new Member($uid);
        if ($mysidia->input->post("submit")) {
            $accountService = new AccountService(new Password);
            if ($mysidia->input->post("email")) $user->setEmail($mysidia->input->post("email"), Model::UPDATE);
            if ($mysidia->input->post("pass1")) {
                $accountService->updatePassword($user, $mysidia->input->post("pass1"));
                if ($mysidia->input->post("emailpwchange") == "yes") $user->sendPasswordEmail($mysidia->input->post("pass1"));
            }
            if (is_numeric($mysidia->input->post("usergroup"))) $mysidia->db->update("users", ["usergroup" => $mysidia->input->post("usergroup")], "uid = '{$user->getID()}'");

            $permissions = ["canlevel", "canvm", "canfriend", "cantrade", "canbreed", "canpound", "canshop"];
            foreach ($permissions as $permission) {
                if ($mysidia->input->post($permission) == "no") {
                    $mysidia->db->update("users_permissions", [$permission => 'no'], "uid = '{$user->getID()}'");
                }
            }
            if ($mysidia->input->post("unban") == "yes") $user->unban();
        }
        $this->setField("user", $uid ? $user : null);
    }

    public function delete($uid = null)
    {
        if (!$uid) $this->index();
        else {
            $user = new Member($uid);
            if ($user->isCurrentUser()) throw new InvalidActionException("current");
            if ($user->isAdmin()) throw new NoPermissionException("admin");
            $user->delete();
        }
        $this->setField("user", $uid ? $user : null);
    }

    public function merge(): never
    {
        throw new InvalidActionException("global_action");
    }

    public function search(): never
    {
        throw new InvalidActionException("global_action");
    }
}
