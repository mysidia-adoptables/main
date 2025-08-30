<?php

namespace Model\ViewModel;

use Resource\Collection\LinkedList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\ViewModel;
use Resource\Exception\InvalidIDException;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;

class VisitorMessageViewModel extends ViewModel
{
    public function view()
    {
        $mysidia = Registry::get("mysidia");
        if ($this->getID() == 0) {
            throw new InvalidIDException("view_none");
        } else {
            $dateSent = $this->model->getDateSent("Y-m-d");
            $sender = $this->model->getSender(Model::MODEL);

            $avatar = new TCell(new Image($sender->getProfile()->getAvatar()));
            $message = new TCell(new Link("profile/view/{$sender->getID()}", $sender->getUsername()));
            $message->add(new Comment("(at {$dateSent})"));
            $message->add(new Comment($this->model->getContent()));
            $cells = new LinkedList();
            $cells->add($avatar);
            $cells->add($message);

            if ($mysidia->user->isAdmin() || ($this->model->isSender($mysidia->user->getID()))) {
                $action = new TCell(new Link("vmessage/edit/{$this->getID()}", new Image("templates/icons/cog.gif"), true));
                $action->add(new Link("vmessage/delete/{$this->getID()}", new Image("templates/icons/delete.gif"), true));
                $cells->add($action);
            }
            return $cells;
        }
    }
}
