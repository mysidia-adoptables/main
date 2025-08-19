<?php

namespace View\Main;

use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\CheckBox;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Component\PasswordField;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\Form;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Service\Builder\CKEditorBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\MessageTableHelper;

class MessagesView extends View
{

    public function index()
    {
        $document = $this->document;
        $document->setTitle($this->lang->access_title);
        $document->addLangvar($this->lang->access, true);

        $document->add(new Image("templates/icons/fans.gif"));
        $document->add(new Link("messages/newpm", "Send a new PM", true));
        $document->add(new Image("templates/icons/fans.gif"));
        $document->add(new Link("messages/draft", "Work on an old draft", true));
        $document->add(new Image("templates/icons/fans.gif"));
        $document->add(new Link("messages/outbox", "Visit my Outbox", true));

        $pagination = $this->getField("pagination");
        $messages = $this->getField("messages");
        if (!$messages) {
            $document->addLangvar($this->lang->read_empty);
            return;
        }
        $pmTable = new TableBuilder("pmtable", 650);
        $pmTable->setAlign(new Align("center"));
        $pmTable->buildHeaders("Message Title", "From User", "Date Received", "Status", "ReadPM", "Delete");
        $helper = new MessageTableHelper;
        $pmTable->setHelper($helper);

        $messageIterator = $messages->iterator();
        while ($messageIterator->hasNext()) {
            $message = $messageIterator->next();
            $cells = new LinkedList;
            $cells->add(new TCell($message->getTitle()));
            $cells->add(new TCell($helper->getProfile($message->getSenderName())));
            $cells->add(new TCell($message->getDateSent("Y-m-d")));
            $cells->add(new TCell($helper->getStatus($message->getStatus())));
            $cells->add(new TCell($helper->getReadLink($message->getID())));
            $cells->add(new TCell($helper->getDeleteLink($message->getID())));
            $pmTable->buildRow($cells);
        }
        $document->add($pmTable);
        $document->addLangvar($pagination->showPage());
    }

    public function read()
    {
        $message = $this->getField("message");
        $document = $this->document;
        $document->setTitle($this->lang->read_title . $message->getSenderName());
        $document->add($message->getMessageBody());
    }

