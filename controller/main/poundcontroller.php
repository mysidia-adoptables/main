<?php

namespace Controller\Main;

use Model\DomainModel\OwnedAdoptable;
use Model\DomainModel\PoundException;
use Model\Settings\PoundSettings;
use Resource\Collection\HashMap;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;
use Resource\Native\Integer;
use Resource\Native\MysString;
use Service\ApplicationService\PoundService;

class PoundController extends AppController
{
    private $poundService;
    private $settings;

    public function __construct()
    {
        parent::__construct("member");
        $mysidia = Registry::get("mysidia");
        if (!$mysidia->user->hasPermission("canpound")) {
            throw new NoPermissionException("denied");
        }

        $this->settings = new PoundSettings($mysidia->db);
        $this->poundService = new PoundService($this->settings);
        if ($this->settings->system == "no") {
            throw new InvalidActionException("pound_disabled");
        }
        if ($this->settings->adopt == "no" && $mysidia->input->action() == "index") {
            throw new InvalidActionException("readopt_disabled");
        }
    }

    public function index()
    {
        $poundMap = new HashMap();
        $poundAdopts = $this->poundService->getPoundedAdopts();
        if (empty($poundAdopts)) {
            throw new InvalidActionException("There are no adoptables at this time.");
        }
        $poundIterator = $poundAdopts->iterator();
        while ($poundIterator->hasNext()) {
            $poundAdopt = $poundIterator->next();
            $poundMap->put($poundAdopt, new Integer($this->poundService->getCost($poundAdopt, "readopt")));
        }
        $this->setField("poundMap", $poundMap);
    }

    public function pound($aid, $confirm = null)
    {
        $adopt = new OwnedAdoptable($aid);
        if ($confirm) {
            try {
                $cost = $this->poundService->pound($aid);
                $this->setField("cost", new Integer($cost));
            } catch (PoundException $poe) {
                $status = $poe->getMessage();
                throw new InvalidActionException($status);
            }
        }
        $this->setField("adopt", $adopt);
        $this->setField("confirm", $confirm ? new MysString($confirm) : null);
    }

    public function adopt()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            try {
                $cost = $this->poundService->readopt($mysidia->input->post("aid"));
                $this->setField("cost", new Integer($cost));
                return;
            } catch (InvalidIDException $iie) {
                $this->setFlags("global_id", $iie->getmessage());
                return;
            } catch (NoPermissionException $npe) {
                $this->setFlags("global_action", $npe->getmessage());
                return;
            } catch (PoundException $poe) {
                $status = $poe->getMessage();
                throw new InvalidActionException($status);
            }
        }
        throw new InvalidIDException("global_id");
    }
}
