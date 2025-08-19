<?php

namespace Resource\GUI\Container;
use Resource\GUI\Component\Option;
use Resource\GUI\Container;
use Resource\GUI\GUIException;
use Resource\GUI\Renderer\ListRenderer;

/**
 * The Optgroup Class, extends from abstract GUI Container class.
 * It specifies a radio button list in which only one button can be selected.
 * @category Resource
 * @package GUI
 * @subpackage Container
 * @author Hall of Famer 
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 *
 */  

class OptGroup extends Container{

	/**
	 * The label property, stores the label of this OptGroup.
	 * @access protected
	 * @var String
    */
	protected $label;
	
    /**
     * Constructor of OptGroup Class, which assigns basic property to this list
     * @param String  $label
     * @param Array|ArrayObject  $components
     * @access public
     * @return void
     */
	public function __construct($label = "", $components = ""){
        parent::__construct($components);
		if(!empty($label)) $this->label = $label;
        $this->renderer = new ListRenderer($this);		
	}
	
	/**
     * The getLabel method, getter method for property $label.    
     * @access public
     * @return String
     */
	public function getLabel(){
	    return $this->label;    
	}

	/**
     * The setLabel method, setter method for property $label.
	 * @param String  $label   
     * @access public
     * @return void
     */
	public function setLabel($label){
	    $this->label = $label;
		$this->setAttributes("Label");
	}

	/**
     * The add method, sets a Option Object to a specific index.
	 * @param Option $option
     * @param int  $index	 
     * @access public
     * @return void
     */	
	public function add(Option $option, $index = -1){
        if($option->getValue()) throw new GUIException("Cannot add an option without a value to the group.");
	    parent::add($option, $index);			
	}

	/**
     * Magic method __toString for OptGroup class, it reveals that the object is an OptGroup.
     * @access public
     * @return String
     */
    public function __toString(): string{
	    return "This is an instance of Mysidia OptGroup class.";
	}    
}