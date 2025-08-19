<?php

namespace Resource\GUI\Component;
use ArrayObject;
use Resource\GUI\GUIException;

/**
 * The FileField Class, extends from TextField class.
 * It provides a way to implement file selection object in GUI package.
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

class FileField extends TextField{

    /**
	 * The type property, specifies the type of this file field.
	 * @access protected
	 * @var String
    */
	protected $type;

    /**
	 * The accept property, defines the acceptable file type.
	 * @access protected
	 * @var String
    */
	protected $accept;
	
	/**
	 * The files property, determines what are the allowable file types. 
	 * @access protected
	 * @var String
    */
	protected $files = ["audio", "video", "image"];
	
	/**
     * Constructor of FileField Class, which assigns basic file field properties.
     * @param String  $name
     * @param String  $accept
     * @access public
     * @return void
     */
	public function __construct($name = "", $accept = ""){
	    parent::__construct($name);
		if(!empty($accept)) $this->setAccept($accept);
        $this->setType("file");		
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
     * @access protected
     * @return void
     */
	protected function setType($type){
		$this->type = $type;
		$this->setAttributes("Type");
	}
	
	/**
     * The getAccept method, getter method for property $accept.    
     * @access public
     * @return String
     */	
	public function getAccept(){
	    return $this->accept;
	}
	
	/**
     * The setAccept method, setter method for property $accept.    
	 * @param String  $accept
     * @access public
     * @return Void
     */	
	public function setAccept($accept){
	    if(!in_array($accept, $this->files)) throw new GUIException("The specified file type is invalid.");
	    $this->accept = $accept;
	}

	/**
     * The render method for FileField class, it renders filefield data fields into html readable format.
	 * It has its own unique implementation of render method, does not call parent render method.
     * @access public
     * @return void
     */
    public function render(){    
        if($this->renderer->getStatus() == "ready"){
			$this->renderer->start();	
	        if($this->css instanceof ArrayObject) $this->renderer->renderCSS();
			
		    if($this->attributes instanceof ArrayObject){
		        foreach($this->attributes as $attribute => $status){
			        $renderMethod = "render{$attribute}";
			        $this->renderer->$renderMethod();
			    }
		    }           
			$this->renderer->renderAccept()->end();
        }
		return $this->renderer->getRender();	
    }

	/**
     * Magic method __toString for FileField class, it reveals that the object is a file field.
     * @access public
     * @return String
     */
    public function __toString(): string{
	    return "This is an instance of Mysidia FileField class.";
	}    
}