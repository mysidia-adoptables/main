<?php

namespace Controller\Main;

use ArrayObject;
use Model\DomainModel\Member;
use Model\DomainModel\TradeException;
use Model\DomainModel\TradeOffer;
use Model\Settings\TradeSettings;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Exception\NoPermissionException;
use Resource\Native\Integer;
use Resource\Native\MysString;
use Service\ApplicationService\TradeService;

class TradeController extends AppController
{
    private $settings;
    private $tradeService;

    public function __construct()
    {
        $mysidia = Registry::get("mysidia");
        parent::__construct("member");
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
        $additional = new ArrayList();
        if ($this->settings->moderate == "enabled") {
            $additional->add(new MysString("moderate"));
        }
        if ($this->settings->multiple == "enabled") {
            $additional->add(new MysString("multiple"));
        }
        if ($this->settings->partial == "enabled") {
            $additional->add(new MysString("partial"));
        }
        if ($this->settings->public == "enabled") {
            $additional->add(new MysString("public"));
        }
        $this->setField("tax", new Integer($this->settings->tax));
        $this->setField("additional", $additional);
    }

    public function offer($type = null, $id = null)
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            $form = new ArrayObject(["public" => $mysidia->input->post("public"), "partial" => $mysidia->input->post("partial"), "recipient" => $mysidia->input->post("recipient"),
                "adoptOffered" => $mysidia->input->post("adoptOffered"), "adoptWanted" => $mysidia->input->post("adoptWanted"), "itemOffered" => $mysidia->input->post("itemOffered"),
                "itemWanted" => $mysidia->input->post("itemWanted"), "cashOffered" => $mysidia->input->post("cashOffered"), "message" => $mysidia->input->post("message")]);
            try {
                $offer = $this->tradeService->createTrade($form);
                $validator = $this->tradeService->getValidator($offer);
                $validator->validate();
                $this->tradeService->saveTrade($offer);
            } catch (TradeException $tie) {
                throw new InvalidActionException($tie->getMessage());
            }
            $this->setField("moderate", new MysString($this->settings->moderate));
            return;
        }

        $mysidia->session->assign("offer", 1, true);
        $fields = $this->tradeService->getFormFields("offer", $type, $id);
        if (($fields["recipient"] instanceof Member) && $fields["recipient"]->isCurrentUser()) {
            throw new InvalidActionException("recipient_duplicate");
        }

        foreach ($fields as $key => $field) {
            $this->setField($key, $field);
        }
        $this->setField("multiSelect", new MysString($this->settings->multiple));
        $this->setField("publicTrade", new MysString($this->settings->public));
        $this->setField("partialTrade", new MysString($this->settings->partial));
    }

    public function publics($tid = null)
    {
        $mysidia = Registry::get("mysidia");
        if ($this->settings->public == "disabled") {
            throw new InvalidActionException("public_disabled");
        }
        if ($mysidia->input->post("submit")) {
            $form = new ArrayObject(["public" => $mysidia->input->post("public"), "recipient" => $mysidia->input->post("recipient"), "adoptOffered" => $mysidia->input->post("adoptOffered"),
                "adoptWanted" => $mysidia->input->post("adoptWanted"), "itemOffered" => $mysidia->input->post("itemOffered"),
                "itemWanted" => $mysidia->input->post("itemWanted"), "cashOffered" => $mysidia->input->post("cashOffered"), "message" => $mysidia->input->post("message")]);
            try {
                $offer = $this->tradeService->createFromPublicTrade($form, $tid);
                $validator = $this->tradeService->getValidator($offer);
                $validator->validate();
                $this->tradeService->saveTrade($offer);
                $privateID = $mysidia->db->select("trade", ["tid"], "1 ORDER BY tid DESC LIMIT 1")->fetchColumn();
                $this->tradeService->associate($tid, $privateID);
                return;
            } catch (TradeException $tre) {
                throw new InvalidActionException($tre->getmessage());
            }
        }

        $this->setField("tid", $tid ? new Integer($tid) : null);
        $this->setField("multiSelect", new MysString($this->settings->multiple));
        if ($tid) {
            $offer = new TradeOffer($tid);
            $this->setField("offer", $offer);
            $fields = $this->tradeService->getFormFields("publics", "tid", $tid);
            foreach ($fields as $key => $field) {
                $this->setField($key, $field);
            }
            return;
        }

        $stmt = $mysidia->db->select("trade", [], "type = 'public' AND status = 'pending'");
        $offers = new ArrayList();
        while ($dto = $stmt->fetchObject()) {
            $offers->add(new TradeOffer($dto->tid, $dto));
        }
        $this->setField("offers", $offers);
    }

    public function privates($tid = null)
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            $form = new ArrayObject(["recipient" => $mysidia->input->post("recipient"), "adoptOffered" => $mysidia->input->post("adoptOffered"), "adoptWanted" => $mysidia->input->post("adoptWanted"),
                "itemOffered" => $mysidia->input->post("itemOffered"), "itemWanted" => $mysidia->input->post("itemWanted"),
                "cashOffered" => $mysidia->input->post("cashOffered"), "message" => $mysidia->input->post("message")]);
            try {
                $offer = $this->tradeService->createTrade($form, $tid);
                if ($mysidia->input->post("cancel") == "yes") {
                    $offer->cancel();
                } else {
                    $validator = $this->tradeService->getValidator($offer);
                    $validator->validate();
                    $offer->revise();
                }
                return;
            } catch (TradeException $tre) {
                throw new InvalidActionException($tre->getmessage());
            }
        }

        $this->setField("tid", $tid ? new Integer($tid) : null);
        $this->setField("multiSelect", new MysString($this->settings->multiple));
        if ($tid) {
            $offer = new TradeOffer($tid);
            $this->setField("offer", $offer);
            $fields = $this->tradeService->getFormFields("privates", "tid", $tid);
            foreach ($fields as $key => $field) {
                $this->setField($key, $field);
            }
            return;
        }
        $stmt = $mysidia->db->select("trade", [], "type = 'private' AND sender = '{$mysidia->user->getID()}' AND status = 'pending'");
        $offers = new ArrayList();
        while ($dto = $stmt->fetchObject()) {
            $offers->add(new TradeOffer($dto->tid, $dto));
        }
        $this->setField("offers", $offers);
    }

    public function partials($tid = null)
    {
        $mysidia = Registry::get("mysidia");
        if ($this->settings->partial == "disabled") {
            throw new InvalidActionException("partial_disabled");
        }
        $this->setField("tid", $tid ? new Integer($tid) : null);

        if ($mysidia->input->post("submit")) {
            $form = new ArrayObject(["partial" => $mysidia->input->post("partial"), "recipient" => $mysidia->input->post("recipient"), "adoptOffered" => $mysidia->input->post("adoptOffered"),
                "adoptWanted" => $mysidia->input->post("adoptWanted"), "itemOffered" => $mysidia->input->post("itemOffered"),
                "itemWanted" => $mysidia->input->post("itemWanted"), "cashOffered" => $mysidia->input->post("cashOffered"), "message" => $mysidia->input->post("message")]);
            try {
                $offer = $this->tradeService->createTrade($form, $tid);
                if ($mysidia->input->post("decline") == "yes") {
                    $offer->decline();
                } else {
                    $validator = $this->tradeService->getValidator($offer);
                    $validator->validate();
                    $offer->reverse(($mysidia->input->post("partial") == "yes") ? "partial" : "private");
                }
                return;
            } catch (TradeException $tre) {
                throw new InvalidActionException($tre->getmessage());
            }
        }

        $this->setField("multiSelect", new MysString($this->settings->multiple));
        if ($tid) {
            $offer = new TradeOffer($tid);
            $this->setField("offer", $offer);
            $fields = $this->tradeService->getFormFields("partials", "tid", $tid);
            foreach ($fields as $key => $field) {
                $this->setField($key, $field);
            }
            return;
        }
        $stmt = $mysidia->db->select("trade", [], "type = 'partial' AND recipient = '{$mysidia->user->getID()}' AND status = 'pending'");
        $offers = new ArrayList();
        while ($dto = $stmt->fetchObject()) {
            $offers->add(new TradeOffer($dto->tid, $dto));
        }
        $this->setField("offers", $offers);
    }
}
