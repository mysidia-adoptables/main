<?php

namespace Resource\GUI\Container;
use Resource\Collection\HashSet;
use Resource\GUI\Container;
use Resource\GUI\Renderer\TableRenderer;
use Resource\Native\MysString;

/**
 * The abstract TableContainer Class, extends from abstract GUI Container class.
 * It defines properties for all table type components/containers, but cannot be instantiated itself.
 * @category Resource
 * @package GUI
 * @subpackage Container
 * @author Hall of Famer 
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 * @abstract
 *
 */ 
 
abstract class TableContainer extends Container{
	
	/**
	 * The width property, it stores the width of the entire table.
	 * @access protected
	 * @var String
    */
	protected $width;
	
	/**
	 * The tableAttributes property, determines the css attributes unique to table containers.
	 * @access protected
	 * @var String
    */
	protected $tableAttributes;
	
	/**
     * Constructor of Table Class, sets up basic form properties.   
	 * @param String  $name
	 * @param String  $width
	 * @param String  $event
	 * @param ArrayList  $components
     * @access public
     * @return void
     */
	public function __construct($name = "", $width = "", $event = "", $components = ""){
        parent::__construct($components); 
        if(!empty($name)){
		    $this->setName($name);
			$this->setID($name);
		}
		if(!empty($width)) $this->setWidth($width);
		if(!empty($event)) $this->setEvent($event);
        $this->lineBreak = FALSE;
        $this->renderer = new TableRenderer($this);				
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
	 * @param int|String  $width    
     * @access public
     * @return void
     */
	public function setWidth($width){
	    if(is_numeric($width)) $this->width = "{$width}px";
	    else $this->width = $width;
		$this->setTableAttributes("Width");
	}
	
	/**
     * The getTableAttributes method, getter method for property $tableAttributes.    
     * @access public
     * @return HashSet
     */
	public function getTableAttributes(){
	    return $this->tableAttributes;
	}
	
	/**
     * The setTableAttributes method, setter method for property $tableAttributes.
     * @param String  $tableAttributes	 
     * @access public
     * @return void
     */
	public function setTableAttributes($tableAttributes){		
	    if(!$this->tableAttributes) $this->tableAttributes = new HashSet;
	    $this->tableAttributes->add(new MysString($tableAttributes));
	}
	
	/**
     * Magic method __toString for TableContainer class, it reveals that the class is a Table Container.
     * @access public
     * @return String
     */
    public function __toString(){
	    return "This is The TableContainer class.";
	}
}