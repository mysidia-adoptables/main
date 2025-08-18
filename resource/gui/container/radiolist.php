<?php

namespace Resource\GUI\Container;
use Resource\GUI\Component;
use Resource\GUI\Container;
use Resource\GUI\GUIException;
use Resource\GUI\Renderer\ListRenderer;

/**
 * The RadioList Class, extends from abstract GUI Container class.
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

class RadioList extends Container{
	
    /**
     * Constructor of RadioList Class, which assigns basic property to this list
     * @access public
     * @return Void
     */
	public function __construct($name = "", $components = "", $identity = ""){
	    if(!empty($name)){
		    $this->name = $name;
			$this->id = $name;
		}
        parent::__construct($components);
		if(!empty($identity)) $this->check($identity);
        $this->renderer = new ListRenderer($this);
	}

	/**
     * The add method, sets a RadioButton Object to a specific index.
	 * @param Component $radio
     * @param int  $index	 
     * @access public
     * @return void
     */	
	public function add(Component $radio, $index = -1){
        if($radio->getName() != $this->name) throw new GUIException("Cannot add unrelated radiobuttons to a RadioList.");
	    parent::add($radio, $index);			
	}
	
	/**
     * The check method, determines which radio button in this group should be set checked.
	 * @param String  $identity   
     * @access public
     * @return void
     */
	public function check($identity){
	    foreach($this->components as $components){
		    if($components->getValue() == $identity) $components->setChecked(TRUE);
		}		
	}

	/**
     * Magic method __toString for RadioList class, it reveals that the object is a radiolist.
     * @access public
     * @return String
     */
    public function __toString(){
	    return "This is an instance of Mysidia RadioList class.";
	}    
}