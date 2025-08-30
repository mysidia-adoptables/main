<?php

namespace View\AdminCP;

use Resource\Collection\LinkedHashMap;
use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\TextArea;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Paragraph;
use Resource\GUI\Element\Align;
use Resource\Native\MysString;
use Resource\Utility\Date;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\TableHelper;
use Service\Helper\TradeFormHelper;

class TradeView extends View
{
    public function index()
    {
        parent::index();
        $document = $this->document;
        $helper = new TableHelper();
        $tradeTable = new TableBuilder("trade");
        $tradeTable->setAlign(new Align("center", "middle"));
        $tradeTable->buildHeaders("ID", "Type", "Sender", "Recipient", "Status", "Edit", "Delete");
        $tradeTable->setHelper($helper);

        $tradeOffers = $this->getField("tradeOffers");
        $iterator = $tradeOffers->iterator();
        while ($iterator->hasNext()) {
            $tradeOffer = $iterator->next();
            $cells = new LinkedList();
            $cells->add(new TCell($tradeOffer->getID()));
            $cells->add(new TCell($tradeOffer->getType()));
            $cells->add(new TCell($helper->getText($tradeOffer->getSenderName())));
            $cells->add(new TCell($helper->getText($tradeOffer->getRecipientName())));
            $cells->add(new TCell($tradeOffer->getStatus()));
            $cells->add(new TCell($helper->getEditLink($tradeOffer->getID())));
            $cells->add(new TCell($helper->getDeleteLink($tradeOffer->getID())));
            $tradeTable->buildRow($cells);
        }
        $document->add($tradeTable);

        $pagination = $this->getField("pagination");
        $document->addLangvar($pagination->showPage());
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
        $tradeForm = new FormBuilder("addform", "add", "post");
        $tradeTypes = new LinkedHashMap();
        $tradeTypes->put(new MysString("Private"), new MysString("private"));
        $tradeTypes->put(new MysString("Public"), new MysString("public"));
        $tradeTypes->put(new MysString("Partial"), new MysString("partial"));
        $date = new Date();

        $tradeForm->add(new Comment("<hr>Basic Information:", true, "b"));
        $tradeForm->add(new Comment("Sender: ", false, "i"));
        $tradeForm->buildDropdownList("sender", "UsernameList");
        $tradeForm->add(new Comment($this->lang->sender_explain));
        $tradeForm->add(new Comment("Recipient: ", false, "i"));
        $tradeForm->buildDropdownList("recipient", "UsernameList");
        $tradeForm->add(new Comment($this->lang->recipient_explain));
        $tradeForm->add(new Comment("Adopt Provided: ", false, "i"));
        $tradeForm->add(new TextField("adoptOffered"));
        $tradeForm->add(new Comment($this->lang->adopt_offered_explain));
        $tradeForm->add(new Comment("Adopt Requested: ", false, "i"));
        $tradeForm->add(new TextField("adoptWanted"));
        $tradeForm->add(new Comment($this->lang->adopt_wanted_explain));
        $tradeForm->add(new Comment("Item Provided: ", false, "i"));
        $tradeForm->add(new TextField("itemOffered"));
        $tradeForm->add(new Comment($this->lang->item_offered_explain));
        $tradeForm->add(new Comment("Item Requested: ", false, "i"));
        $tradeForm->add(new TextField("itemWanted"));
        $tradeForm->add(new Comment($this->lang->item_wanted_explain));
        $tradeForm->add(new Comment("Cash Offered: ", false, "i"));
        $tradeForm->add(new TextField("cashOffered", 0));
        $tradeForm->add(new Comment($this->lang->cash_offered_explain));

        $tradeForm->add(new Comment("<hr>Additional Information:", true, "b"));
        $tradeForm->add(new Comment("Trade Type: ", false, "i"));
        $tradeForm->buildRadioList("type", $tradeTypes, "private");
        $tradeForm->add(new Comment($this->lang->type_explain));
        $tradeForm->add(new Comment("Trade Message: ", true, "i"));
        $tradeForm->add(new TextArea("message", "Enter a short message here.", 6, 65));
        $tradeForm->add(new Comment($this->lang->message_explain));
        $tradeForm->add(new Comment("Trade Status: ", false, "i"));
        $tradeForm->add(new TextField("status", "pending"));
        $tradeForm->add(new Comment($this->lang->status_explain));
        $tradeForm->add(new Comment("Trade DateTime: ", false, "i"));
        $tradeForm->add(new TextField("date", $date->format("Y-m-d")));
        $tradeForm->add(new Comment($this->lang->date_explain));
        $tradeForm->add(new Button("Initiate Trade Offer", "submit", "submit"));
        $document->add($tradeForm);
    }

