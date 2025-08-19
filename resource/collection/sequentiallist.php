<?php

namespace Resource\Collection;

use Resource\Exception\IndexOutOfBoundsException;
use Resource\Native\Objective;

/**
 * The abstract SequantialList Class, extending from the abstract List Class.
 * It defines basic functionality for sequential list type collections, a good example is LinkedList.
 * @category Resource
 * @package Collection
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not much at this point.
 * @abstract
 *
 */
abstract class SequentialList extends Lists
{

    /**
     * The delete method, removes an Object at the supplied index and returns the deleted object.
     * @param int $index
     * @access public
     * @return Objective
     */
    public function delete($index)
    {
        try {
            $iterator = $this->listIterator($index);
            $outCast = $iterator->next();
            $iterator->remove();
            return $outCast;
        } catch (EmptyElementException) {
            throw new IndexOutOfBoundsException("Index: {$index}");
        }
    }

    /**
     * The get method, acquires the object stored at a given index.
     * @param int $index
     * @access public
     * @return Objective
     */
    public function get($index)
    {
        try {
            $listIterator = $this->listIterator($index);
            return $listIterator->next();
        } catch (EmptyElementException) {
            throw new IndexOutOfBoundsException("Index: {$index}");
        }
    }

    /**
     * The insert method, inserts an object to any given index available in the sequential list.
     * @param int $index
     * @param Objective $object
     * @access public
     * @return void
     */
    public function insert($index, Objective $object)
    {
        try {
            $iterator = $this->listIterator($index);
            $iterator->add($object);
        } catch (EmptyElementException) {
            throw new IndexOutOfBoundsException("Index: {$index}");
        }
    }

    /**
     * The insertAll method, inserts a collection of objects at a given index.
     * @param int $index
     * @param Collective $collection
     * @access public
     * @return void
     */
    public function insertAll($index, Collective $collection)
    {
        try {
            $modified = false;
            $listIterator = $this->listIterator($index);
            $collectionIterator = $collection->iterator();
            while ($collectionIterator->hasNext()) {
                $listIterator->add($collectionIterator->next());
                $modified = true;
            }
            return $modified;
        } catch (EmptyElementException) {
            throw new IndexOutOfBoundsException("Index: {$index}");
        }
    }

    /**
     * The iterator method, acquires an instance of the listIterator object of this sequential list.
     * @access public
     * @return ListIterator
     */
    public function iterator(): \Iterator
    {
        return $this->listIterator(0);
    }

    /**
     * The set method, updates a supplied index with a given object.
     * @param int $index
     * @param Objective $object
     * @access public
     * @return void
     */
    public function set($index, Objective $object)
    {
        $iterator = $this->listIterator($index);
        $element = $iterator->next();
        $iterator->set($object);
        return $element;
    }

    /**
     * The listIterator method, acquires an instance of the ListIterator for this SequentialList.
     * This method is disabled in abstract SequentialList class, and thus child class must implement the method by overwriting it.
     * @param int $index
     * @access public
     * @return ListIterator
     */
    public function listIterator($index = 0)
    {
        throw \UNSUPPORTEDOPERATIONEXCEPTION;
    }
}
