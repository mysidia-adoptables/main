<?php

namespace Model\ViewModel;

use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\ViewModel;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Division;

class OwnedAdoptableViewModel extends ViewModel
{

    public function getType()
    {
        return $this->type;
    }

    public function getTypeAndName()
    {
        return "<em>{$this->model->getName()}</em> the {$this->model->getType()}";
    }

    public function getName()
    {
        return $this->model->getName();
    }

    public function getAdoptImage()
    {
        return $this->model->getImage(Model::GUI);
    }

    public function getGenderImage()
    {
        return $this->model->getGender(Model::GUI);
    }

    public function getTotalClicks()
    {
        return $this->model->getTotalClicks();
    }

    public function getCurrentLevel()
    {
        return $this->model->getCurrentLevel();
    }

    public function getManageLink()
    {
        return new Link("myadopts/manage/{$this->getID()}", $this->model->getImage(Model::GUI));
    }

    public function getStats()
    {
        $mysidia = Registry::get("mysidia");
        $stats = new Division("adoptstats");
        $stats->add(new Comment("<br><br><b>Total Clicks: {$this->model->getTotalClicks()}"));
        $stats->add(new Comment("Gender: ", false));
        $stats->add($this->getGenderImage());

        if ($this->model->hasNextLevel()) {
            $level = $this->model->getNextLevel();
            $levelupClicks = $this->model->getLevelupClicks();
            $nextLevel = $level->getLevel() . $mysidia->lang->clicks . $levelupClicks;
        } else $nextLevel = $mysidia->lang->maximum;

        $adoptStats = "<br>Trade Status: {$this->model->getTradeStatus()}<br>
				       Current Level: {$this->model->getCurrentLevel()}<br>Next Level: {$nextLevel}</b>";
        $stats->add(new Comment($adoptStats));
        return $stats;
    }
}
