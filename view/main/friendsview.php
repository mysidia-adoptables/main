<?php

namespace View\Main;

use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\RadioButton;
use Resource\GUI\Container\Form;
use Resource\GUI\Container\RadioList;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Service\Builder\TableBuilder;
use Service\Helper\FriendTableHelper;

class FriendsView extends View
{
    public function request()
    {
        $document = $this->document;
        $recipient = $this->getField("recipient");

        $document->setTitle($this->lang->request_title);
        $document->addLangvar($this->lang->request);
        $document->addLangvar("<br>Congrats! You have successfully sent a friendrequest to {$recipient->getUsername()}, you may now wait for his/her response.");
    }

    public function option()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;

        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->option_title);
            $document->addLangvar($this->lang->option);
            return;
        }
        $document->setTitle($this->lang->privacy_title);
        $document->addLangvar($this->lang->privacy);
        $options = $this->getField("options");
        $optionForm = new Form("optionform", "option", "post");

        $pmoption = new RadioList("pm");
        $pmoption->add(new RadioButton("public", "pm", 0));
        $pmoption->add(new RadioButton("friend-only", "pm", 1));
        $pmoption->check((int)$options->getPMStatus());

        $vmoption = new RadioList("vm");
        $vmoption->add(new RadioButton("public", "vm", 0));
        $vmoption->add(new RadioButton("friend-only", "vm", 1));
        $vmoption->check((int)$options->getVMStatus());

        $tradeoption = new RadioList("trade");
        $tradeoption->add(new RadioButton("public", "trade", 0));
        $tradeoption->add(new RadioButton("friend-only", "trade", 1));
        $tradeoption->check((int)$options->getTradeStatus());

        $optionForm->add(new Comment("PM status: "));
        $optionForm->add($pmoption);
        $optionForm->add(new Comment("VM status: "));
        $optionForm->add($vmoption);
        $optionForm->add(new Comment("Trade status: "));
        $optionForm->add($tradeoption);

        $optionForm->add(new Comment(""));
        $optionForm->add(new Button("Update Friend-Options", "submit", "submit"));
        $document->add($optionForm);
    }

    public function edit()
    {
        $document = $this->document;
        $sender = $this->getField("sender");
        $confirm = (string)$this->getField("confirm");

        switch ($confirm) {
            case "accept":
                $document->setTitle("Friend Request Accepted");
                $document->addLangvar("Congrats, you and {$sender->getUsername()} are friends now. You may view your friendlist to see this change.");
                break;
            case "decline":
                $document->setTitle("Friend Request Declined");
                $document->addLangvar("You have rejected this friend request from {$sender->getUsername()}.");
                break;
            default:
                $requests = $this->getField("requests");
                $document->setTitle($this->lang->friend_request);
                $helper = new FriendTableHelper();
                $requestTable = new TableBuilder("friendrequest");
                $requestTable->setAlign(new Align("center", "middle"));
                $requestTable->buildHeaders("From User", "Status", "Message", "Accept", "Decline");
                $requestTable->setHelper($helper);

                $requestIterator = $requests->iterator();
                while ($requestIterator->hasNext()) {
                    $request = $requestIterator->next();
                    $cells = new LinkedList();
                    $cells->add(new TCell($helper->getProfileLink($request->getSenderID(), $request->getSenderName())));
                    $cells->add(new TCell($request->getStatus()));
                    $cells->add(new TCell($request->getContent()));
                    $cells->add(new TCell($helper->getAcceptLink($request->getID())));
                    $cells->add(new TCell($helper->getDeclineLink($request->getID())));
                    $requestTable->buildRow($cells);
                }
                $document->add($requestTable);
        }
    }


    public function delete()
    {
        $document = $this->document;
        $document->setTitle($this->lang->remove_title);
        $document->addLangvar($this->lang->remove);
    }
}
