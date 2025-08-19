<?php

namespace Resource\GUI\Element;
use Resource\GUI\Element;

/**
 * The Dimension Class, extends from abstract GUI Element class.
 * It defines a standard dimension element used mostly in a division block.
 * @category Resource
 * @package GUI
 * @subpackage Element
 * @author Hall of Famer 
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 *
 */   

class Dimension extends Element{

	/**
	 * The width property, determines the width of the division block.
	 * @access protected
	 * @var String
    */
	protected $width;
	
	/**
	 * The height property, defines the height of the division block.
	 * @access protected
	 * @var String
    */
	protected $height;
	
    /**
     * Constructor of Dimension Class, which assigns width and/or height properties.
     * @param int|String  $width
     * @param int|String  height 
     * @access public
     * @return Void
     */
	public function __construct($width = "", $height = ""){	
	    parent::__construct();
		if(!empty($width)) $this->setWidth($width);	 
        if(!empty($height)) $this->setHeight($height);		
	}
	
	/**
     * The getWidth method, getter method for property $width.    
     * @access public
     * @return String
     */
	public function getWidth(){
	    return $this->width;
	}
	
	/**
     * The setWidth method, setter method for property $width.
	 * @param String  $width     
     * @access public
     * @return Void
     */
	public function setWidth($width){
	    $this->width = $width;
		$this->setAttributes("Width");
	}
	
	/**
     * The getHeight method, getter method for property $height.    
     * @access public
     * @return String
     */
	public function getHeight(){
	    return $this->height;
	}
	
	/**
     * The setHeight method, setter method for property $height.
	 * @param String  $height  
     * @access public
     * @return Void
     */
	public function setHeight($height){
	    $this->height = $height;
		$this->setAttributes("Height");
	}
	
	/**
     * Magic method __toString for Dimension class, it reveals that the object is a dimension.
     * @access public
     * @return String
     */
    public function __toString(): string{
	    return "This is an instance of Mysidia Dimension class.";
	}    
} 