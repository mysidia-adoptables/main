<?php

namespace Service\Helper;

use Model\DomainModel\OwnedAdoptable;
use Resource\Collection\ArrayList;
use Resource\Core\Model;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Component\RadioButton;
use Resource\GUI\Document\Comment;
use Resource\Utility\URL;

/**
 * The AdoptTableHelper Class, extends from the TableHelper class.
 * It is a specific helper for tables that involves operations on adoptables.
 * @category Service
 * @package Helper
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not much at this point.
 *
 */

class AdoptTableHelper extends TableHelper
{
    /**
     * The getAdopt method, fetches the adoptable type.
     * @param Type $adopt
     * @access public
     * @return String
     */
    public function getAdopt($adopt)
    {
        if (empty($adopt)) {
            return "N/A";
        } else {
            return $adopt;
        }
    }

    /**
     * The getAdoptImage method, fetches the adoptable image.
     * @param Adoptable $adopt
     * @param Level  $level
     * @access public
     * @return Link
     */
    public function getAdoptImage($adopt, $level)
    {
        if ($adopt->currentlevel == 0) {
            $url = $adopt->eggimage;
        } elseif ($adopt->usealternates == 'yes') {
            $url = $level->alternateimage;
        } else {
            $url = $level->primaryimage;
        }

        $image = new Image($url, $adopt->name);
        return new Link("myadopts/manage/{$adopt->aid}", $image);
    }

    /**
     * The getLevelupLink method, fetches the adoptable image with levelup link.
     * @param OwnedAdoptable  $adopt
     * @access public
     * @return Link
     */
    public function getLevelupLink(OwnedAdoptable $adopt)
    {
        return new Link("levelup/click/{$adopt->getAdoptID()}", $adopt->getImage(Model::GUI));
    }

    /**
     * The getOwnerProfile method, wraps up the table cell with a user profile link.
     * @param int  $ownerID
     * @param String  $ownerName
     * @access protected
     * @return Link
     */
    public function getOwnerProfile($ownerID, $ownerName = null)
    {
        if (!$ownerName) {
            $ownerName = $ownerID;
        }
        $url = new URL("profile/view/{$ownerID}");
        return new Link($url, $ownerName);
    }

    /**
     * The getGenderImage method, returns the gender image of an adoptable.
     * @param String  $gender
     * @access public
     * @return Image
     */
    public function getGenderImage($gender)
    {
        return new Image("picuploads/{$gender}.png");
    }

    /**
     * The getPoundButton method, returns the radio button of a pounded adoptable.
     * @param Int  $aid
     * @access public
     * @return Image
     */
    public function getPoundButton($aid)
    {
        return new RadioButton("", "aid", $aid);
    }

    /**
     * The getBasicInfo method, retrieves the basic information of this pounded adoptable.
     * @param String  $name
     * @param Int  $cost
     * @access public
     * @return ArrayList
     */
    public function getBasicInfo($name, $cost)
    {
        $nameField = new Comment($name);
        $nameField->setBold();
        $costField = new Comment("Cost: {$cost}", false);
        $info = new ArrayList();
        $info->add($nameField);
        $info->add($costField);
        return $info;
    }

    /**
     * The getAdditionalInfo method, retrieves the additional information of this pounded adoptable.
     * @param Adoptable  $adopt
     * @access public
     * @return ArrayList
     */
    public function getAdditionalInfo($adopt)
    {
        $info = new ArrayList();
        $info->add(new Comment("level: {$adopt->getCurrentLevel()} "));
        $info->add(new Comment("Gender: ", false));
        $info->add($this->getGenderImage($adopt->getGender()));
        return $info;
    }

    /**
     * Magic method __toString for AdoptTableHelper class, it reveals that the object is an adopt table helper.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia AdoptTableHelper class.";
    }
}
