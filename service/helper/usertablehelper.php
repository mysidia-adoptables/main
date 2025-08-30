<?php

namespace Service\Helper;

use Resource\Core\Registry;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Document\Comment;
use Resource\Utility\URL;

/**
 * The UserTableHelper Class, extends from TableHelper class.
 * It is a specialized helper class to manipulate user related tables.
 * @category Service
 * @package Helper
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not much at this point.
 *
 */

class UserTableHelper extends TableHelper
{
    /**
     * The getUsername method, wraps up the table cell with the appropriate Username.
     * @param String  $param
     * @access public
     * @return String
     */
    public function getUsername($param)
    {
        if (!$param) {
            return "Guest";
        } else {
            return $param;
        }
    }

    /**
     * The getProfileLink method, wraps up the table cell with a user profile link.
     * @param String  $uid
     * @param String  $username
     * @access public
     * @return Link
     */
    public function getProfileLink($uid, $username = null)
    {
        $path = Registry::get("path");
        if (!$username) {
            $username = $uid;
        }
        $url = new URL("{$path->getAbsolute()}profile/view/{$uid}");
        return new Link($url, $username);
    }

    /**
     * The getProfileImage method, wraps up the table cell with a user profile image.
     * @param String  $param
     * @access public
     * @return Comment|Link
     */
    public function getProfileImage($param)
    {
        if (!$param || $param == "Guest") {
            return new Comment("N/A", false);
        }
        $path = Registry::get("path");
        $url = new URL("{$path->getAbsolute()}profile/view/{$param}");
        $image = new Image("templates/buttons/profile.gif");
        return new Link($url, $image);
    }

    /**
     * The getPMImage method, wraps up the table cell with a user pm image.
     * @param String  $param
     * @access public
     * @return Comment|Link
     */
    public function getPMImage($param)
    {
        if (!$param || $param == "Guest") {
            return new Comment("N/A", false);
        }
        $url = new URL("messages/newpm/{$param}");
        $image = new Image("templates/buttons/pm.gif");
        return new Link($url, $image);
    }

    /**
     * Magic method __toString for UserTableHelper class, it reveals that the object is a user table helper.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia UserTableHelper class.";
    }
}
