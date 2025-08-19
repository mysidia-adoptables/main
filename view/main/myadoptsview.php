<?php

namespace View\Main;

use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Component\TextArea;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\UserTableHelper;

class MyadoptsView extends View
{
    public function index()
    {
        $document = $this->document;
        $document->setTitle($this->lang->title);

        $pagination = $this->getField("pagination");
        $ownedAdopts = $this->getField("ownedAdopts");

        $adoptTable = new TableBuilder("adopttable", 650);
        $adoptTable->setAlign(new Align("center", "middle"));
        $adoptTable->buildHeaders("Gender", "Name/Type", "Image", "Level", "Clicks");
        $ownedAdoptsIterator = $ownedAdopts->iterator();
        while ($ownedAdoptsIterator->hasNext()) {
            $ownedAdopt = $ownedAdoptsIterator->next();
            $cells = new LinkedList();
            $cells->add(new TCell($ownedAdopt->getGenderImage()));
            $cells->add(new TCell($ownedAdopt->getTypeAndName()));
            $cells->add(new TCell($ownedAdopt->getManageLink()));
            $cells->add(new TCell($ownedAdopt->getCurrentLevel()));
            $cells->add(new TCell($ownedAdopt->getTotalClicks()));
            $adoptTable->buildRow($cells);
        }
        $document->add($adoptTable);
        $document->addLangvar($pagination->showPage());
    }

    public function manage()
    {
        $document = $this->document;
        $ownedAdopt = $this->getField("ownedAdopt");
        $image = $this->getField("image");
        $document->setTitle("Managing {$ownedAdopt->getName()}");
        $document->add($image);
        $document->add(new Comment("<br><br>This page allows you to manage {$ownedAdopt->getName()}.  Click on an option below to change settings.<br>"));

        $document->add(new Image("templates/icons/add.gif"));
        $document->add(new Link("levelup/click/{$ownedAdopt->getID()}", " Level Up {$ownedAdopt->getName()}", true));
        $document->add(new Image("templates/icons/stats.gif"));
        $document->add(new Link("myadopts/stats/{$ownedAdopt->getID()}", " Get Stats for {$ownedAdopt->getName()}", true));
        $document->add(new Image("templates/icons/bbcodes.gif"));
        $document->add(new Link("myadopts/bbcode/{$ownedAdopt->getID()}", " Get BBCodes / HTML Codes for {$ownedAdopt->getName()}", true));
        $document->add(new Image("templates/icons/title.gif"));
        $document->add(new Link("myadopts/rename/{$ownedAdopt->getID()}", " Rename {$ownedAdopt->getName()}", true));
        $document->add(new Image("templates/icons/trade.gif"));
        $document->add(new Link("myadopts/trade/{$ownedAdopt->getID()}", " Change Trade status for {$ownedAdopt->getName()}", true));
        $document->add(new Image("templates/icons/freeze.gif"));
        $document->add(new Link("myadopts/freeze/{$ownedAdopt->getID()}", " Freeze or Unfreeze {$ownedAdopt->getName()}", true));
        $document->add(new Image("templates/icons/delete.gif"));
        $document->add(new Link("pound/pound/{$ownedAdopt->getID()}", " Pound {$ownedAdopt->getName()}", true));
    }

    public function stats()
    {
        $ownedAdopt = $this->getField("ownedAdopt");
        $votes = $this->getField("votes");

        $document = $this->document;
        $document->setTitle($ownedAdopt->getName() . $this->lang->stats);
        $document->add($ownedAdopt->getAdoptImage());
        $document->add($ownedAdopt->getStats());
        $document->addLangvar("<h2>{$ownedAdopt->getName()}'s Voters:</h2><br>{$this->lang->voters}<br><br>");

        $helper = new UserTableHelper();
        $voterTable = new TableBuilder("voters", 500);
        $voterTable->setAlign(new Align("center"));
        $voterTable->buildHeaders("User", "Date Voted", "Profile", "PM");
        $voterTable->setHelper($helper);
        $votesIterator = $votes->iterator();
        while ($votesIterator->hasNext()) {
            $vote = $votesIterator->next();
            $cells = new LinkedList();
            $cells->add(new TCell($helper->getUsername($vote->getUsername())));
            $cells->add(new TCell($vote->getDate("Y-m-d")));
            $cells->add(new TCell($helper->getProfileImage($vote->getUserID())));
            $cells->add(new TCell($helper->getPMImage($vote->getUserID())));
            $voterTable->buildRow($cells);
        }
        $document->add($voterTable);
    }

