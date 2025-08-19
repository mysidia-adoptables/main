<?php

namespace Controller\Main;

use Model\DomainModel\Member;
use Model\DomainModel\MessageNotfoundException;
use Model\DomainModel\PrivateMessage;
use Model\ViewModel\PrivateMessageViewModel;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\NoPermissionException;
use Resource\Native\MysString;
use Service\ApplicationService\MessageService;

class MessagesController extends AppController
{
    private $message;
    private $messageService;

    public function __construct()
    {
        parent::__construct("member");
        $mysidia = Registry::get("mysidia");
        if ($mysidia->systems->messages != "enabled") {
            throw new NoPermissionException("The admin has turned off private message feature for this site, please contact him/her for detailed information.");
        }
        if (!$mysidia->user->hasPermission("canpm")) {
            throw new NoPermissionException("banned");
        }
        $this->messageService = new MessageService();
    }

    public function index()
    {
        $mysidia = Registry::get("mysidia");
        try {
            $total = $mysidia->user->countMessages();
            $pagination = new Pagination($total, 10, "messages", $mysidia->input->get("page"));
            $messages = $this->messageService->getMessages("inbox", $pagination->getLimit(), $pagination->getRowsperPage());
            $this->setField("pagination", $pagination);
            $this->setField("messages", $messages);
        } catch (MessageNotfoundException) {
            $this->setFlagss("nonexist_title", "nonexist");
        }
    }

    public function read($mid)
    {
        $mysidia = Registry::get("mysidia");
        try {
            $this->message = new PrivateMessage($mid);
        } catch (MessageNotfoundException) {
            $this->setFlags("nonexist_title", "nonexist");
            return;
        }

        if (!$this->message->isRecipient($mysidia->user)) {
            $this->setFlags("nopermission_title", "nopermission");
            return;
        }
        if (!$this->message->isRead()) {
            $this->message->markRead();
        }
        $this->setField("message", new PrivateMessageViewModel($this->message));
    }

    public function newpm($uid = null)
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            try {
                $recipient = new Member($mysidia->input->post("recipient"));
                $status = $this->messageService->postNew($recipient);
                $this->setField("draftStatus", new MysString($status));
            } catch (MemberNotfoundException) {
                $this->setFlags("error", "error_user");
            }
            return;
        }
        $this->setField("recipient", $uid ? new Member($uid) : null);
    }

    public function delete($mid)
    {
        $mysidia = Registry::get("mysidia");
        try {
            $this->message = new PrivateMessage($mid);
            if (!$this->message->isRecipient($mysidia->user)) {
                $this->setFlags("nopermission_title", "nopermission");
                return;
            }
            $this->message->remove();
        } catch (MessageNotfoundException) {
            $this->setFlags("nonexist_title", "nonexist");
        }
    }

    public function outbox()
    {
        $mysidia = Registry::get("mysidia");
        try {
            $total = $mysidia->user->countMessages("outbox");
            $pagination = new Pagination($total, 10, "messages/outbox", $mysidia->input->get("page"));
            $messages = $this->messageService->getMessages("outbox", $pagination->getLimit(), $pagination->getRowsperPage());
            $this->setField("pagination", $pagination);
            $this->setField("messages", $messages);
        } catch (MessageNotfoundException) {
            $this->setFlags("message_error", "outbox_empty");
        }
    }

    public function outboxread($mid)
    {
        $mysidia = Registry::get("mysidia");
        try {
            $this->message = new PrivateMessage($mid, "outbox");
            if (!$this->message->isSender($mysidia->user)) {
                $this->setFlags("nopermission_title", "nopermission");
                return;
            }
            $this->setField("message", $this->message);
        } catch (MessageNotfoundException) {
            $this->setFlags("nonexist_title", "nonexist");
            return;
        }
        $this->setField("message", new PrivateMessageViewModel($this->message));
    }

    public function outboxdelete($mid)
    {
        $mysidia = Registry::get("mysidia");
        try {
            $this->message = new PrivateMessage($mid, "outbox");
            if (!$this->message->isSender($mysidia->user)) {
                $this->setFlags("nopermission_title", "nopermission");
                return;
            }
            $this->message->remove();
        } catch (MessageNotfoundException) {
            $this->setFlags("nonexist_title", "nonexist");
        }
    }

    public function draft()
    {
        $mysidia = Registry::get("mysidia");
        try {
            $total = $mysidia->user->countMessages("draft");
            $pagination = new Pagination($total, 10, "messages/draft", $mysidia->input->get("page"));
            $messages = $this->messageService->getMessages("draft", $pagination->getLimit(), $pagination->getRowsperPage());
            $this->setField("pagination", $pagination);
            $this->setField("messages", $messages);
        } catch (MessageNotfoundException) {
            $this->setFlags("message_error", "draft_empty");
        }
    }

    public function draftedit($mid)
    {
        $mysidia = Registry::get("mysidia");
        try {
            $this->message = new PrivateMessage($mid, "draft");
            if (!$this->message->isSender($mysidia->user)) {
                $this->setFlags("nopermission_title", "nopermission");
                return;
            }
        } catch (MessageNotfoundException) {
            $this->setFlags("nonexist_title", "nonexist");
        }
        $this->setField("message", new PrivateMessageViewModel($this->message));
    }

    public function draftdelete($mid)
    {
        $mysidia = Registry::get("mysidia");
        try {
            $this->message = new PrivateMessage($mid, "draft");
            if (!$this->message->isSender($mysidia->user)) {
                $this->setFlags("nopermission_title", "nopermission");
                return;
            }
            $this->message->remove();
        } catch (MessageNotfoundException) {
            $this->setFlags("nonexist_title", "nonexist");
        }
    }

    public function report($mid)
    {
        $mysidia = Registry::get("mysidia");
        try {
            $this->message = new PrivateMessage($mid);
        } catch (MessageNotfoundException) {
            $this->setFlags("nonexist_title", "nonexist");
            return;
        }
        if ($mysidia->input->post("submit")) {
            $this->message->report();
            return;
        }
        $this->setField("message", $this->message);
        $this->setField("admin", new Member($mysidia->settings->systemuser));
    }
}
