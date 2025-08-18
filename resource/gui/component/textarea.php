<?php

namespace Resource\GUI\Component;
use Resource\GUI\GUIException;

/**
 * The TextArea Class, extends from abstract TextComponent class.
 * It defines an editable textarea in HTML.
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

class TextArea extends TextComponent{

    /**
	 * The rows property, specifies the height of this text area.
	 * @access protected
	 * @var int
    */
	protected $rows = 4;

    /**
	 * The cols property, specifies the width of this text area.
	 * @access protected
	 * @var int
    */
	protected $cols = 50;
	
	/**
	 * The wraps property, determines if the text area is hard or soft wrapped upon form submission.
	 * soft = FALSE(default), hard = TRUE.
	 * @access protected
	 * @var Boolean
    */
	protected $wrap = FALSE;
	
	/**
     * Constructor of TextArea Class, which assigns basic text area properties.
     * @param String  $name
     * @param String  $value
     * @param int  $rows
     * @param int  $cols
     * @param String  $event
     * @access public
     * @return void
     */
	public function __construct($name = "", $value = "", $rows = "", $cols = "", $event = ""){
	    parent::__construct($name, $value, $event);
		if(is_numeric($rows)) $this->setRows($rows);
		if(is_numeric($cols)) $this->setCols($cols);
	}
	
	/**
     * The getRows method, getter method for property $rows.    
     * @access public
     * @return int
     */	
	public function getRows(){
	    return $this->rows;
	}
	
	/**
     * The setRows method, setter method for property $rows.
	 * @param int  $rows  
     * @access public
     * @return void
     */
	public function setRows($rows){
	    if(!is_numeric($rows)) throw new GUIException("The supplied height is not numeric!");
	    $this->rows = $rows;
		$this->setAttributes("Rows");
	}
	
	/**
     * The getCols method, getter method for property $cols.    
     * @access public
     * @return int
     */	
	public function getCols(){
	    return $this->cols;
	}
	
	/**
     * The setCols method, setter method for property $cols.
	 * @param int  $cols  
     * @access public
     * @return void
     */
	public function setCols($cols){
	    if(!is_numeric($cols)) throw new GUIException("The supplied height is not numeric!");
	    $this->cols = $cols;
		$this->setAttributes("Cols");
	}
	
	/**
     * The isWrapped method, getter method for property $wrap.    
     * @access public
     * @return Boolean
     */	
	public function isWrapped(){
	    return $this->wrap;
	}
	
	/**
     * The setWrapped method, setter method for property $wrap.
	 * @param Boolean  $wrap
     * @access public
     * @return void
     */
	public function setWrapped($wrap){
	    $this->wrap = $wrap;
		$this->setAttributes("Wrapped");
	}
	
	/**
     * The append method, append a string to the end of TextArea.
	 * @param String  $text
     * @access public
     * @return void
     */
	public function append($text){
	    $this->value .= $text;
	}
	
	/**
     * The insert method, insert a string in the specified position in TextArea.
	 * @param String  $text
	 * @param int  $position
     * @access public
     * @return void
     */
	public function insert($text, $position){
	    if(!is_numeric($position)) throw new GUIException("The supplied position is not numeric!");
		$text1 = substr_replace($this->value, $text, $position);
	    $text2 = substr_replace($this->value, "", 0, $position);
		$this->value = $text1.$text2;
	}
	
	/**
     * The replace method, replace a string from a starting to an end index in TextArea.
	 * @param String  $text
	 * @param int  $start
	 * @param int  $end
     * @access public
     * @return void
     */
	public function replace($text, $start, $end){
	    if(!is_numeric($start) or !is_numeric($end)) throw new GUIException("The supplied positions are not numeric!");
		$length = $end - $start;
		$this->value = substr_replace($this->value, $text, $start, $length);
	}
	
	/**
     * The lineCount method, returns the actual number of lines contained in TextArea.    
     * @access public
     * @return int
     */	
	public function lineCount(){
	    return ceil(strlen($this->value)/$this->rows);
	}

	/**
     * Magic method __toString for TextArea class, it reveals that the object is a text area.
     * @access public
     * @return String
     */
    public function __toString(){
	    return "This is an instance of Mysidia TextArea class.";
	}    
}