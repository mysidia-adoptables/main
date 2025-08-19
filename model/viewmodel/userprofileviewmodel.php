<?php

namespace Model\ViewModel;

use Model\DomainModel\UserContact;
use Model\DomainModel\VisitorMessage;
use Resource\Collection\LinkedList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\ViewModel;
use Resource\Exception\InvalidActionException;

;

use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Division;
use Resource\GUI\Element\Align;
use Service\Builder\TableBuilder;
use Service\Helper\FriendTableHelper;
use Service\Helper\MessageTableHelper;

class UserProfileViewModel extends ViewModel
{

    public function getAvatarImage()
    {
        return $this->model->getAvatar(Model::GUI);
    }

    public function getBio()
    {
        return stripslashes((string) $this->model->getBio());
    }

    public function getFavpet()
    {
        $favpet = $this->model->getFavpet();
        return $favpet ? new Link("levelup/click/{$favpet}", new Image("levelup/siggy/{$favpet}"), true)
            : new Comment("None Selected");
    }

    public function display($action = "", $data = null)
    {
        if ($action == "contactinfo") return $this->displayContactinfo($data);
        else {
            $validActions = ["vmessages", "aboutme", "adopts", "friends"];
            if (!in_array($action, $validActions)) throw new InvalidActionException("Invalid profile tab action specified.");
            $method = "display" . ucfirst((string) $action);
            return $this->$method();
        }
    }

    protected function displayVmessages()
    {
        $mysidia = Registry::get("mysidia");
        $uid = $this->model->getID();
        $stmt = $mysidia->db->select("visitor_messages", [], "touser = '{$uid}' ORDER BY vid DESC LIMIT 0, 15");
        if ($stmt->rowCount() == 0) {
            return new Comment;
        }
        $helper = new MessageTableHelper;
        $vmList = new TableBuilder("vmessages", 800, false);
        $vmList->setHelper($helper);
        while ($dto = $stmt->fetchObject()) {
            $vmessage = new VisitorMessage($dto->vid, $dto);
            $senderProfile = $vmessage->getSenderProfile();
            $cells = new LinkedList;
            $cells->add(new TCell($helper->getAvatarImage($senderProfile->getAvatar())));
            $cells->add(new TCell($helper->getVisitorMessage($vmessage)));
            if ($mysidia->user->isAdmin() || $vmessage->isSender($mysidia->user)) {
                $cells->add(new TCell($helper->getManageActions($vmessage->getID())));
            }
            $vmList->buildRow($cells);
        }
        return $vmList;
    }

    protected function displayAboutme()
    {
        $mysidia = Registry::get("mysidia");
        $division = new Division;
        $title = new Comment($mysidia->lang->basic . $this->model->getUser()->getUsername());
        $title->setBold();
        $title->setUnderlined();
        $basicinfo = "<br><strong>Member Since:</strong> {$this->model->getUser()->getMemberSince('Y-m-d')}<br>
				    Gender: {$this->getModel()->getGender()}<br>
				    Favorite Color: {$this->model->getColor()}<br>
				    Nickname: {$this->model->getNickname()}<br>
				    Bio: {$this->getBio()}";

        $division->add($title);
        $division->add(new Image($this->model->getAvatar(), "avatar", 100));
        $division->add(new Comment($basicinfo));
        return $division;
    }

    protected function displayAdopts()
    {
        $division = new Division;
        $spotlight = new Comment(".:AdoptSpotlight:.");
        $spotlight->setHeading(2);

        $division->add($spotlight);
        $division->add($this->getFavpet());
        $division->add(new Comment($this->model->getAbout()));

        $user = $this->model->getUser();
        $title = new Comment("{$user->getUsername()}'s Pets:");
        $title->setBold();
        $title->setUnderlined();
        $division->add($title);

        $adopts = $user->getOwnedAdopts();
        foreach ($adopts as $adopt) {
            $division->add(new Link("levelup/click/{$adopt->getAdoptID()}", $adopt->getImage(Model::GUI)));
        }
        return $division;
    }

    protected function displayFriends()
    {
        $user = $this->model->getUser();
        $friendsList = $user->getFriendsList(Model::MODEL);
        $numFriends = $user->countFriends();
        $division = new Division;
        $division->add(new Comment("{$user->getUsername()} currently have {$numFriends} friends."));
        if ($numFriends == 0) return $division;

        $friendTable = new TableBuilder("friends", "", false);
        $friendTable->setHelper(new FriendTableHelper);
        foreach ($friendsList as $friend) {
            $avatar = new TCell(new Image($friend->getProfile()->getAvatar(60)));
            $avatar->setAlign(new Align("left"));
            $info = new TCell($friendTable->getHelper()->getFriendInfo($friend));
            $cells = new LinkedList;
            $cells->add($avatar);
            $cells->add($info);

            if ($user->isCurrentUser()) {
                $action = new TCell;
                $action->setAlign(new Align("right"));
                $action->add(new Comment("<br><br><br><br>"));
                $action->add(new Link("friends/delete/{$friend->getID()}", "Break Friendship"));
                $cells->add($action);
            }
            $friendTable->buildRow($cells);
        }
        $division->add($friendTable);
        return $division;
    }

    protected function displayContactinfo(UserContact $contact)
    {
        $uid = $this->model->getID();
        $username = $this->model->getUser()->getUsername();
        $contactInfo = new Division;
        $contactInfo->add(new Image("templates/icons/web.gif", "web"));
        $contactInfo->add(new Comment($contact->getWebsite() ?: "No Website Information Given"));
        $contactInfo->add(new Image("templates/icons/facebook.gif", "facebook"));
        $contactInfo->add(new Comment($contact->getFacebook() ?: "No Facebook Information Given"));
        $contactInfo->add(new Image("templates/icons/twitter.gif", "twitter"));
        $contactInfo->add(new Comment($contact->getTwitter() ?: "No Twitter Information Given"));
        $contactInfo->add(new Image("templates/icons/aim.gif", "aim"));
        $contactInfo->add(new Comment($contact->getAIM() ?: "No AIM Information Given"));
        $contactInfo->add(new Image("templates/icons/msn.gif", "msn"));
        $contactInfo->add(new Comment($contact->getMSN() ?: "No MSN Information Given"));
        $contactInfo->add(new Image("templates/icons/yahoo.gif", "yahoo"));
        $contactInfo->add(new Comment($contact->getYahoo() ?: "No YIM Information Given"));
        $contactInfo->add(new Image("templates/icons/skype.gif", "skype"));
        $contactInfo->add(new Comment($contact->getSkype() ?: "No Skype Information Given"));
        $contactInfo->add(new Image("templates/icons/title.gif", "Write a PM"));
        $contactInfo->add(new Link("messages/newpm/{$uid}", "Send {$username} a Private Message", true));
        $contactInfo->add(new Image("templates/icons/fr.gif", "Send a Friend Request"));
        $contactInfo->add(new Link("friends/request/{$uid}", "Send {$username} a Friend Request", true));
        $contactInfo->add(new Image("templates/icons/trade.gif", "Make a Trade Offer"));
        $contactInfo->add(new Link("trade/offer/user/{$uid}", "Make {$username} a Trade Offer"));
        return $contactInfo;
    }
}
