<?php

namespace View\Main;

use Resource\Core\View;
use Resource\GUI\Component\Link;

class OnlineView extends View
{
    public function index()
    {
        $document = $this->document;
        $document->setTitle($this->lang->title);
        $document->addLangvar($this->lang->default);

        $total = $this->getField("total");
        $members = $this->getField("members");
        $membersIterator = $members->iterator();
        while ($membersIterator->hasNext()) {
            $member = $membersIterator->next();
            $onlineLink = new Link("profile/view/{$member->getID()}");
            $onlineLink->setClass("onlinelist");
            $onlineInfo = "<span class='onlinelistt'>{$member->getUsername()}</span>
						   <span class='onlinelistn'>{$member->getProfile()->getNickname()}</span>
						   <span class='onlinelistj'>{$member->countOwnedAdopts()}</span>
						   <span class='onlinelistp'>{$member->getMoney()}</span>
						   <span class='onlinelistg'>{$member->getProfile()->getGender()}</span>";
            $onlineLink->setText($onlineInfo);
            $onlineLink->setLineBreak(true);
            $document->add($onlineLink);
        }
        $document->addLangvar($this->lang->visitors . $total->getValue());
        $this->refresh(30);
    }
}
