<?php

namespace Resource\Collection;

use Resource\Native\MysObject;

/**
 * The CollectionIterator Class, extending from root Object Class and implements the Iterative interface.
 * It defines a standard collection iterator, it must be extended by subclasses.
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

abstract class CollectionIterator extends MysObject implements Iterative
{
    /**
     * The cursor property, it specifies the current index location for the iterator.
     * @access protected
     * @var int
    */
    protected $cursor = 0;

    /**
     * The forward method, increments the current index by a magnitude of 1.
     * @access public
     * @return void
     */
    public function forward()
    {
        $this->cursor++;
    }

    /**
     * The key method, returns the current index location.
     * @access public
     * @return int
     */
    public function key(): int
    {
        return $this->cursor;
    }

    /**
     * The rewind method, sets the cursor of the iterator back to the beginning.
     * @access public
     * @return void
     */
    public function rewind(): void
    {
        $this->cursor = 0;
    }

    /**
     * The valid method, alias to hasNext method.
     * @access public
     * @return Boolean
     */
    public function valid(): bool
    {
        return $this->hasNext();
    }
}
