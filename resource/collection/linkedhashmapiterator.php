<?php

namespace Resource\Collection;

use Resource\Exception\IllegalStateException;
use Resource\Exception\NosuchElementException;

/**
 * The abstract LinkedHashMapIterator Class, extending from the abstract CollectionIterator Class.
 * It defines a base linked hash map iterator, it must be extended by subclasses.
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

abstract class LinkedHashMapIterator extends CollectionIterator
{
    /**
     * The last property, it defines the last returned Entry.
     * @access private
     * @var MapEntry
    */
    private $last;

    /**
     * The next property, it specifies the next Entry in iteration.
     * @access private
     * @var MapEntry
    */
    private $next;

    /**
     * Constructor of LinkedHashMapIterator Class, initializes basic properties for the iterator.
     * @param LinkedHashMap  $map
     * @access public
     * @return void
     */
    public function __construct(/**
     * The map property, it stores a reference to the LinkedHashMap object.
     * @access private
     */
        private readonly LinkedHashMap $map
    ) {
        $this->next = $this->map->getHeader()->getAfter();
    }

    /**
     * The current method, returns the current entry in the iterator.
     * @access public
     * @return Entry
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->last;
    }

    /**
     * The hasNext method, checks if the iterator has next entry.
     * This is a final method, and thus can not be overridden by child class.
     * @access public
     * @return Entry
     * @final
     */
    final public function hasNext()
    {
        return ($this->next !== $this->map->getHeader());
    }

    /**
     * The nextEntry method, returns the next entry in iteration.
     * This is a final method, and thus can not be overridden by child class.
     * @access public
     * @return Entry
     * @final
     */
    final public function nextEntry()
    {
        if ($this->next === $this->map->getHeader()) {
            throw new NosuchElementException();
        }
        $entry = $this->last = $this->next;
        $this->next = $entry->getAfter();
        return $entry;
    }

    /**
     * The remove method, removes from the underlying value associated with the current key in iteration.
     * @access public
     * @return void
     */
    public function remove()
    {
        if ($this->last == null) {
            throw new IllegalStateException();
        }
        $this->map->remove($this->last->getKey());
        $this->last = null;
    }
}
