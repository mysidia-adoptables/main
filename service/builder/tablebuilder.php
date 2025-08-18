<?php

namespace Service\Builder;
use ArrayObject, PDOStatement;
use Resource\Collection\Collective;
use Resource\Collection\Listable;
use Resource\Collection\Mappable;
use Resource\GUI\Container\Table;
use Resource\GUI\Container\TCell;
use Resource\GUI\Container\THeader;
use Resource\GUI\Container\TRow;
use Resource\GUI\GUIException;
use Service\Helper\TableHelper;

/**
 * The TableBuilder Class, extends from the GUI Table class.
 * It provides shortcut for building tables in quick manner.
 * @category Service
 * @package Builder
 * @author Hall of Famer 
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 *
 */  
 
class TableBuilder extends Table{
		
	/**
	 * The Helper property, determines the helper class used to process table content.
	 * @access protected
	 * @var TableHelper
     */
	protected $helper;
	
	/**
	 * The methods property, specifies the methods to apply on each column data.
	 * @access protected
	 * @var ArrayObject
     */
	protected $methods;
	
	/**
	 * The params property, specifies the additional params to pass to the helper methods.
	 * @access protected
	 * @var ArrayObject
     */
	protected $params;
	
	/**
     * Constructor of TableBuilder Class, it is very similar to Table class. 
	 * @param String  $name
     * @param String  $width
	 * @param Boolean  $bordered
	 * @param String  $event
	 * @param ArrayObject  $components
     * @access public
     * @return void
     */
	public function __construct($name = "", $width = "", $bordered = TRUE, $event = "", $components = ""){
        parent::__construct($name, $width, $bordered, $event, $components); 			
	}
		
	/**
     * The getHelper method, getter method for property $helper.    
     * @access public
     * @return TableHelper
     */
	public function getHelper(){
	    return $this->helper;    
	}

	/**
     * The setHelper method, setter method for property $helper.
	 * @param TableHelper  $helper   
     * @access public
     * @return void
     */
	public function setHelper(TableHelper $helper){
	    $this->helper = $helper;
	}
	
	/**
     * The getMethods method, getter method for property $methods.    
     * @access public
     * @return ArrayObject
     */
	public function getMethods(){
	    return $this->methods;    
	}
	
	/**
     * The setMethod method, setter method for property $methods.
	 * It takes care of only specified field.
	 * @param String  $field
	 * @param String  $method
     * @access public
     * @return void
     */
	public function setMethod($field, $method){
	    if(!$this->methods) $this->methods = new ArrayObject;
		$this->methods->offsetSet($field, $method);
	}

	/**
     * The setMethods method, setter method for property $methods. 
	 * Different from setMethod method, it attempts to set methods for many fields.
	 * @param Array  $methods
	 * @param Boolean  $overwrite
     * @access public
     * @return void
     */
	public function setMethods($methods, $overwrite = FALSE){
	    if(!$this->methods) $this->methods = new ArrayObject;
		if($overwrite){
			if($methods instanceof ArrayObject) $this->methods = $methods;
			else $this->methods = new ArrayObject($methods);
		}
		else{
		    foreach($methods as $field => $method){
			    $this->methods->offsetSet($field, $method);
			}
		}
	}
	
	/**
     * The getParams method, getter method for property $params.    
     * @access public
     * @return ArrayObject
     */
	public function getParams(){
	    return $this->params;    
	}
	
	/**
     * The setParams method, setter method for property $params.
     * @param String  $field
     * @param Array|ArrayObject  $params	 
     * @access public
     * @return void
     */
	public function setParams($field, $params){
	    if(!$this->params) $this->params = new ArrayObject;
	    $this->params->offsetSet($field, $params);    
	}
	
	/**
     * The buildCell method, build a table cell to the current table row.
	 * You can enter a method for the specified helper to process the cell content.
	 * @param String|TCell  $cell
     * @param String  $method	 
     * @access public
     * @return TableBuilder
     */
	public function buildCell($cell, $method = ""){
	    $row = $this->component[$this->currentIndex];
		if(!($row instanceof TRow)) throw new GUIException("The current table row is invalid.");
		if(!empty($method)) $cell = $this->helper->execMethod($cell, $method);
		$row->add($cell);
		return $this;
	}
	
	/**
     * The buildHeaders method, build a row of table headers to the current table.
	 * It is usually applied to the very beginning of the table object instantiation.	 
     * @access public
     * @return TableBuilder
     */
	public function buildHeaders(){
	    $headers = func_get_args();
		$row = new TRow;
		
		for($i = 0; $i < count($headers); $i++){
		    if($headers[$i] instanceof THeader) $row->add($headers[$i]);
			else $row->add(new THeader($headers[$i]));
		}
		$this->add($row);
		return $this;
	}
	
	/**
     * The buildRow method, build a table row with the given parameters as table cells and methods.
	 * If no argument is supplied, it will build an empty row pending for action.
	 * @param Collective  $cells
     * @access public
     * @return TableBuilder
     */
	public function buildRow(Collective $fields){
		$row = new TRow;
		$iterator = $fields->iterator();
		
		if($fields instanceof Listable){
		    while($iterator->hasNext()){
                $field = $iterator->next();
                if($field instanceof TCell) $row->add($field);
                elseif($field instanceof String) $row->add(new TCell($field->getValue()));
				else $row->add(new TCell($field));
            }
		}
		elseif($fields instanceof Mappable){
            while($iterator->hasNext()){
			    $entry = $iterator->next();
			    $field = $entry->getKey();
		        $method = $entry->getValue();
			    if($field instanceof TCell) $row->add($field->getValue());
			    elseif($this->helper and $method){
			        $cell = $this->helper->execMethod($field->getValue(), $method->getValue());
				    $row->add(new TCell($cell));
			    }
			    else $row->add(new TCell($field->getValue()));
		    }		
		}
        else throw new GUIException("Supplied Collection type is invalid!");
		
		$this->add($row);
		return $this;
	}
	
	/**
     * The buildTable method, build an entire table from sql database.
	 * It is possible to specify fields used to construct this table.
	 * @param PDOStatement  $stmt
     * @param Array  $fields
     * @param Array  $methods	 
     * @access public
     * @return void
     */
	public function buildTable(PDOStatement $stmt, $fields = "", $methods = ""){
	    if(is_array($methods)) $this->methods = new ArrayObject($methods);
	    while($dataRow = $stmt->fetchObject()){
            $tableRow = new TRow;
            for($i = 0; $i < count($fields); $i++){
			    $method = ($this->methods instanceof ArrayObject)?$this->methods->offsetGet($fields[$i]):FALSE;
                if($this->helper and $method){
                    $field = $this->helper->getField($fields[$i]);
				    $dataRow->$fields[$i] = $this->helper->execMethod($dataRow->$field, $method);	
                }
			    $tableRow->add(new TCell($dataRow->$fields[$i]));		
			}
	        $this->add($tableRow);			
        }			
	}
	
	/**
     * Magic method __toString for TableBuilder class, it reveals that the class is a TableBuilder.
     * @access public
     * @return String
     */
    public function __toString(){
	    return "This is The TableBuilder class.";
	}
}