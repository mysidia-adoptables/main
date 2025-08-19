<?php

namespace Controller\Main;

use Exception;
use Model\DomainModel\TradeException;
use Model\DomainModel\TradeOffer;
use Model\Settings\TradeSettings;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Exception\NoPermissionException;
use Resource\Native\MysString;
use Service\ApplicationService\TradeService;

class MytradesController extends AppController
{

    private $settings;
    private $tradeService;

    public function __construct()
    {
        parent::__construct("member");
        $mysidia = Registry::get("mysidia");
        $this->settings = new TradeSettings($mysidia->db);
        $this->tradeService = new TradeService($this->settings);

        if ($this->settings->system != "enabled") {
            throw new NoPermissionException("disabled");
        }
        if (!$mysidia->user->hasPermission("cantrade")) {
            throw new NoPermissionException("permission");
        }
    }

    public function index()
    {
        $mysidia = Registry::get("mysidia");
        $stmt = $mysidia->db->select("trade", [], " type='private' AND recipient='{$mysidia->user->getID()}' AND status='pending'");
        if ($stmt->rowCount() == 0) throw new TradeException("empty");
        $offers = new ArrayList;
        while ($dto = $stmt->fetchObject()) {
            $offers->add(new TradeOffer($dto->tid, $dto));
        }
        $this->setField("offers", $offers);
    }

    public function accept($tid = null, $confirm = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$tid) throw new TradeException("accept_none");
        $offer = new TradeOffer($tid);
        $this->setField("confirm", $confirm ? new MysString($confirm) : null);

        if ($confirm) {
            try {
                if (!$mysidia->session->fetch("tid")) throw new Exception("Session already expired");
                $validator = $this->tradeService->getValidator($offer);
                $validator->validate();
                $this->tradeService->completeTrade($offer);
                $validator->setStatus("complete");
                $this->tradeService->syncronize($offer);
                $mysidia->session->terminate("tid");
            } catch (TradeException $tre) {
                throw new InvalidActionException($tre->getMessage());
            }
            return;
        }

        $this->setField("offer", $offer);
        $mysidia->session->assign("tid", $tid, true);
    }

    public function decline($tid = null, $confirm = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$tid) throw new TradeException("decline_none");
        $offer = new TradeOffer($tid);
        $this->setField("confirm", $confirm ? new MysString($confirm) : null);

        if ($confirm) {
            if (!$mysidia->session->fetch("tid")) throw new Exception("Session already expired");
            $offer = new TradeOffer($tid);
            $offer->decline();
            $mysidia->session->terminate("tid");
            return;
        }
        $this->setField("offer", $offer);
        $mysidia->session->assign("tid", $tid, true);
    }
}
