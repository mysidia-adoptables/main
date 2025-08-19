<?php

namespace View\Main;

use Resource\Collection\LinkedList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\CheckBox;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Component\PasswordField;
use Resource\GUI\Component\TextArea;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\Form;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Service\Builder\TableBuilder;
use Service\Helper\TradeFormHelper;

class TradeView extends View
{

    public function index()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $document->setTitle($this->lang->title);
        $document->addLangvar($this->lang->default . $this->lang->section);

        $tax = $this->getField("tax");
        $additionalList = $this->getField("additional");
        $additionalIterator = $additionalList->iterator();
        while ($additionalIterator->hasNext()) {
            $additional = (string)$additionalIterator->next();
            $document->add(new Image("templates/icons/yes.gif"));
            $document->addLangvar($this->lang->{$additional});
        }

        $document->addLangvar($this->lang->section2);
        $document->add(new Image("templates/icons/warning.gif"));
        $document->addLangvar("{$this->lang->tax} {$tax->getValue()} {$mysidia->settings->cost}. {$this->lang->tax2}");
        $document->add(new Image("templates/icons/next.gif"));
        $document->addLangvar($this->lang->start);
        $document->add(new Link("trade/offer", "Let's start a trade now!", true));
        $document->add(new Image("templates/icons/next.gif"));
        $document->addLangvar($this->lang->start2);
        $document->add(new Link("trade/privates", "Revise my Private Trade Offers"));
    }

    public function offer()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->offered_title);
            $document->addLangvar($this->lang->offered);
            $moderate = (string)$this->getField("moderate");
            if ($moderate == "enabled") {
                $document->add(new Image("templates/icons/warning.gif"));
                $document->addLangvar($this->lang->moderated);
            }
            return;
        }

        $document->setTitle($this->lang->offer_title);
        $document->addLangvar($this->lang->offer);
        $recipient = $this->getField("recipient");
        $multiSelect = (string)$this->getField("multiSelect");
        $tradeHelper = new TradeFormHelper($this->getLangvars(), ($multiSelect == "enabled"));

        $tradeForm = new Form("tradeform", "", "post");
        $tradeForm->add($tradeHelper->getRecipient($recipient));
        $tradeForm->add($tradeHelper->getAdoptOffered(null, $this->getField("adoptOffered")));
        $tradeForm->add($tradeHelper->getAdoptWanted($this->getField("adoptSelected"), $this->getField("adoptWanted")));
        $tradeForm->add($tradeHelper->getItemOffered(null, $this->getField("itemOffered")));
        $tradeForm->add($tradeHelper->getItemWanted($this->getField("itemSelected"), $this->getField("itemWanted")));
        $tradeForm->add(new Comment($this->lang->cash_offered));
        $tradeForm->add(new TextField("cashOffered"));
        $tradeForm->add(new Comment($this->lang->message));
        $tradeForm->add(new TextArea("message", "Enter your trade message here, make sure it is brief."));

        $publicTrade = (string)$this->getField("publicTrade");
        $partialTrade = (string)$this->getField("partialTrade");
        if ($publicTrade == "enabled" && !$recipient) $tradeForm->add($tradeHelper->getPublicOffer($recipient));
        if ($partialTrade == "enabled") $tradeForm->add($tradeHelper->getPartialOffer($recipient));
        $tradeForm->add(new Button("Submit Trade Offer!", "submit", "submit"));
        $document->add($tradeForm);
    }

    public function publics()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->offered_title);
            $document->addLangvar($this->lang->offered);
            return;
        }

        $document->setTitle($this->lang->view_public_title);
        $multiSelect = (string)$this->getField("multiSelect");
        $tradeHelper = new TradeFormHelper($this->getLangvars(), ($multiSelect == "enabled"));
        $tid = $this->getField("tid");

        if ($tid) {
            $document->addLangvar($this->lang->view_public2);
            $offer = $this->getField("offer");
            $recipient = $mysidia->user;
            $tradeForm = new Form("tradeform", "", "post");
            $tradeForm->add(new Image("templates/icons/warning.gif"));
            $tradeForm->add(new Comment($this->lang->recipient . $offer->getSender(Model::MODEL)->getUsername(), true, "b"));
            $tradeForm->add($tradeHelper->getAdoptOfferedPublic($recipient, $this->getField("adoptOffered")));
            $tradeForm->add($tradeHelper->getAdoptWantedPublic($recipient, $offer->getAdoptOffered()));
            $tradeForm->add($tradeHelper->getItemOfferedPublic($recipient, $this->getField("itemOffered")));
            $tradeForm->add($tradeHelper->getItemWantedPublic($recipient, $offer->getItemOffered()));

            $tradeForm->add(new Comment($this->lang->message));
            $tradeForm->add(new TextArea("message", "Enter your trade message here, make sure it is brief."));
            $tradeForm->add(new PasswordField("hidden", "recipient", $offer->getSender()));
            $tradeForm->add(new PasswordField("hidden", "cashOffered", 0));
            $tradeForm->add(new PasswordField("hidden", "publics", $tid->getValue()));
            $tradeForm->add(new Button("Submit Trade Offer!", "submit", "submit"));
            $document->add($tradeForm);
            return;
        }

        $document->addLangvar($this->lang->view_public);
        $offers = $this->getField("offers");
        if ($offers->size() == 0) {
            $document->addLangvar($this->lang->view_public_empty);
            return;
        }
        $tradeTable = new TableBuilder("tradetable", 700);
        $tradeTable->setAlign(new Align("center", "middle"));
        $tradeTable->buildHeaders("ID", "Sender", "Adopt Offered", "Adopt Wanted", "Item Offered", "Item Wanted", "Message", "View");

        $iterator = $offers->iterator();
        while ($iterator->hasNext()) {
            $offer = $iterator->next();
            $cells = new LinkedList;
            $cells->add(new TCell($offer->getID()));
            $cells->add(new TCell($offer->getSender(Model::MODEL)->getUsername()));
            $cells->add(new TCell($tradeHelper->getAdoptImages($offer->getAdoptOffered())));
            $cells->add(new TCell($tradeHelper->getAdoptList($offer->getAdoptWanted())));
            $cells->add(new TCell($tradeHelper->getItemImages($offer->getItemOffered())));
            $cells->add(new TCell($tradeHelper->getItemList($offer->getItemWanted())));
            $cells->add(new TCell($offer->getMessage()));
            $cells->add(new Link("trade/publics/{$offer->getID()}", new Image("templates/icons/next.gif")));
            $tradeTable->buildRow($cells);
        }
        $document->add($tradeTable);
    }

    public function privates()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->revise_title);
            $document->addLangvar($this->lang->revise);
            return;
        }

        $document->setTitle($this->lang->view_private_title);
        $multiSelect = (string)$this->getField("multiSelect");
        $tradeHelper = new TradeFormHelper($this->getLangvars(), ($multiSelect == "enabled"));
        $tid = $this->getField("tid");

        if ($tid) {
            $offer = $this->getField("offer");
            $document->addLangvar($this->lang->view_private2);
            $tradeForm = new Form("tradeform", "", "post");
            $tradeForm->add($tradeHelper->getRecipient($this->getField("recipient")));
            $tradeForm->add($tradeHelper->getAdoptOffered($offer->getAdoptOffered(), $this->getField("adoptOffered")));
            $tradeForm->add($tradeHelper->getAdoptWanted($offer->getAdoptWanted(), $this->getField("adoptWanted")));
            $tradeForm->add($tradeHelper->getItemOffered($offer->getItemOffered(), $this->getField("itemOffered")));
            $tradeForm->add($tradeHelper->getItemWanted($offer->getItemWanted(), $this->getField("itemWanted")));
            $tradeForm->add(new Comment($this->lang->cash_offered));
            $tradeForm->add(new TextField("cashOffered", $offer->getCashOffered()));
            $tradeForm->add(new Comment($this->lang->message));
            $tradeForm->add(new TextArea("message", $offer->getMessage()));
            $tradeForm->add(new CheckBox("Cancel this trade offer?", "cancel", "yes"));
            $tradeForm->add(new PasswordField("hidden", "recipient", $offer->getRecipient()));
            $tradeForm->add(new Button("Revise Trade Offer!", "submit", "submit"));
            $document->add($tradeForm);
            return;
        }

        $document->addLangvar($this->lang->view_private);
        $offers = $this->getField("offers");
        if ($offers->size() == 0) {
            $document->addLangvar($this->lang->view_private_empty);
            return;
        }
        $tradeTable = new TableBuilder("tradetable", 700);
        $tradeTable->setAlign(new Align("center", "middle"));
        $tradeTable->buildHeaders("ID", "Recipient", "Adopt Offered", "Adopt Wanted", "Item Offered", "Item Wanted", "Cash Offered", "Message", "Revise");

        $iterator = $offers->iterator();
        while ($iterator->hasNext()) {
            $offer = $iterator->next();
            $cells = new LinkedList;
            $cells->add(new TCell($offer->getID()));
            $cells->add(new TCell($offer->getRecipientName()));
            $cells->add(new TCell($tradeHelper->getAdoptImages($offer->getAdoptOffered())));
            $cells->add(new TCell($tradeHelper->getAdoptImages($offer->getAdoptWanted())));
            $cells->add(new TCell($tradeHelper->getItemImages($offer->getItemOffered())));
            $cells->add(new TCell($tradeHelper->getItemImages($offer->getItemWanted())));
            $cells->add(new TCell($offer->getCashOffered()));
            $cells->add(new TCell($offer->getMessage()));
            $cells->add(new TCell(new Link("trade/privates/{$offer->getID()}", new Image("templates/icons/cog.gif"))));
            $tradeTable->buildRow($cells);
        }
        $document->add($tradeTable);
    }

    public function partials()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $tid = $this->getField("tid");
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->revise_title);
            $document->addLangvar("{$this->lang->revise} {$tid}");
            return;
        }

        $document->setTitle($this->lang->view_partial_title);
        $multiSelect = (string)$this->getField("multiSelect");
        $tradeHelper = new TradeFormHelper($this->getLangvars(), ($multiSelect == "enabled"));

        if ($tid) {
            $offer = $this->getField("offer");
            $document->addLangvar($this->lang->view_partial2);
            $tradeForm = new Form("tradeform", "", "post");
            $tradeForm->add(new Comment("The partial trade offer was originally sent by: {$offer->getSenderName()}"));
            $tradeForm->add($tradeHelper->getAdoptOffered($offer->getAdoptWanted(), $this->getField("adoptOffered")));
            $tradeForm->add($tradeHelper->getAdoptWanted($offer->getAdoptOffered(), $this->getField("adoptWanted")));
            $tradeForm->add($tradeHelper->getItemOffered($offer->getItemWanted(), $this->getField("itemOffered")));
            $tradeForm->add($tradeHelper->getItemWanted($offer->getItemOffered(), $this->getField("itemWanted")));
            $tradeForm->add(new Comment($this->lang->cash_offered));
            $tradeForm->add(new TextField("cashOffered", 0));
            $tradeForm->add(new Comment($this->lang->message));
            $tradeForm->add(new TextArea("message", "Enter your reply message here, make sure it is brief. "));
            $tradeForm->add(new CheckBox("Decline this trade offer?", "decline", "yes"));
            $tradeForm->add(new Comment);
            $tradeForm->add(new CheckBox("This is a partial trade offer", "partial", "yes", "yes"));
            $tradeForm->add(new PasswordField("hidden", "sender", $offer->getRecipient()));
            $tradeForm->add(new PasswordField("hidden", "recipient", $offer->getSender()));
            $tradeForm->add(new Button("Submit Trade Offer!", "submit", "submit"));
            $document->add($tradeForm);
            return;
        }

        $document->addLangvar($this->lang->view_partial);
        $offers = $this->getField("offers");
        if ($offers->size() == 0) {
            $document->addLangvar($this->lang->view_partial_empty);
            return;
        }
        $tradeTable = new TableBuilder("tradetable", 700);
        $tradeTable->setAlign(new Align("center", "middle"));
        $tradeTable->buildHeaders("ID", "Sender", "Adopt Offered", "Adopt Wanted", "Item Offered", "Item Wanted", "Cash Offered", "Message", "Reply");

        $iterator = $offers->iterator();
        while ($iterator->hasNext()) {
            $offer = $iterator->next();
            $cells = new LinkedList;
            $cells->add(new TCell($offer->getID()));
            $cells->add(new TCell($offer->getSenderName()));
            $cells->add(new TCell($tradeHelper->getAdoptImages($offer->getAdoptOffered())));
            $cells->add(new TCell($tradeHelper->getAdoptImages($offer->getAdoptWanted())));
            $cells->add(new TCell($tradeHelper->getItemImages($offer->getItemOffered())));
            $cells->add(new TCell($tradeHelper->getItemImages($offer->getItemWanted())));
            $cells->add(new TCell($offer->getCashOffered()));
            $cells->add(new TCell($offer->getMessage()));
            $cells->add(new TCell(new Link("trade/partials/{$offer->getID()}", new Image("templates/icons/status.gif"))));
            $tradeTable->buildRow($cells);
        }
        $document->add($tradeTable);
    }
}
