<?php

namespace Resource\GUI\Element;
use Resource\GUI\GUIException;
use Resource\GUI\Renderer\ElementRenderer;

/**
 * The Padding Class, extends from abstract Spacing class.
 * It defines a standard padding element to be used in HTML.
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

class Padding extends Spacing{

    /**
	 * The type property, defines this spacing element as a padding.
	 * @access protected
	 * @var String
    */
	protected $type;
	
    /**
     * Constructor of Padding Class, it simply calls parent constructor.
     * @param String  $direction
     * @param Int|String  $width
     * @access public
     * @return Void
     */
	public function __construct($direction = "", $width = ""){
	    parent::__construct($direction, $width);	
		$this->type = "padding";
	}
	
	/**
     * The getType method, getter method for property $type.    
     * @access public
     * @return String
     */
	public function getType(){
	    return $this->type;    
	}
	
	/**
     * The render method for Padding class, it renders margin data field into html readable format.
     * @access public
     * @return Void
     */
    public function render(){
	    if(!($this->renderer instanceof ElementRenderer)) throw new GUIException("Cannot find Renderer Object..."); 
	    
        if(!$this->renderer->getRender()){
            $this->renderer->renderPadding()->renderWidth();
        }
		return $this->renderer->getRender();	
    }

	/**
     * Magic method __toString for Padding class, it reveals that it is a padding object.
     * @access public
     * @return String
     */
    public function __toString(): string{
	    return "This is an instance of Mysidia Padding class.";
	}    
} 