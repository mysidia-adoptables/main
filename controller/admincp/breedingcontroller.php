<?php

namespace Controller\AdminCP;

use Exception;
use Model\DomainModel\BreedAdoptable;
use Model\Settings\BreedingSettings;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;

class BreedingController extends AppController
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
        $total = $mysidia->db->select("breeding")->rowCount();
        if ($total == 0) {
            throw new InvalidIDException("empty");
        }
        $pagination = new Pagination($total, $mysidia->settings->pagination, "admincp/breeding", $mysidia->input->get("page"));
        $stmt = $mysidia->db->select("breeding", [], "1 LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
        $breedAdopts = new ArrayList();
        while ($dto = $stmt->fetchObject()) {
            $breedAdopts->add(new BreedAdoptable($dto->bid, $dto));
        }
        $this->setField("pagination", $pagination);
        $this->setField("breedAdopts", $breedAdopts);
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            $this->dataValidate();
            $availability = ($mysidia->input->post("available") == "yes") ? "yes" : "no";
            $mysidia->db->insert("breeding", ["bid" => null, "offspring" => (int)$mysidia->input->post("offspring"), "parent" => (int)$mysidia->input->post("parent"), "mother" => (int)$mysidia->input->post("mother"), "father" => (int)$mysidia->input->post("father"),
                                              "probability" => (int)$mysidia->input->post("probability"), "survival" => (int)$mysidia->input->post("survival"), "level" => (int)$mysidia->input->post("level"), "available" => $availability]);
        }
    }

    public function edit($bid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$bid) {
            $this->index();
        } elseif (!$mysidia->input->post("submit")) {
            try {
                $breedAdopt = new BreedAdoptable($bid);
            } catch (Exception) {
                throw new InvalidIDException("global_id");
            }
        } else {
            $this->dataValidate();
            $breedAdopt = new BreedAdoptable($bid);
            $availability = ($mysidia->input->post("available") == "yes") ? "yes" : "no";
            $mysidia->db->update("breeding", ["offspring" => (int)$mysidia->input->post("offspring"), "parent" => (int)$mysidia->input->post("parent"), "mother" => (int)$mysidia->input->post("mother"), "father" => (int)$mysidia->input->post("father"),
                                              "probability" => (int)$mysidia->input->post("probability"), "survival" => (int)$mysidia->input->post("survival"), "level" => (int)$mysidia->input->post("level"), "available" => $availability], "bid='{$breedAdopt->getID()}'");
        }
        $this->setField("breedAdopt", $bid ? $breedAdopt : null);
    }

    public function delete($bid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$bid) {
            $this->index();
        } else {
            $breedAdopt = new BreedAdoptable($bid);
            $mysidia->db->delete("breeding", "bid='{$breedAdopt->getID()}'");
        }
        $this->setField("breedAdopt", $bid ? $breedAdopt : null);
    }

    public function settings()
    {
        $mysidia = Registry::get("mysidia");
        $breedingSettings = new BreedingSettings($mysidia->db);
        if ($mysidia->input->post("submit")) {
            $settings = ['system', 'method', 'species', 'interval', 'level', 'capacity',
                         'number', 'chance', 'cost', 'usergroup', 'item'];
            foreach ($settings as $name) {
                if ($mysidia->input->post($name) != ($breedingSettings->$name)) {
                    $mysidia->db->update("breeding_settings", ["value" => $mysidia->input->post($name)], "name = :name", ["name" => $name]);
                }
            }
            return;
        }
        $this->setField("breedingSettings", $breedingSettings);
    }

    private function dataValidate()
    {
        $mysidia = Registry::get("mysidia");
        if (!$mysidia->input->selected("offspring")) {
            throw new BlankFieldException("offspring");
        }
        if (!$mysidia->input->selected("parent") && !$mysidia->input->selected("mother") && !$mysidia->input->selected("father")) {
            throw new BlankFieldException("parent");
        }
        if ($mysidia->input->selected("parent") && ($mysidia->input->selected("mother") || $mysidia->input->selected("father"))) {
            throw new BlankFieldException("parents");
        }

        if (!is_numeric($mysidia->input->post("probability"))) {
            throw new BlankFieldException("probability");
        }
        if (!is_numeric($mysidia->input->post("survival"))) {
            throw new BlankFieldException("survival");
        }
        if (!is_numeric($mysidia->input->post("level"))) {
            throw new BlankFieldException("level");
        }
    }
}
