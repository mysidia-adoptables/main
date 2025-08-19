<?php

namespace Controller\AdminCP;

use Exception;
use Model\DomainModel\Adoptable;
use Model\DomainModel\Member;
use Model\DomainModel\OwnedAdoptable;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;

class OwnedadoptController extends AppController
{

    public function __construct()
    {
        parent::__construct();
        $mysidia = Registry::get("mysidia");
        if ($mysidia->usergroup->getpermission("canmanageadopts") != "yes") {
            throw new NoPermissionException("You do not have permission to manage adoptables.");
        }
    }

    public function index()
    {
        parent::index();
        $mysidia = Registry::get("mysidia");
        $total = $mysidia->db->select("owned_adoptables")->rowCount();
        if ($total == 0) throw new InvalidIDException("empty");
        $pagination = new Pagination($total, $mysidia->settings->pagination, "admincp/ownedadopt", $mysidia->input->get("page"));
        $stmt = $mysidia->db->join("adoptables", "adoptables.id = owned_adoptables.adopt")
            ->select("owned_adoptables", [], "1 LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
        $ownedAdopts = new ArrayList;
        while ($dto = $stmt->fetchObject()) {
            $ownedAdopts->add(new OwnedAdoptable($dto->aid, $dto));
        }
        $this->setField("pagination", $pagination);
        $this->setField("ownedAdopts", $ownedAdopts);
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            $this->dataValidate();
            $adopt = new Adoptable($mysidia->input->post("adopt"));
            $owner = new Member($mysidia->input->post("owner"));
            $mysidia->db->insert("owned_adoptables", ["aid" => null, "adopt" => $mysidia->input->post("adopt"), "name" => $mysidia->input->post("name"), "owner" => $owner->getID(), "currentlevel" => (int)$mysidia->input->post("level"), "totalclicks" => (int)$mysidia->input->post("clicks"),
                "code" => $adopt->generateCode(), "imageurl" => null, "alternate" => (int)$mysidia->input->post("alternate"), "tradestatus" => 'fortrade', "isfrozen" => 'no', "gender" => $mysidia->input->post("gender"), "offsprings" => 0, "lastbred" => 0]);
        }
    }

    public function edit($aid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$aid) $this->index();
        elseif (!$mysidia->input->post("submit")) {
            try {
                $ownedAdopt = new OwnedAdoptable($aid);
                $this->setField("ownedAdopt", $ownedAdopt);
            } catch (Exception) {
                throw new InvalidIDException("global_id");
            }
        } else {
            $this->dataValidate();
            $ownedAdopt = new OwnedAdoptable($aid);
            $owner = new Member($mysidia->input->post("owner"));
            $mysidia->db->update("owned_adoptables", ["adopt" => $mysidia->input->post("adopt"), "name" => $mysidia->input->post("name"), "owner" => $owner->getID(), "totalclicks" => (int)$mysidia->input->post("clicks"),
                "currentlevel" => (int)$mysidia->input->post("level"), "alternate" => (int)$mysidia->input->post("alternate"), "gender" => $mysidia->input->post("gender")], "aid='{$ownedAdopt->getID()}'");
        }
        $this->setField("ownedAdopt", $aid ? $ownedAdopt : null);
    }

    public function delete($aid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$aid) $this->index();
        else {
            $ownedAdopt = new OwnedAdoptable($aid);
            $mysidia->db->delete("owned_adoptables", "aid = '{$ownedAdopt->getID()}'");
        }
        $this->setField("ownedAdopt", $aid ? $ownedAdopt : null);
    }

    private function dataValidate()
    {
        $mysidia = Registry::get("mysidia");
        $fields = ["adopt" => $mysidia->input->post("adopt"), "name" => $mysidia->input->post("name"), "owner" => $mysidia->input->post("owner"), "clicks" => $mysidia->input->post("clicks"),
            "level" => $mysidia->input->post("level"), "usealternates" => $mysidia->input->post("usealternates"), "gender" => $mysidia->input->post("gender")];
        foreach ($fields as $field => $value) {
            if (!$value) {
                if (($field == "clicks" && $value == 0) || ($field == "usealternates") || ($field == "level" && $value == 0)) {
                    continue;
                }
                throw new BlankFieldException("You did not enter in {$field} for the adoptable.  Please go back and try again.");
            }
        }
        return true;
    }
}
