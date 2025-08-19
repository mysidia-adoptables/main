<?php

namespace Resource\Collection;

use Resource\Exception\IllegalArgumentException;
use Resource\Exception\IllegalStateException;
use Resource\Exception\NosuchElementException;
use Resource\Native\Objective;

/**
 * The abstract Queue Class, extending from abstract Collection class and implementing Queueable Interface.
 * It defines a standard class to handle queue type collections, similar to Java's Abstract Queue.
 * However, this class is abstract and thus needs concrete implementation in order to use its functionalities.
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
abstract class Queue extends Collection implements Queueable
{
    /**
     * Constructor of Queue Class, it simply calls parent constructor.
     * @access public
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * The add method, append an object to the end of the Queue.
     * @param Objective $object
     * @access public
     * @return bool
     */
    public function add(Objective $object): bool
    {
        if ($this->offer($object)) {
            return true;
        } else {
            throw new IllegalStateException();
        }
    }

    /**
     * The addAll method, append a collection of objects to the end of the Queue.
     * @param Collective $collection
     * @access public
     * @return bool
     */
    public function addAll(Collective $collection): bool
    {
        if ($collection == $this) {
            throw \ILLEGALARGUMENTEXCEPTION;
        }
        $modified = false;
        $iterator = $collection->iterator();
        while ($iterator->hasNext()) {
            if ($this->add($iterator->getNext())) {
                $modified = true;
            }
        }
        return $modified;
    }

    /**
     * The clear method, drops all objects currently stored in the Queue.
     * @access public
     * @return void
     */
    public function clear(): void
    {
        while ($this->poll() != null) ;
    }

    /**
     * The element method, retrieves but not remove the head of the queue.
     * This method throws an Exception if the Queue is empty.
     * @access public
     * @return Objective
     */
    public function element()
    {
        $object = $this->peek();
        if ($object == null) {
            throw new NosuchElementException();
        }
        return $object;
    }

    /**
     * The erase method, removes and retrieve the head of the queue.
     * This method throws an Exception if the Queue is empty.
     * @access public
     * @return Objective
     */
    public function erase()
    {
        $object = $this->poll();
        if ($object == null) {
            throw new NosuchElementException();
        }
        return $object;
    }
}
