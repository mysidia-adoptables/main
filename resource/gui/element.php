<?php

namespace Resource\GUI;
use Resource\GUI\Renderer\ElementRenderer;

/**
 * The Abstract Element Class, extends from abstract GUI class.
 * It is parent to all GUI elements classes, such as font, color, link, image.
 * These ain't GUI Components, instead they serve as properties of GUI Components.
 * @category Resource
 * @package GUI
 * @author Hall of Famer 
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 * @abstract
 *
 */ 
 
abstract class Element extends GUI implements Renderable{
	
	/**
	 * The component property, specifies which component this element belongs to.
	 * @access protected
	 * @var Component
    */
	protected $component;
	
	/**
     * Constructor of Element Class, assigns the proper renderer object.
     * @param String  $id
     * @access public
     * @return Void
     */
	public function __construct($id = ""){
	    if(!empty($id)) $this->id = $id;
        $this->renderer = new ElementRenderer($this);
    } 
	
	/**
     * The getComponent method, getter method for property $component
     * @access public
     * @return Component
     */
	public function getComponent(){
	    return $this->component;
	}
	
	/**
     * The setComponent method, setter method for property $component
	 * @param Component  $component
     * @access public
     * @return Void
     */
	public function setComponent(Component $component){
	    $this->component = $component;
	}
	
	/**
     * Magic method __toString for Element class, it reveals that the class is a GUI Element.
     * @access public
     * @return String
     */
    public function __toString(): string{
	    return "This is the GUI Element Class.";
	}
	
	/**
     * The render method for Element class, it renders element data field into html readable format.
     * @access public
     * @return Void
     */
	 public function render(){    
        if(!$this->renderer->getRender()){
		    foreach($this->attributes as $attribute => $status){
			    $renderMethod = "render{$attribute}";
			    $this->renderer->$renderMethod();
			}
        }
		return $this->renderer->getRender();	
    }
}