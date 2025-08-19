<?php

namespace Service\Helper;

use Model\DomainModel\Member;
use Resource\Collection\ArrayList;
use Resource\Core\Registry;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Document\Comment;
use Resource\Utility\URL;

/**
 * The FriendTableHelper Class, extends from UserTableHelper class.
 * It is a specialized helper class to manipulate friend related tables.
 * @category Service
 * @package Helper
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not much at this point.
 *
 */
class FriendTableHelper extends UserTableHelper
{
    /**
     * The getAcceptLink method, wraps up the table cell with an accept friend request link.
     * @param String $param
     * @access public
     * @return String
     */
    public function getAcceptLink($param)
    {
        $path = Registry::get("path");
        $url = new URL("{$path->getAbsolute()}friends/edit/{$param}/accept");
        return new Link($url, $this->getYesImage());
    }

    /**
     * The getDeclineLink method, wraps up the table cell with an decline friend request link.
     * @param String $param
     * @access protected
     * @return public
     */
    public function getDeclineLink($param)
    {
        $path = Registry::get("path");
        $url = new URL("{$path->getAbsolute()}friends/edit/{$param}/decline");
        return new Link($url, new Image("{$path->getAbsolute()}templates/icons/delete.gif"));
    }

    /**
     * The getFriendGender method, obtains the gender image of a friend.
     * @param String $gender
     * @access public
     * @return Component
     */
    public function getFriendGender($gender)
    {
        switch (strtolower($gender)) {
            case "male":
                $genderGUI = new Image("picuploads/m.png");
                $genderGUI->setLineBreak(true);
                break;
            case "female":
                $genderGUI = new Image("picuploads/f.png");
                $genderGUI->setLineBreak(true);
                break;
            default:
                $genderGUI = new Comment("");
        }
        return $genderGUI;
    }

    /**
     * The getFriendOnline method, obtains the online status of a friend.
     * @param String $name
     * @access public
     * @return Image
     */
    public function getFriendOnline($name)
    {
        $mysidia = Registry::get("mysidia");
        $userexist = $mysidia->db->select("online", ["username"], "username = :username", ["username" => $name])->fetchColumn();
        $online = $userexist ? new Image("templates/icons/user_online.gif", "{$name} is online")
            : new Image("templates/icons/user_offline.gif", "{$name} is offline");
        return $online;
    }

    /**
     * The getFriendInfo method, wraps up an entire cell with friend information.
     * @param Member $friend
     * @access public
     * @return ArrayList
     */
    public function getFriendInfo(Member $friend)
    {
        $info = new ArrayList();
        $info->add(new Link("profile/view/{$friend->getID()}", "<strong>{$friend->getUsername()}</strong>"));
        $info->add($this->getFriendGender($friend->getProfile()->getGender()));
        $info->add(new Comment($friend->getProfile()->getnickname()));
        $info->add($this->getFriendOnline($friend->getUsername()));
        $info->add(new Link($friend->getContact()->getWebsite(), new Image("templates/icons/web.gif")));
        $info->add(new Link("messages/newpm/{$friend->getID()}", new Image("templates/icons/title.gif")));
        return $info;
    }

    /**
     * Magic method __toString for FriendTableHelper class, it reveals that the object is a friend table helper.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia FriendTableHelper class.";
    }
}
