<?php

namespace Resource\Collection;
use Resource\Native\Objective;

/**
 * The Listable Interface, extending from the Collective interface.
 * It defines a standard interface for any List type Objects to implement.
 * @category Resource
 * @package Collection
 * @author Hall of Famer 
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not much at this point.
 *
 */

interface Listable extends Collective{

 	/**
     * The delete method, removes an Object at the supplied index and returns the deleted object.
     * @param int  $index
     * @access public
     * @return Objective
     */	
 	public function delete($index);
	
 	/**
     * The get method, acquires the object stored at a given index.
     * @param int  $index
     * @access public
     * @return Objective
     */		
 	public function get($index);
	
	/**
     * The indexOf method, returns the first index found for a given object.
     * @param Objective  $object 
     * @access public
     * @return int
     */		
	public function indexOf(Objective $object);
	
	/**
     * The insert method, inserts an object to any given index available in the List.
	 * @param int  $index
     * @param Objective  $object 
     * @access public
     * @return void
     */		
    public function insert($index, Objective $object); 
	
	/**
     * The insertAll method, inserts a collection of objects at a given index.
	 * @param int  $index
     * @param Collective  $collection
     * @access public
     * @return void
     */		
    public function insertAll($index, Collective $collection);	
	
	/**
     * The lastIndexOf method, returns the last index found for a given object.
     * @param Objective  $object 
     * @access public
     * @return int
     */			
	public function lastIndexOf(Objective $object);
	
	/**
     * The listIterator method, acquires an instance of the listIterator object of this list.
     * @param int  $index
     * @access public
     * @return ListIterator
     */			
	public function listIterator($index);
	
	/**
     * The set method, updates a supplied index with a given object.
	 * @param int  $index
     * @param Objective  $object 
     * @access public
     * @return void
     */		
	public function set($index, Objective $object);
	
	/**
     * The listIterator method, acquires a Sublist from a certain index to an end Index of the List.
	 * @param int  $fromIndex
	 * @param int  $toIndex
     * @access public
     * @return ListIterator
     */			
	public function subList($fromIndex, $toIndex);
	
}