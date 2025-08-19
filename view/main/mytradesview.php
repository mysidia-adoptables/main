<?php

namespace View\Main;

use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Paragraph;
use Resource\GUI\Element\Align;
use Service\Builder\TableBuilder;
use Service\Helper\TradeFormHelper;

class MytradesView extends View
{
    public function index()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $document->setTitle($mysidia->user->getUsername() . $this->lang->title);
        $document->addLangvar($this->lang->default . $this->lang->warning);
        $offers = $this->getField("offers");

        $tradeTable = new TableBuilder("tradetable", 700);
        $tradeTable->setAlign(new Align("center", "middle"));
        $tradeTable->buildHeaders("ID", "Sender", "Adopt Offered", "Adopt Wanted", "Item Offered", "Item Wanted", "Cash Offered", "Message", "Accept", "Decline");
        $tradeHelper = new TradeFormHelper($this->getLangvars());

        $iterator = $offers->iterator();
        while ($iterator->hasNext()) {
            $offer = $iterator->next();
            $cells = new LinkedList();
            $cells->add(new TCell($offer->getID()));
            $cells->add(new TCell($offer->getSenderName()));
            $cells->add(new TCell($tradeHelper->getAdoptImages($offer->getAdoptOffered())));
            $cells->add(new TCell($tradeHelper->getAdoptImages($offer->getAdoptWanted())));
            $cells->add(new TCell($tradeHelper->getItemImages($offer->getItemOffered())));
            $cells->add(new TCell($tradeHelper->getItemImages($offer->getItemWanted())));
            $cells->add(new TCell($offer->getCashOffered()));
            $cells->add(new TCell($offer->getMessage()));
            $cells->add(new TCell(new Link("mytrades/accept/{$offer->getID()}", new Image("templates/icons/yes.gif"))));
            $cells->add(new TCell(new Link("mytrades/decline/{$offer->getID()}", new Image("templates/icons/delete.gif"))));
            $tradeTable->buildRow($cells);
        }
        $document->add($tradeTable);
    }

    public function accept()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $confirm = (string)$this->getField("confirm");
        if ($confirm) {
            $document->setTitle($this->lang->accepted_title);
            $document->addLangvar($this->lang->accepted);
            return;
        }

        $document->setTitle($this->lang->accept_title);
        $document->addLangvar($this->lang->accept);
        $offer = $this->getField("offer");
        $tradeHelper = new TradeFormHelper($this->getLangvars());

        $document->addLangvar($this->lang->review);
        $document->add(new Image("templates/icons/warning.gif"));
        $document->add(new Comment($this->lang->review_partner . $offer->getSenderName(), true, "b"));
        $document->add(new Comment());
        $document->add(new Image("templates/icons/next.gif"));
        $document->addLangvar($this->lang->review_adoptoffered);
        $document->add($tradeHelper->getAdoptImages($offer->getAdoptOffered(), false));
        $document->add(new Image("templates/icons/next.gif"));
        $document->addLangvar($this->lang->review_adoptwanted);
        $document->add($tradeHelper->getAdoptImages($offer->getAdoptWanted(), false));

        $document->add(new Image("templates/icons/next.gif"));
        $document->addLangvar($this->lang->review_itemoffered);
        $document->add($tradeHelper->getItemImages($offer->getItemOffered(), false));
        $document->add(new Image("templates/icons/next.gif"));
        $document->addLangvar($this->lang->review_itemwanted);
        $document->add($tradeHelper->getItemImages($offer->getItemWanted(), false));

        $document->add(new Image("templates/icons/next.gif"));
        $document->addLangvar($this->lang->review_cashoffered . $offer->getCashOffered() . " " . $mysidia->settings->cost);
        $document->add(new Comment("<br>"));
        $document->add(new Image("templates/icons/warning.gif"));
        $document->addLangvar($this->lang->review_message);
        $document->add(new Paragraph(new Comment($offer->getMessage(), true, "b")));
        $document->add(new Link("mytrades/accept/{$offer->getID()}/confirm", "Yes, I confirm my action!", true));
        $document->add(new Link("mytrades", "No, take me back to the tradeoffer list."));
    }

    public function decline()
    {
        $document = $this->document;
        $confirm = (string)$this->getField("confirm");
        if ($confirm) {
            $document->setTitle($this->lang->declined_title);
            $document->addLangvar($this->lang->declined);
            $document->add(new Link("mytrades", "Click here to see all of your pending trade requests."));
            return;
        }

        $offer = $this->getField("offer");
        $document->setTitle($this->lang->decline_title);
        $document->addLangvar($this->lang->decline);
        $document->add(new Link("mytrades/decline/{$offer->getID()}/confirm", "Yes, I confirm my action!", true));
        $document->add(new Link("mytrades", "No, take me back to the tradeoffer list."));
    }
}
