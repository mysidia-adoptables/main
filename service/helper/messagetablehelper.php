<?php

namespace Service\Helper;

use Model\DomainModel\VisitorMessage;
use Resource\Collection\ArrayList;
use Resource\Core\Registry;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Document\Comment;
use Resource\Utility\URL;

/**
 * The MessageTableHelper Class, extends from the TableHelper class.
 * It is a specific helper for tables that involves operations on messages.
 * @category Service
 * @package Helper
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not much at this point.
 *
 */

class MessageTableHelper extends TableHelper
{
    /**
     * The getProfile method, generates the sender/recipient profile field for the message table.
     * @param String  $param
     * @access protected
     * @return Link|String
     */
    public function getProfile($param)
    {
        if ($param == "SYSTEM") {
            return $param;
        }
        return new Link("profile/view/{$param}", $param);
    }

    /**
     * The getRecipient method, generates the recipient field for the message table.
     * @param String  $param
     * @access protected
     * @return Link|String
     */
    public function getRecipient($param)
    {
        if ($param == "SYSTEM") {
            return $param;
        }
        return new Link("profile/view/{$param}", $param);
    }

    /**
     * The getStatus method, gets the status of the message for the message table.
     * @param String  $param
     * @access protected
     * @return Comment
     */
    public function getStatus($param = "")
    {
        $status = new Comment($param);
        if ($param == "unread") {
            $status->setBold();
        }
        return $status;
    }

    /**
     * The getReadLink method, wraps up the inbox table cell with a read image/link.
     * @param String  $param
     * @access public
     * @return String
     */
    public function getReadLink($param)
    {
        $path = Registry::get("path");
        $image = new Image("{$path->getAbsolute()}templates/icons/next.gif");
        $url = new URL("messages/read/{$param}", false, false);
        return new Link($url, $image);
    }

    /**
     * The getDeleteLink method, wraps up the inbox table cell with a delete image/link.
     * @param String  $param
     * @access public
     * @return String
     */
    public function getDeleteLink($param)
    {
        $path = Registry::get("path");
        $image = new Image("{$path->getAbsolute()}templates/icons/delete.gif");
        $url = new URL("messages/delete/{$param}", false, false);
        return new Link($url, $image);
    }

    /**
     * The getOutboxReadLink method, wraps up the outbox table cell with a read image/link.
     * @param String  $param
     * @access public
     * @return String
     */
    public function getOutboxReadLink($param)
    {
        $path = Registry::get("path");
        $image = new Image("{$path->getAbsolute()}templates/icons/next.gif");
        $url = new URL("messages/outboxread/{$param}", false, false);
        return new Link($url, $image);
    }

    /**
     * The getOutboxDeleteLink method, wraps up the outbox table cell with a delete image/link.
     * @param String  $param
     * @access public
     * @return String
     */
    public function getOutboxDeleteLink($param)
    {
        $path = Registry::get("path");
        $image = new Image("{$path->getAbsolute()}templates/icons/delete.gif");
        $url = new URL("messages/outboxdelete/{$param}", false, false);
        return new Link($url, $image);
    }

    /**
     * The getDraftEditLink method, wraps up the draft table cell with a edit image/link.
     * @param String  $param
     * @access public
     * @return String
     */
    public function getDraftEditLink($param)
    {
        $path = Registry::get("path");
        $image = new Image("{$path->getAbsolute()}templates/icons/cog.gif");
        $url = new URL("messages/draftedit/{$param}", false, false);
        return new Link($url, $image);
    }

    /**
     * The getDraftDeleteLink method, wraps up the draft table cell with a delete image/link.
     * @param String  $param
     * @access public
     * @return String
     */
    public function getDraftDeleteLink($param)
    {
        $path = Registry::get("path");
        $image = new Image("{$path->getAbsolute()}templates/icons/delete.gif");
        $url = new URL("messages/draftdelete/{$param}", false, false);
        return new Link($url, $image);
    }

    /**
     * The getAvatarImage method, returns the avatar image suitable for VMlist.
     * @param String  $avatar
     * @access public
     * @return Image
     */
    public function getAvatarImage($avatar)
    {
        return new Image($avatar, "avatar", 40);
    }

    /**
     * The getAvatarImage method, returns the avatar image suitable for VMlist.
     * @param VisitorMessage  $vmessage
     * @access protected
     * @return ArrayList
     */
    public function getVisitorMessage(VisitorMessage $vmessage)
    {
        $vmField = new ArrayList();
        $vmField->add(new Link("profile/view/{$vmessage->getSenderID()}", $vmessage->getSenderName()));
        $vmField->add(new Comment("({$vmessage->getDateSent('Y-m-d')})", false));
        $vmField->add(new Link("vmessage/view/{$vmessage->getSenderID()}/{$vmessage->getRecipientID()}", new Image("templates/icons/status.gif"), true));
        $vmField->add(new Comment(stripslashes((string) $vmessage->getContent())));
        return $vmField;
    }

    /** The getManageActions method, retrieves the links of managing visitor messages.
     * @param int  $vid
     * @access public
     * @return ArrayList
     */
    public function getManageActions($vid)
    {
        $action = new ArrayList();
        $action->add(new Link("vmessage/edit/{$vid}", new Image("templates/icons/cog.gif")));
        $action->add(new Link("vmessage/delete/{$vid}", new Image("templates/icons/delete.gif"), true));
        return $action;
    }

    /**
     * Magic method __toString for MessageTableHelper class, it reveals that the object is a message table helper.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia MessageTableHelper class.";
    }
}
