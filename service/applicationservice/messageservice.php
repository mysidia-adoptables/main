<?php

namespace Service\ApplicationService;

use Model\DomainModel\Member;
use Model\DomainModel\PrivateMessage;
use Resource\Collection\ArrayList;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Native\MysObject;

class MessageService extends MysObject
{

    public function getMessages($folder, $limit, $rows)
    {
        $mysidia = Registry::get("mysidia");
        if ($folder == "inbox") $stmt = $mysidia->db->select("messages", [], "touser = '{$mysidia->user->getID()}' ORDER BY mid DESC LIMIT {$limit},{$rows}");
        else $stmt = $mysidia->db->select("folders_messages", [], "fromuser='{$mysidia->user->getID()}' AND folder='{$folder}' ORDER BY mid DESC LIMIT {$limit},{$rows}");
        if ($stmt->rowCount() == 0) return null;

        $messages = new ArrayList;
        while ($dto = $stmt->fetchObject()) {
            $messages->add(new PrivateMessage($dto->mid, $folder, $dto));
        }
        return $messages;
    }

    public function postNew(Member $recipient)
    {
        $mysidia = Registry::get("mysidia");
        $this->validateRecipient($recipient);
        $message = new PrivateMessage;
        $message->setRecipient($recipient->getID());
        if ($mysidia->input->post("draft") == "yes") {
            $message->setMessage($mysidia->input->post("mtitle"), $mysidia->input->rawPost("mtext"));
            $message->postDraft($mysidia->input->post("recipient"));
            return "draftnew";
        } elseif ($mysidia->input->post("draftedit") == "yes") {
            $message->setMessage($mysidia->input->post("mtitle"), $mysidia->input->rawPost("mtext"));
            $message->editDraft();
            return "draftedit";
        } else {
            $this->validateMessage();
            $message->post();
            return "posted";
        }
    }

    private function validateRecipient(Member $recipient)
    {
        $mysidia = Registry::get("mysidia");
        $options = $recipient->getOption();
        if ($options->getPMStatus() == 1 && !$recipient->isFriend($mysidia->user)) throw new InvalidActionException("error_friend");
    }

    private function validateMessage()
    {
        $mysidia = Registry::get("mysidia");
        if (!$mysidia->input->post("mtitle") || !$mysidia->input->post("mtext")) throw new InvalidActionException("error_blank");
        if ($mysidia->input->post("outbox") == "yes" && $mysidia->input->post("draft")) throw new InvalidActionException("draft_conflict");
    }
}
