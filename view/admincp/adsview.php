<?php

namespace View\AdminCP;

use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\PasswordField;
use Resource\GUI\Component\TextArea;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\Form;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Service\Builder\TableBuilder;
use Service\Helper\TableHelper;

class AdsView extends View
{
    public function index()
    {
        parent::index();
        $ads = $this->getField("ads");
        $document = $this->document;

        $adsTable = new TableBuilder("ads");
        $adsTable->setAlign(new Align("center", "middle"));
        $adsTable->buildHeaders("Ad", "Page", "Impressions", "Actual Impressions", "date", "Status", "Edit", "Delete");
        $adsTable->setHelper(new TableHelper());

        $iterator = $ads->iterator();
        while ($iterator->hasNext()) {
            $ad = $iterator->next();
            $cells = new LinkedList();
            $cells->add(new TCell($ad->getAdName()));
            $cells->add(new TCell($ad->getPage()));
            $cells->add(new TCell($ad->getImpressions()));
            $cells->add(new TCell($ad->getActualImpressions()));
            $cells->add(new TCell($ad->getDate("Y-m-d")));
            $cells->add(new TCell($adsTable->getHelper()->getStatusImage($ad->getStatus())));
            $cells->add(new TCell($adsTable->getHelper()->getEditLink($ad->getID())));
            $cells->add(new TCell($adsTable->getHelper()->getDeleteLink($ad->getID())));
            $adsTable->buildRow($cells);
        }
        $document->add($adsTable);
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->added_title);
            $document->addLangvar($this->lang->added);
            return;
        }

        $document->setTitle($this->lang->add_title);
        $document->addLangvar($this->lang->add);
        $adForm = new Form("addads", "add", "post");
        $adForm->add(new Comment("Ad Code:"));
        $adForm->add(new TextArea("description", "", 4, 45));
        $adForm->add(new Comment("Ad Campain Name: ", false));
        $adForm->add(new TextField("adname"));
        $adForm->add(new Comment("Page to run this ad on: ", false));
        $adForm->add(new TextField("adpage"));
        $adForm->add(new Comment($this->lang->page));
        $adForm->add(new Comment("Max Impressions Allowed: ", false));
        $adForm->add(new TextField("impressions", "", 8));
        $adForm->add(new Comment($this->lang->impressions));
        $adForm->add(new Button("Start Ad Campain", "submit", "submit"));
        $adForm->add(new Button("Reset Ad Campain", "reset", "reset"));
        $document->add($adForm);
    }

    public function edit()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $ad = $this->getField("ad");
        if (!$ad) {
            // An Ad has yet been selected, return to the index page.
            $this->index();
        } elseif ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->edited_title);
            $document->addLangvar($this->lang->edited);
        } else {
            $document->setTitle($this->lang->edit_title);
            $document->addLangvar($this->lang->edit);
            $adForm = new Form("editads", $ad->getID(), "post");
            $adForm->add(new Comment($this->lang->edit2));
            $adForm->add(new Comment("Ad Code:"));
            $adForm->add(new TextArea("description", $ad->getText(), 4, 45));
            $adForm->add(new Comment("Ad Campain Name: ", false));
            $adForm->add(new TextField("adname", $ad->getAdName()));
            $adForm->add(new Comment("Page to run this ad on: "));
            $adForm->add(new TextField("adpage", $ad->getPage()));
            $adForm->add(new Comment($this->lang->page));
            $adForm->add(new Comment("Max Impressions Allowed: ", false));
            $adForm->add(new TextField("impressions", $ad->getImpressions(), 8));
            $adForm->add(new Comment($this->lang->impressions));
            $adForm->add(new PasswordField("hidden", "aimp", $ad->getActualImpressions()));
            $adForm->add(new Button("Edit Ad Campain", "submit", "submit"));
            $adForm->add(new Button("Reset Ad Campain", "reset", "reset"));
            $document->add($adForm);
        }
    }

    public function delete()
    {
        $document = $this->document;
        $ad = $this->getField("ad");
        if (!$ad) {
            // An Add has yet been selected, return to the index page.
            $this->index();
            return;
        }
        $document->setTitle($this->lang->delete_title);
        $document->addLangvar($this->lang->delete);
    }
}
