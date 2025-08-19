<?php

namespace Service\Helper;
use Resource\Core\Registry;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Document\Comment;
use Resource\Utility\URL;

/**
 * The TableHelper Class, extends from abstract GUIHelper class.
 * It is a standard helper for tables to aid certain table construction operations.
 * @category Resource
 * @package GUI
 * @author Hall of Famer 
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not much at this point.
 *
 */

class TableHelper extends GUIHelper{

	/**
	 * The frontController property, stores a reference of the frontController to be used later.
	 * @access protected
	 * @var String
     */	
    protected $frontController;
	
	/**
	 * The appController property, stores a reference of the appController to be used later.
	 * @access protected
	 * @var String
     */	
	protected $appController;

    /**
     * Constructor of TableHelper Class, it simply serves as a wrap-up.
     * @access public
     * @return Void
     */
	public function __construct(){
        $mysidia = Registry::get("mysidia");
	    $this->frontController = ((string)$mysidia->input->get("frontcontroller") == "index") ? "" : "{$mysidia->input->get("frontcontroller")->toLowerCase()}/";
		$this->appController = (string)$mysidia->input->get("appcontroller")->toLowercase();    
	}

	/**
     * The execMethod method, returns the field content after executing the method. 
     * @param String  $field
     * @access public
     * @return String
     */
    public function execMethod($field, $method, $params = ""){
        if(!$params) return $this->$method($field);
        else return $this->$method($field, $params);
    }

	/**
     * The getImage method, wraps up an image url in an image object.   
     * @param String  $src 
     * @access protected
     * @return Image
     */
    public function getImage($src){
        return new Image(new URL($src));
    }

	/**
     * The getLink method, wraps up an image url in a hyperlink object. 
     * @param String  $href   
     * @access protected
     * @return Link
     */
    public function getLink($href){
        return new Link(new URL($href));
    }
	
	/**
     * The getText method, wraps up the table cell with text.   
     * @param String  $text
     * @access protected
     * @return Comment
     */
    public function getText($text){
		if(!$text) return new Comment("N/A");
        else return new Comment($text);
    }	
	
	/**
     * The getYesImage method, wraps up the table cell with a yes image.   
     * @param String  $param
     * @access protected
     * @return Image
     */
    public function getYesImage($param = ""){
        return new Image("templates/icons/yes.gif");
    }

	/**
     * The getNoImage method, wraps up the table cell with a no image.   
     * @param String  $param
     * @access protected
     * @return Image
     */
    public function getNoImage($param = ""){
        return new Image("templates/icons/no.gif");
    }
	
	/**
     * The getStatusImage method, wraps up the table cell with a status image.   
     * @param String  $param
     * @access protected
     * @return Image
     */
    public function getStatusImage($param){
		if($param == "active") return $this->getYesImage();
        else return $this->getNoImage();
    }

	/**
     * The getEditLink method, wraps up the table cell with a edit image/link.   
     * @param String  $param
     * @access protected
     * @return Link
     */
    public function getEditLink($param){
        $image = new Image("templates/icons/cog.gif");	    
        $url = new URL("{$this->frontController}{$this->appController}/edit/{$param}");
        return new Link($url, $image);
    }
	
	/**
     * The getDeleteLink method, wraps up the table cell with a delete image/link.   
     * @param String  $param
     * @access protected
     * @return Link
     */
    public function getDeleteLink($param){
        $image = new Image("templates/icons/delete.gif");
        $url = new URL("{$this->frontController}{$this->appController}/delete/{$param}");
        return new Link($url, $image);
    }
	
	/**
     * The getModerateLink method, wraps up the table cell with a moderate image/link.   
     * @param String  $param
     * @access protected
     * @return Link
     */
    public function getModerateLink($param){
        $image = new Image("templates/icons/status.gif");	    
        $url = new URL("{$this->frontController}{$this->appController}/moderate/{$param}");
        return new Link($url, $image);
    }	

	/**
     * Magic method __toString for TableHelper class, it reveals that the object is a table helper.
     * @access public
     * @return String
     */
    public function __toString(): string{
	    return "This is an instance of Mysidia TableHelper class.";
	}    
} 