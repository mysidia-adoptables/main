<?php

namespace Resource\Collection;

/**
 * The DescendingListIterator Class, extending from LinkedListIterator Class.
 * It defines a special descending list iterator, it traverses from the back to the front for a given list.
 * @category Resource
 * @package Collection
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not much at this point.
 *
 */
class DescendingListIterator extends LinkedListIterator
{

    /**
     * Constructor of DescendingListIterator Class, initializes basic properties for the iterator.
     * @param LinkedList $list
     * @access public
     * @return void
     */
    public function __construct(Lists $list)
    {
        parent::__construct($list, $list->size());
    }

    /**
     * The hasNext method, checks if the descending iterator has not reached the end of its iteration yet.
     * @access public
     * @return bool
     */
    public function hasNext()
    {
        return $this->hasPrevious();
    }

    /**
     * The next method, returns the next object in the iteration.
     * @access public
     * @return Objective
     */
    public function next()
    {
        return $this->previous();
    }
}
