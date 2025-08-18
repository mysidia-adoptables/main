<?php

namespace Resource\GUI\Component;

/**
 * The RadioButton Class, extends from abstract Button class.
 * It defines a standard Radio Button object to be used in HTML.
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

class RadioButton extends ButtonComponent{

    /**
	 * The type property, which is a radio button.
	 * @access protected
	 * @var String
    */
	protected $type;
	
    /**
	 * The checked property, specifies if the button is checked by default.
	 * @access protected
	 * @var Boolean
    */
	protected $checked = FALSE;
	
    /**
     * Constructor of RadioButton Class, which assigns basic radio button properties.
     * @param String  $text
     * @param String  $name
     * @param String  $value
     * @param String  $event
     * @access public
     * @return void
     */
	public function __construct($text = "", $name = "", $value = "", $event = ""){
	    parent::__construct($text, $name, $value, $event);
        $this->setType("radio");
		$this->setLineBreak(FALSE);
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
     * The setType method, setter method for property $type.
	 * @param String  $type    
     * @access public
     * @return void
     */
    public function setType($type){
        $this->type = $type;
		$this->setAttributes("Type");
    }
	
	/**
     * The isChecked method, getter method for property $checked.    
     * @access public
     * @return Boolean
     */	
	public function isChecked(){
	    return $this->checked;
	}
	
	/**
     * The setChecked method, setter method for property $checked.
	 * @param Boolean  $checked    
     * @access public
     * @return void
     */
	public function setChecked($checked){
	    $this->checked = $checked;
		$this->setAttributes("Checked");
	}

	/**
     * Magic method __toString for RadioButton class, it reveals that the object is a radio button.
     * @access public
     * @return String
     */
    public function __toString(){
	    return "This is an instance of Mysidia RadioButton class.";
	}    
}