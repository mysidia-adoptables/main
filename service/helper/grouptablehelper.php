<?php

namespace Service\Helper;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;

/**
 * The GroupTableHelper Class, extends from TableHelper class.
 * It is a specialized helper class to manipulate group related tables.
 * @category Service
 * @package Helper
 * @author Hall of Famer 
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not much at this point.
 *
 */

class GroupTableHelper extends TableHelper{
	
	/**
     * The getPermissionImage method, wraps up the table cell with a permission image.
     * @param String  $param
     * @access public
     * @return Image
     */
	public function getPermissionImage($param){
	    if($param == "yes") return $this->getYesImage();
		else return $this->getNoImage();
	}
	
	/**
     * The getDeleteLink method, wraps up the table cell with a delete image/link.  
     * It overrides the TableHelper's getDeleteLink() method.	 
     * @param String  $param
     * @access public
     * @return Link
     */
    public function getDeleteLink($param){
	    if($param == 1 or $param == 3) return "N/A";
		return parent::getDeleteLink($param);
    }

	/**
     * Magic method __toString for GroupTableHelper class, it reveals that the object is a group table helper.
     * @access public
     * @return String
     */
    public function __toString(): string{
	    return "This is an instance of Mysidia GroupTableHelper class.";
	}    
} 