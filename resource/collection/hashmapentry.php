<?php

namespace Resource\Collection;
use Resource\Native\Objective;

/**
 * The HashMapEntry Class, extending from the MapEntry Class.
 * It defines a standard entry for HashMap type objects, which usually comes in handy.
 * @category Resource
 * @package Collection
 * @author Hall of Famer 
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not much at this point.
 *
 */

class HashMapEntry extends MapEntry{

    /**
	 * The hash property, it specifies the hash of this HashMapEntry.
	 * @access protected
	 * @var int
    */
	protected $hash;
	
    /**
	 * The next property, it stores the next entry adjacent to this one.
	 * @access protected
	 * @var MapEntry
    */
	protected $next;	

	/**
     * Constructor of HashMapEntry Class, it initializes a HashMapEntry with a key and a value.
	 * @param int  $hash
     * @param Objective  $key
	 * @param Objective  $value
	 * @param MapEntry  $entry
     * @access public
     * @return void
     */	
	public function __construct($hash = 0, Objective $key = NULL, Objective $value = NULL, MapEntry $entry = NULL){
	    parent::__construct($key, $value);
		$this->hash = $hash;
		$this->next = $entry;
	}
	
    /**
     * The equals method, checks whether target MapEntry is equivalent to this one.
     * @param Objective  $object	 
     * @access public
     * @return Boolean
     */
    public function equals(Objective $object){
        if(!($object instanceof HashMapEntry)) return FALSE;
		$key = $this->getKey();
		$key2 = $object->getKey();
		
		if($key == $key2 or ($key != NULL and $key->equals($key2))){
            $value = $this->getValue();
            $value2 = $object->getValue();
            if($value == $value2 or ($value != NULL and $value->equals($value2))) return TRUE;			
        }
        return FALSE;		
    }

	/**
     * The getHash method, getter method for property $hash. 
     * @access public
     * @return int
     */		
	public function getHash(){
	    return $this->hash;
	}	
	
	/**
     * The getNext method, getter method for property $next. 
     * @access public
     * @return MapEntry
     */		
	public function getNext(){
	    return $this->next;
	}	
	
	/**
     * The recordAccess method, it is invoked whenever the value in an entry is overriden with put method.
     * @access public
     * @return void
     */		
	public function recordAccess(HashMap $map){
	
	}
	
	/**
     * The recordRemoval method, it is invoked whenever the value in an entry is removed from HashMap.
     * @access public
     * @return void
     */		
	public function recordRemoval(HashMap $map){
	
	}	
	
	/**
     * The setNext method, setter method for property $next. 
	 * @param MapEntry  $next
     * @access public
     * @return void
     */			
	public function setNext(MapEntry $next = NULL){
	    $this->next = $next;
	}	
}