    public function newpm()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $draftStatus = (string)$this->getField("draftStatus");
            $document->setTitle($this->lang->sent_title);
            if ($draftStatus == "draftnew") $document->addLangvar($this->lang->draft_sent);
            elseif ($draftStatus == "draftedit") $document->addLangvar($this->lang->draft_edited);
            else $document->addLangvar($this->lang->sent);
            return;
        }

        $document->setTitle($this->lang->send_title);
        $document->addLangvar($this->lang->send);
        $recipient = $this->getField("recipient");
        $editor = new CKEditorBuilder;
        $pmForm = new Form("pmform", "", "post");
        $pmForm->add(new Comment("Message Recipient: ", false));
        $pmForm->add(new TextField("recipient", $recipient ? $recipient->getUsername() : "", 50));
        $pmForm->add(new Comment("Message Title: ", false));
        $pmForm->add(new TextField("mtitle", "", 25));
        $pmForm->add(new Comment("Message Text: ", false));
        $pmForm->add($editor->buildEditor("mtext", "Enter your message here."));
        $pmForm->add(new CheckBox("Send a Copy to Outbox", "outbox", "yes"));
        $pmForm->add(new CheckBox("Save as Draft", "draft", "yes"));
        $pmForm->add(new Button("Send PM", "submit", "submit"));
        $document->add($pmForm);
    }

    public function delete()
    {
        $document = $this->document;
        $document->setTitle($this->lang->delete_title);
        $document->addLangvar($this->lang->delete);
    }

    public function outbox()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $document->setTitle($mysidia->user->getUsername() . $this->lang->outbox_title);
        $document->addLangvar($this->lang->outbox);

        $pagination = $this->getField("pagination");
        $messages = $this->getField("messages");
        if (!$messages) {
            $document->addLangvar($this->lang->outbox_empty);
            return;
        }
        $pmTable = new TableBuilder("pmtable", 650);
        $pmTable->setAlign(new Align("center"));
        $pmTable->buildHeaders("Message Title", "To User", "Date Sent", "ReadPM", "Delete");
        $helper = new MessageTableHelper;
        $pmTable->setHelper($helper);

        $messageIterator = $messages->iterator();
        while ($messageIterator->hasNext()) {
            $message = $messageIterator->next();
            $cells = new LinkedList;
            $cells->add(new TCell($message->getTitle()));
            $cells->add(new TCell($helper->getProfile($message->getRecipientName())));
            $cells->add(new TCell($message->getDateSent("Y-m-d")));
            $cells->add(new TCell($helper->getOutboxReadLink($message->getID())));
            $cells->add(new TCell($helper->getOutboxDeleteLink($message->getID())));
            $pmTable->buildRow($cells);
        }
        $document->add($pmTable);
        $document->addLangvar($pagination->showPage());
    }

    public function outboxread()
    {
        $this->read();
    }

    public function outboxdelete()
    {
        $this->delete();
    }

    public function draft()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $document->setTitle($mysidia->user->getUsername() . $this->lang->draft_title);
        $document->addLangvar($this->lang->draft);

        $pagination = $this->getField("pagination");
        $messages = $this->getField("messages");
        if (!$messages) {
            $document->addLangvar($this->lang->draft_empty);
            return;
        }
        $pmTable = new TableBuilder("pmtable", 650);
        $pmTable->setAlign(new Align("center"));
        $pmTable->buildHeaders("Message Title", "To User", "Date Created", "Edit", "Delete");
        $helper = new MessageTableHelper;
        $pmTable->setHelper($helper);

        $messageIterator = $messages->iterator();
        while ($messageIterator->hasNext()) {
            $message = $messageIterator->next();
            $cells = new LinkedList;
            $cells->add(new TCell($message->getTitle()));
            $cells->add(new TCell($helper->getProfile($message->getRecipientName())));
            $cells->add(new TCell($message->getDateSent("Y-m-d")));
            $cells->add(new TCell($helper->getDraftEditLink($message->getID())));
            $cells->add(new TCell($helper->getDraftDeleteLink($message->getID())));
            $pmTable->buildRow($cells);
        }
        $document->add($pmTable);
        $document->addLangvar($pagination->showPage());
    }

    public function draftedit()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $document->setTitle($this->lang->draft_edit_title . $mysidia->user->getUsername());
        $document->addLangvar($this->lang->draft_edit);
        $message = $this->getField("message");

        $editor = new CKEditorBuilder;
        $draftForm = new Form("pmform", "../newpm", "post");
        $draftForm->add(new Comment("Message Recipient: ", false));
        $draftForm->add(new TextField("recipient", $message->getRecipientName(), 50));
        $draftForm->add(new Comment("Message Title: ", false));
        $draftForm->add(new TextField("mtitle", $message->getTitle(), 25));
        $draftForm->add(new Comment("Message Text: ", false));
        $draftForm->add($editor->buildEditor("mtext", $message->formatContent()));
        $draftForm->add(new CheckBox("Send a Copy to Outbox", "outbox", "yes"));
        $draftForm->add(new CheckBox("Save as Draft", "draft", "yes"));
        $draftForm->add(new PasswordField("hidden", "draftid", $message->getID()));
        $draftForm->add(new Button("Send PM", "submit", "submit"));
        $document->add($draftForm);
    }

    public function draftdelete()
    {
        $this->delete();
    }

    public function report()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->reported_title);
            $document->addLangvar($this->lang->reported);
            return;
        }
        $message = $this->getField("message");
        $admin = $this->getField("admin");

        $reportForm = new Form("reportform", "", "post");
        $reportForm->add(new Comment("Report Message to: ", false));
        $reportForm->add(new TextField("recipient", $admin->getUsername()));
        $reportForm->add(new Comment("Reasons for Reporting this Message: ", false));
        $reportForm->add(new TextField("reason", "Spam", 50));
        $reportForm->add(new PasswordField("hidden", "mtitle", $message->getTitle()));
        $reportForm->add(new PasswordField("hidden", "mtext", $message->format($message->getContent())));
        $reportForm->add(new Button("Report", "submit", "submit"));

        $document->setTitle($this->lang->report_title);
        $document->addLangvar($this->lang->report);
        $document->add($reportForm);
    }
}
