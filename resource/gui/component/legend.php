<?php

namespace Resource\GUI\Component;

/**
 * The Legend Class, extends from abstract GUIAccessory class.
 * It defines a standard Legend Element to be used in a GUIContainer.
 * @category Resource
 * @package GUI
 * @subpackage Component
 * @author Hall of Famer 
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 *
 */  

class Legend extends Accessory{

    /**
	 * The text property, contains the text of this label.
	 * @access protected
	 * @var String
    */
	protected $text;
	
    /**
     * Constructor of Legend Class, which assigns basic properties.
     * @param String  $text
     * @param String  $id
     * @access public
     * @return void
     */
	public function __construct($text = "", $id = ""){
	    parent::__construct($id);
	    if(!empty($text)) $this->setText($text);
        $this->containers = ["Fieldset"];        
	}
	
	/**
     * The getText method, getter method for property $text.    
     * @access public
     * @return String
     */
	public function getText(){
	    return $this->text;    
	}
	
	/**
     * The setText method, setter method for property $text.
	 * @param String  $text    
     * @access public
     * @return void
     */
	public function setText($text){
	    $this->text = $text;
	}

	/**
     * The render method for Legend class, it renders option data field into html readable format.
	 * Similar to Label object, it does not call parent render method since it has only one attribute and no css.
     * @access public
     * @return void
     */
    public function render(){
	    if($this->renderer->getStatus() == "ready"){
		    $this->renderer->start()->renderText()->end();
		}
		return $this->renderer->getRender();	
    }

	/**
     * Magic method __toString for Legend class, it reveals that the object is a legend.
     * @access public
     * @return String
     */
    public function __toString(): string{
	    return "This is an instance of Mysidia Legend class.";
	}    
} 