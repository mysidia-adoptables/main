<?php

namespace Controller\Main;

use Model\DomainModel\AdoptNotfoundException;
use Model\DomainModel\BreedingException;
use Model\DomainModel\OwnedAdoptable;
use Model\Settings\BreedingSettings;
use Resource\Collection\LinkedList;
use Resource\Core\AppController;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;
use Resource\GUI\Component\Link;
use Resource\Native\Integer;
use Resource\Utility\Date;
use Service\ApplicationService\BreedingService;

class BreedingController extends AppController
{
    public function __construct()
    {
        parent::__construct("member");
        $mysidia = Registry::get("mysidia");
        if (!$mysidia->user->hasPermission("canbreed")) {
            throw new NoPermissionException("permission");
        }
    }

    public function index()
    {
        $mysidia = Registry::get("mysidia");
        $settings = new BreedingSettings($mysidia->db);
        if ($settings->system != "enabled") {
            throw new InvalidActionException("system");
        }

        if ($mysidia->input->post("submit")) {
            if ($mysidia->input->post("female") == "none" || $mysidia->input->post("male") == "none") {
                throw new InvalidIDException("none_select");
            }

            try {
                $female = new OwnedAdoptable($mysidia->input->post("female"));
                $male = new OwnedAdoptable($mysidia->input->post("male"));
                $breedingService = new BreedingService($female, $male, $settings);
                $validator = $breedingService->getValidator("all");
                $validator->validate();
            } catch (AdoptNotfoundException) {
                throw new InvalidIDException("none_exist");
            } catch (BreedingException $bre) {
                $status = $bre->getmessage();
                throw new InvalidActionException($status);
            }

            if ($settings->method == "advanced") {
                $species = $breedingService->getBabySpecies();
            }
            $breedingService->getBabyAdopts($species);
            $breedingService->breed();
            $num = $breedingService->countOffsprings();

            if ($num > 0) {
                $offsprings = $breedingService->getOffsprings();
                $offspringID = $mysidia->db->select("owned_adoptables", ["aid"], "1 ORDER BY aid DESC LIMIT 1")->fetchColumn() - $num + 1;
                $links = new LinkedList();
                foreach ($offsprings as $offspring) {
                    $image = $offspring->getEggImage(Model::GUI);
                    $links->add(new Link("myadopts/manage/{$offspringID}", $image));
                    $offspringID++;
                }
                $this->setField("links", $links);
            } else {
                $this->setField("links", null);
            }
            $this->setField("numOffsprings", new Integer($num));
            return;
        }

        $this->setField("settings", $settings);
        $current = new Date();
        $lasttime = $current->getTimestamp() - (($settings->interval) * 24 * 60 * 60);

        $stmt = $mysidia->db->select("owned_adoptables", ["name", "aid"], "owner = '{$mysidia->user->getID()}' AND gender = 'f' AND currentlevel >= {$settings->level} AND lastbred <= '{$lasttime}'");
        $female = ($stmt->rowcount() == 0) ? null : $mysidia->db->fetchMap($stmt);
        $this->setField("femaleMap", $female);

        $stmt2 = $mysidia->db->select("owned_adoptables", ["name", "aid"], "owner = '{$mysidia->user->getID()}' AND gender = 'm' AND currentlevel >= {$settings->level} AND lastbred <= '{$lasttime}'");
        $male = ($stmt->rowcount() == 0) ? null : $mysidia->db->fetchMap($stmt2);
        $this->setField("maleMap", $male);
    }
}
