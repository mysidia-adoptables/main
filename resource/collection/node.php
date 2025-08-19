<?php

namespace Resource\Collection;

use Resource\Native\MysObject;
use Resource\Native\Objective;

/**
 * The Node Class, extending from the root Object Class.
 * It defines a standard node type object that holds reference to its neighbor nodes.
 * @category Resource
 * @package Collection
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not much at this point.
 *
 */
class Node extends MysObject
{

    /**
     * Constructor of LinkedList Class, it initializes the LinkedList.
     * @param Objective $object
     * @param Node $next
     * @param Node $prev
     * @access public
     * @return void
     */
    public function __construct(
        /**
         * The object property, it contains the actual object passed onto this Node.
         * @access private
         */
        private ?\Resource\Native\Objective $object = null,
        /**
         * The next property, it stores a reference of the next node adjacent to this Node.
         * @access private
         */
        private ?\Resource\Collection\Node $next = null,
        /**
         * The prev property, it stores a reference of the previous node adjacent to this Node.
         * @access private
         */
        private ?\Resource\Collection\Node $prev = null
    )
    {
    }

    /**
     * The get method, getter method for property $object.
     * @access public
     * @return Objective
     */
    public function get()
    {
        return $this->object;
    }

    /**
     * The getNext method, getter method for property $next.
     * @access public
     * @return Node
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * The getPrev method, getter method for property $prev.
     * @access public
     * @return Node
     */
    public function getPrev()
    {
        return $this->prev;
    }

    /**
     * The set method, setter method for property $object.
     * @param Objective $object
     * @access public
     * @return void
     */
    public function set(Objective $object = null)
    {
        $this->object = $object;
    }

    /**
     * The setNext method, setter method for property $next.
     * @param Node $next
     * @access public
     * @return void
     */
    public function setNext(Node $next = null)
    {
        $this->next = $next;
    }

    /**
     * The setPrev method, setter method for property $prev.
     * @param Node $prev
     * @access public
     * @return void
     */
    public function setPrev(Node $prev = null)
    {
        $this->prev = $prev;
    }
}