    public function bbcode()
    {
        $mysidia = Registry::get("mysidia");
        $adopt = $this->getField("adopt");
        $document = $this->document;
        $document->setTitle($this->lang->bbcode . $adopt->getName());
        $document->addLangvar($this->lang->bbcode_info);
        $document->add(new Comment("<br>"));

        $forumComment = new Comment("Forum BBCode: ");
        $forumComment->setUnderlined();
        $forumcode = "[url={$mysidia->path->getAbsolute()}levelup/click/{$adopt->getAdoptID()}][img]{$mysidia->path->getAbsolute()}levelup/siggy/{$adopt->getAdoptID()}[/img][/url]";
        $forumArea = new TextArea("forumcode", $forumcode, 4, 50);
        $forumArea->setReadOnly(true);

        $altComment = new Comment("Alternative BBCode: ");
        $altComment->setUnderlined();
        $altcode = "[url={$mysidia->path->getAbsolute()}levelup/click/{$adopt->getAdoptID()}][img]{$mysidia->path->getAbsolute()}get/{$adopt->getAdoptID()}.gif\"[/img][/url]";
        $altArea = new TextArea("altcode", $altcode, 4, 50);
        $altArea->setReadOnly(true);

        $htmlComment = new Comment("HTML BBCode: ");
        $htmlComment->setUnderlined();
        $htmlcode = "<a href='{$mysidia->path->getAbsolute()}levelup/click/{$adopt->getAdoptID()}' target='_blank'>
	                 <img src='{$mysidia->path->getAbsolute()}levelup/siggy/{$adopt->getAdoptID()}' border=0></a>";
        $htmlArea = new TextArea("htmlcode", $htmlcode, 4, 50);
        $htmlArea->setReadOnly(true);

        $document->add($forumComment);
        $document->add($forumArea);
        $document->add($altComment);
        $document->add(($mysidia->settings->usealtbbcode == "yes") ? $altArea : new Comment("The Admin has disabled Alt BBCode for this site."));
        $document->add($htmlComment);
        $document->add($htmlArea);
    }

    public function rename()
    {
        $mysidia = Registry::get("mysidia");
        $adopt = $this->getField("adopt");
        $image = $this->getField("image");
        $document = $this->document;

        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->rename_success_title);
            $document->add($image);
            $message = "<br>{$this->lang->rename_success}{$adopt->getName()}. 
					    You can now manage {$adopt->getName()} on the";
            $document->addLangvar($message);
            $document->add(new Link("myadopts/manage/{$adopt->getAdoptID()}", "My Adopts Page"));
            return;
        }

        $document->setTitle($this->lang->rename . $adopt->getName());
        $document->add($image);
        $document->addLangvar("<br />{$this->lang->rename_default}{$adopt->getName()}{$this->lang->rename_details}<br />");

        $renameForm = new FormBuilder("renameform", "", "post");
        $renameForm->buildTextField("adoptname")->buildButton("Rename Adopt", "submit", "submit");
        $document->add($renameForm);
    }

    public function trade()
    {
        $adopt = $this->getField("adopt");
        $image = $this->getField("image");
        $confirm = $this->getField("confirm");
        $document = $this->document;
        $document->setTitle($this->lang->trade . $adopt->getName());
        $document->add($image);

        if ($confirm) {
            $document->addLangvar(($adopt->getTradeStatus() == "fortrade") ? $this->lang->trade_allow : $this->lang->trade_disallow);
        } else {
            $document->addLangvar("Are you sure you wish to change the trade status of this adoptable?
					               <center><b><a href='{$adopt->getAdoptID()}/confirm'>Yes I'd like to change its trade status</a></b><br /><br />
					               <b><a href='../../myadopts'>Nope I change my mind! Go back to the previous page.</a></b></center><br />");
        }
    }

    public function freeze()
    {
        $adopt = $this->getField("adopt");
        $image = $this->getField("image");
        $confirm = $this->getField("confirm");
        $document = $this->document;
        $document->setTitle($this->lang->freeze . $adopt->getName());
        $document->add($image);

        if ($confirm) {
            $document->addLangvar(($adopt->isFrozen() == "yes") ? $this->lang->freeze_success : $this->lang->freeze_reverse);
            $document->addLangvar("<br>You may now manage {$adopt->getName()} on the ");
            $document->add(new Link("myadopts/manage/{$adopt->getAdoptID()}", "My Adopts Page"));
        } else {
            $document->add(new Comment("<br /><b>{$adopt->getName()}'s Current Status: "));

            if ($adopt->isfrozen() == "yes") {
                $document->add(new Image("templates/icons/freeze.gif", "Frozen"));
                $document->add(new Comment("Frozen<br<br>"));
                $document->add(new Comment($this->lang->freeze));
                $document->add(new Image("templates/icons/unfreeze.gif", "Unfreeze"));
                $document->add(new Link("myadopts/freeze/{$adopt->getAdoptID()}/confirm", "Unfreeze this Adoptable", true));
            } else {
                $document->add(new Image("templates/icons/unfreeze.gif", "Not Frozen"));
                $document->add(new Comment("Not Frozen<br><br>"));
                $document->add(new Comment($this->lang->freeze));
                $document->add(new Image("templates/icons/freeze.gif", "Greeze"));
                $document->add(new Link("myadopts/freeze/{$adopt->getAdoptID()}/confirm", "Freeze this Adoptable", true));
            }
            $document->add(new Comment("<br><br>"));
            $document->add(new Image("templates/icons/warning.gif"));
            $document->addLangvar($this->lang->freeze_warning);
        }
    }
}
