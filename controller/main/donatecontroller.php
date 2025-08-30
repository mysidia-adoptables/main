<?php

namespace Controller\Main;

use Model\DomainModel\Member;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Native\Integer;
use Service\ApplicationService\DonationException;

class DonateController extends AppController
{
    public function __construct()
    {
        parent::__construct("member");
    }

    public function index()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("recipient") && $mysidia->input->post("amount")) {
            $recipientName = preg_replace("/[^a-zA-Z0-9\\040]/", "", (string) $mysidia->input->post("recipient"));
            $amount = (int)$mysidia->input->post("amount");
            $recipient = new Member($recipientName);
            if ($amount < 0) {
                throw new DonationException("negative");
            } elseif ($mysidia->user->getMoney() < $amount) {
                throw new DonationException("funds");
            } elseif ($recipient->isCurrentUser()) {
                throw new DonationException("user");
            } else {
                $mysidia->user->donate($recipient, $amount);
                $this->setField("recipient", $recipient);
                $this->setField("amount", new Integer($amount));
            }
            return;
        }
    }
}
