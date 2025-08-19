<?php

namespace Controller\AdminCP;

use Model\DomainModel\Adoptable;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\DuplicateIDException;
use Resource\Exception\NoPermissionException;

class AdoptController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $mysidia = Registry::get("mysidia");
        if ($mysidia->usergroup->getpermission("canmanageadopts") != "yes") {
            throw new NoPermissionException("You do not have permission to manage adoptables.");
        }
    }

    public function add()
    {
        // The action of creating a new adoptable!
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            // The form has been submitted, it's time to validate data and add a record to database.
            if ($mysidia->session->fetch("acpAdopt") != "add") {
                $this->setFlag("global_error", "Session already expired...");
                return;
            }

            if (!$mysidia->input->post("type")) {
                throw new BlankFieldException("type");
            } elseif (!$mysidia->input->post("class")) {
                throw new BlankFieldException("class");
            } elseif (!$mysidia->input->post("imageurl") && $mysidia->input->post("existingimageurl") == "none") {
                throw new BlankFieldException("image");
            } elseif ($mysidia->input->post("imageurl") && $mysidia->input->post("existingimageurl") != "none") {
                throw new BlankFieldException("image2");
            } elseif (!$mysidia->input->post("cba")) {
                throw new BlankFieldException("condition");
            }

            if ($mysidia->input->post("cba") == "conditions") {
                if ($mysidia->input->post("freqcond") == "enabled" && !is_numeric($mysidia->input->post("number"))) {
                    throw new BlankFieldException("condition_freq");
                }
                if ($mysidia->input->post("datecond") == "enabled" && !$mysidia->input->post("date")) {
                    throw new BlankFieldException("condition_date");
                }
                if ($mysidia->input->post("adoptscond") == "enabled") {
                    if (!$mysidia->input->post("moreless") || !is_numeric($mysidia->input->post("morelessnum")) || !$mysidia->input->post("levelgrle") or !is_numeric($mysidia->input->post("grlelevel"))) {
                        throw new BlankFieldException("condition_moreandlevel");
                    }
                }

                if ($mysidia->input->post("maxnumcond") == "enabled" && !is_numeric($mysidia->input->post("morethannum"))) {
                    throw new BlankFieldException("maxnum");
                }
                if ($mysidia->input->post("usergroupcond") == "enabled" && !is_numeric($mysidia->input->post("usergroups"))) {
                    throw new BlankFieldException("group");
                }
            }

            if ($mysidia->input->post("alternates") == "enabled") {
                if (!is_numeric($mysidia->input->post("altoutlevel"))) {
                    throw new BlankFieldException("alternate");
                }
            }
            $typeExist = $mysidia->db->select("adoptables", ["type"], "type = :type", ["type" => $mysidia->input->post("type")])->fetchColumn();
            if ($typeExist) {
                throw new DuplicateIDException("exist");
            }

            $eggimage = ($mysidia->input->post("imageurl") && $mysidia->input->post("existingimageurl") == "none") ? $mysidia->input->post("imageurl") : $mysidia->input->post("existingimageurl");
            // insert into table adoptables
            $mysidia->db->insert("adoptables", ["id" => null, "type" => $mysidia->input->post("type"), "class" => $mysidia->input->post("class"), "description" => $mysidia->input->post("description"), "eggimage" => $eggimage, "whenisavail" => $mysidia->input->post("cba"),
                "alternates" => $mysidia->input->post("alternates"), "altoutlevel" => (int)$mysidia->input->post("altoutlevel"), "shop" => (int)$mysidia->input->post("shop"), "cost" => (int)$mysidia->input->post("cost")]);
            $id = $mysidia->db->select("adoptables", ["id"], "type = :type", ["type" => $mysidia->input->post("type")])->fetchColumn();
            // insert into table adoptables_conditions
            $mysidia->db->insert("adoptables_conditions", ["id" => $id, "type" => $mysidia->input->post("type"), "whenisavail" => $mysidia->input->post("cba"), "freqcond" => $mysidia->input->post("freqcond"), "number" => (int)$mysidia->input->post("number"), "datecond" => $mysidia->input->post("datecond"),
                "date" => $mysidia->input->post("date"), "adoptscond" => $mysidia->input->post("adoptscond"), "moreless" => $mysidia->input->post("maxnumcond"), "morelessnum" => (int)$mysidia->input->post("morethannum"), "levelgrle" => $mysidia->input->post("usergroupcond"), "grlelevel" => (int)$mysidia->input->post("usergroups")]);

            // insert our level thing
            $mysidia->db->insert("levels", ["lvid" => null, "adopt" => $id, "level" => 0, "requiredclicks" => 0, "primaryimage" => $eggimage, "rewarduser" => null, "promocode" => null]);
            $mysidia->session->terminate("acpAdopt");
            return;
        }
        $mysidia->session->assign("acpAdopt", "add", true);
    }

    public function edit()
    {
        $mysidia = Registry::get("mysidia");
        if (!$mysidia->input->post("choose")) {
            return;
        }
        $adopt = new Adoptable($mysidia->input->post("adopt"));
        if ($mysidia->input->post("submit")) {
            if ($mysidia->session->fetch("acpAdopt") != "edit") {
                $this->setFlag("global_error", "Session already expired...");
                return;
            } elseif ($mysidia->input->post("delete") == "yes") {
                $adopt->delete($mysidia->input->post("deltype"));
            } else {
                if ($mysidia->input->post("eggimage") != "" && $mysidia->input->post("eggimage") != "none") {
                    $adopt->updateEggImage($mysidia->input->post("eggimage"));
                }
                if ($mysidia->input->post("resetconds") == "yes") {
                    $adopt->resetDefaultConditions();
                }
                if ($mysidia->input->post("resetdate") == "yes" && $mysidia->input->post("date")) {
                    $adopt->enableDateConditions($mysidia->input->post("date"));
                }
                if ($mysidia->input->post("shop") && is_numeric($mysidia->input->post("cost"))) {
                    $adopt->changeShopSettings((int)$mysidia->input->post("shop"), (int)$mysidia->input->post("cost"));
                }
                if (is_numeric($mysidia->input->post("altoutlevel"))) {
                    $adopt->changeAltSettings($mysidia->input->post("alternates"), (int)$mysidia->input->post("altoutlevel"));
                }
            }
        } else {
            $mysidia->session->assign("acpAdopt", "edit", true);
            $this->setField("adopt", $adopt);
        }
    }

    public function delete()
    {

    }
}