    public function edit()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $tradeOffer = $this->getField("tradeOffer");
        if (!$tradeOffer) {
            $this->index();
        } elseif ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->edited_title);
            $document->addLangvar($this->lang->edited);
        } else {
            $document->setTitle($this->lang->edit_title);
            $document->addLangvar($this->lang->edit);
            $tradeForm = new FormBuilder("editform", $tradeOffer->getID(), "post");
            $tradeTypes = new LinkedHashMap();
            $tradeTypes->put(new MysString("Private"), new MysString("private"));
            $tradeTypes->put(new MysString("Public"), new MysString("public"));
            $tradeTypes->put(new MysString("Partial"), new MysString("partial"));

            $tradeForm->add(new Comment("<hr>Basic Information:", true, "b"));
            $tradeForm->add(new Comment("Sender ID: ", false, "i"));
            $tradeForm->buildDropdownList("sender", "UsernameList", $tradeOffer->getSender());
            $tradeForm->add(new Comment($this->lang->sender_explain));
            $tradeForm->add(new Comment("Recipient ID: ", false, "i"));
            $tradeForm->buildDropdownList("recipient", "UsernameList", $tradeOffer->getRecipient());
            $tradeForm->add(new Comment($this->lang->recipient_explain));
            $tradeForm->add(new Comment("Adopt Provided: ", false, "i"));
            $tradeForm->add(new TextField("adoptOffered", $tradeOffer->getAdoptOfferedInfo()));
            $tradeForm->add(new Comment($this->lang->adopt_offered_explain));
            $tradeForm->add(new Comment("Adopt Requested: ", false, "i"));
            $tradeForm->add(new TextField("adoptWanted", $tradeOffer->getAdoptWantedInfo()));
            $tradeForm->add(new Comment($this->lang->adopt_wanted_explain));
            $tradeForm->add(new Comment("Item Provided: ", false, "i"));
            $tradeForm->add(new TextField("itemOffered", $tradeOffer->getItemOfferedInfo()));
            $tradeForm->add(new Comment($this->lang->item_offered_explain));
            $tradeForm->add(new Comment("Item Requested: ", false, "i"));
            $tradeForm->add(new TextField("itemWanted", $tradeOffer->getItemWantedInfo()));
            $tradeForm->add(new Comment($this->lang->item_wanted_explain));
            $tradeForm->add(new Comment("Cash Offered: ", false, "i"));
            $tradeForm->add(new TextField("cashOffered", $tradeOffer->getCashOffered()));
            $tradeForm->add(new Comment($this->lang->cash_offered_explain));

            $tradeForm->add(new Comment("<hr>Additional Information:", true, "b"));
            $tradeForm->add(new Comment("Trade Type: ", false, "i"));
            $tradeForm->buildRadioList("type", $tradeTypes, $tradeOffer->getType());
            $tradeForm->add(new Comment($this->lang->type_explain));
            $tradeForm->add(new Comment("Trade Message: ", true, "i"));
            $tradeForm->add(new TextArea("message", $tradeOffer->getMessage(), 6, 65));
            $tradeForm->add(new Comment($this->lang->message_explain));
            $tradeForm->add(new Comment("Trade Status: ", false, "i"));
            $tradeForm->add(new TextField("status", $tradeOffer->getStatus()));
            $tradeForm->add(new Comment($this->lang->status_explain));
            $tradeForm->add(new Comment("Trade DateTime: ", false, "i"));
            $tradeForm->add(new TextField("date", $tradeOffer->getDate("Y-m-d")));
            $tradeForm->add(new Comment($this->lang->date_explain));
            $tradeForm->add(new Button("Update Trade Offer", "submit", "submit"));
            $document->add($tradeForm);
        }
    }

    public function delete()
    {
        $document = $this->document;
        $tradeOffer = $this->getField("tradeOffer");
        if (!$tradeOffer) {
            $this->index();
        } else {
            $document->setTitle($this->lang->delete_title);
            $document->addLangvar($this->lang->delete);
        }
    }

    public function moderate()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $tradeOffer = $this->getField("tradeOffer");
        if ($tradeOffer) {
            // A trade offer has been selected for moderation, let's go over it!
            if ($mysidia->input->post("submit")) {
                $status = (string)$this->getField("status");
                $document->setTitle($this->lang->moderated_title);
                $document->addLangvar($this->lang->{$status});
                return;
            }

            $document->setTitle($this->lang->moderate_title);
            $document->addLangvar($this->lang->review);
            $document->add(new Comment("<br>Type: {$tradeOffer->getType()}"));
            $document->add(new Comment("<br>Sender: {$tradeOffer->getSenderName()}"));
            $document->add(new Comment("<br>Recipient: {$tradeOffer->getRecipientName()}"));

            $tradeHelper = new TradeFormHelper($this->lang);
            $document->add(new Comment("<br>AdoptOffered: "));
            $document->add($tradeHelper->getAdoptImages($tradeOffer->getAdoptOffered(), false));
            $document->add(new Comment("<br>AdoptWanted: "));
            $document->add(($tradeOffer->getType() == "public") ? $tradeHelper->getAdoptList($tradeOffer->getAdoptWanted()) : $tradeHelper->getAdoptImages($tradeOffer->getAdoptWanted(), false));
            $document->add(new Comment("<br>ItemOffered: "));
            $document->add($tradeHelper->getItemImages($tradeOffer->getItemOffered(), false));
            $document->add(new Comment("<br>ItemWanted: "));
            $document->add(($tradeOffer->getType() == "public") ? $tradeHelper->getItemList($tradeOffer->getItemWanted()) : $tradeHelper->getItemImages($tradeOffer->getItemWanted(), false));
            $document->add(new Comment("<br>CashOffered: {$tradeOffer->getCashOffered()} {$mysidia->settings->cost}"));
            $document->add(new Comment("<br>Message: "));
            $document->add(new Paragraph(new Comment($tradeOffer->getMessage(), true, "i"), "message"));

            $statusTypes = new LinkedHashMap();
            $statusTypes->put(new MysString("Approve"), new MysString("pending"));
            $statusTypes->put(new MysString("Disapprove"), new MysString("canceled"));
            $tradeForm = new FormBuilder("moderateform", $tradeOffer->getID(), "post");
            $tradeForm->add(new Comment("<br>You can now approve or disapprove this trade offer: "));
            $tradeForm->buildRadioList("status", $statusTypes, "pending");
            $tradeForm->add(new Button("Moderate Trade Offer", "submit", "submit"));
            $document->add($tradeForm);
            return;
        }

        $document->setTitle($this->lang->moderate_title);
        $document->addLangvar($this->lang->moderate);
        $helper = new TableHelper();
        $tradeTable = new TableBuilder("item");
        $tradeTable->setAlign(new Align("center", "middle"));
        $tradeTable->buildHeaders("ID", "Type", "Sender", "Recipient", "Status", "Moderate");
        $tradeTable->setHelper($helper);

        $tradeOffers = $this->getField("tradeOffers");
        $iterator = $tradeOffers->iterator();
        while ($iterator->hasNext()) {
            $tradeOffer = $iterator->next();
            $cells = new LinkedList();
            $cells->add(new TCell($tradeOffer->getID()));
            $cells->add(new TCell($tradeOffer->getType()));
            $cells->add(new TCell($helper->getText($tradeOffer->getSenderName())));
            $cells->add(new TCell($helper->getText($tradeOffer->getRecipientName())));
            $cells->add(new TCell($tradeOffer->getStatus()));
            $cells->add(new TCell($helper->getModerateLink($tradeOffer->getID())));
            $tradeTable->buildRow($cells);
        }
        $document->add($tradeTable);
    }

    public function settings()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->settings_changed_title);
            $document->addLangvar($this->lang->settings_changed);
            return;
        }

        $tradeSettings = $this->getField("tradeSettings");
        $document->setTitle($this->lang->settings_title);
        $document->addLangvar($this->lang->settings);
        $settingsForm = new FormBuilder("settingsform", "settings", "post");
        $enabled = new LinkedHashMap();
        $enabled->put(new MysString("Enabled"), new MysString("enabled"));
        $enabled->put(new MysString("Disabled"), new MysString("disabled"));
        $settingsForm->buildComment("Trade System Enabled:   ", false)->buildRadioList("system", $enabled, $tradeSettings->system)
                     ->buildComment("Multiple Adopts/Items Enabled:   ", false)->buildRadioList("multiple", $enabled, $tradeSettings->multiple)
                     ->buildComment("Partial Trade Enabled:   ", false)->buildRadioList("partial", $enabled, $tradeSettings->partial)
                     ->buildComment("Public Trade Enabled:   ", false)->buildRadioList("public", $enabled, $tradeSettings->public)
                     ->buildComment("Ineligible Species(separate by comma):   ", false)->buildTextField("species", $tradeSettings->species ? implode(",", $tradeSettings->species) : "")
                     ->buildComment("Interval/wait-time(days) between successive trade offers:	 ", false)->buildTextField("interval", $tradeSettings->interval)
                     ->buildComment("Maximum Number of adoptables/items allowed:   ", false)->buildTextField("number", $tradeSettings->number)
                     ->buildComment("Number of days till Trade is still valid(or expiring):   ", false)->buildTextField("duration", $tradeSettings->duration)
                     ->buildComment("Tax for each Trade Offer:	 ", false)->buildTextField("tax", $tradeSettings->tax)
                     ->buildComment("Usergroup(s) permitted to trade(separate by comma):	", false)->buildTextField("usergroup", ($tradeSettings->usergroup == "all") ? $tradeSettings->usergroup : implode(",", $tradeSettings->usergroup))
                     ->buildComment("Ineligible/non-tradable Item(s)(separate by comma):	", false)->buildTextField("item", ($tradeSettings->item) ? implode(",", $tradeSettings->item) : "")
                     ->buildComment("Moderation Required:   ", false)->buildRadioList("moderate", $enabled, $tradeSettings->moderate)
                     ->buildButton("Change Trade Settings", "submit", "submit");
        $document->add($settingsForm);
    }
}
