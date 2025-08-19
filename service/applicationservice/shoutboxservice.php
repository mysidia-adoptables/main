<?php

namespace Service\ApplicationService;

use HTMLPurifier;
use Model\DomainModel\ShoutComment;
use Resource\Collection\ArrayList;
use Resource\Core\Registry;
use Resource\Exception\GuestNoaccessException;
use Resource\Native\MysObject;
use Resource\Utility\Date;

class ShoutboxService extends MysObject
{

    private $htmlPurifier;

    public function __construct($htmlPurifier = true, private $limit = 10)
    {
        if ($htmlPurifier) $this->htmlPurifier = new HTMLPurifier;
    }

    public function getMessages()
    {
        $mysidia = Registry::get("mysidia");
        $stmt = $mysidia->db->select("shoutbox", [], "1 ORDER BY id DESC LIMIT 0, {$this->limit}");
        $messages = new ArrayList;
        while ($dto = $stmt->fetchObject()) {
            $messages->add(new ShoutComment($dto->id, $dto));
        }
        return $messages;
    }

    public function format($text)
    {
        return stripslashes(html_entity_decode(strip_tags((string) $text, "<b><i><u><s><p><sub><sup><a><li><ul><ol>")));
    }

    public function postMessage($comment)
    {
        $mysidia = Registry::get("mysidia");
        if (!$mysidia->user->isLoggedIn()) throw new GuestNoaccessException("guest");
        $today = new Date;
        if ($this->htmlPurifier) $comment = $this->htmlPurifier->purify($this->format($comment));
        $mysidia->db->insert("shoutbox", ["id" => null, "user" => $mysidia->user->getID(), "date" => $today->format("Y-m-d H:i:s"), "comment" => $comment]);
    }
}
