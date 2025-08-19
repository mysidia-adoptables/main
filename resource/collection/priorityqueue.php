<?php

namespace Resource\Collection;

use Resource\Exception\IllegalArgumentException;
use Resource\Native\MysArray;
use Resource\Native\Objective;
use Resource\Utility\Comparative;

/**
 * The PriorityQueue Class, extending from abstract Queue class.
 * It defines a standard class to handle priority queue type collections, similar to Java's PriorityQueue.
 * @category Resource
 * @package Collection
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not much at this point.
 *
 */
class PriorityQueue extends Queue
{

    /**
     * serialID constant, it serves as identifier of the object being PriorityQueue.
     */
    const SERIALID = "-7720805057305804111L";

    /**
     * The queue property, it stores a balanced binary heap inside the PriorityQueue.
     * @access private
     * @var MysArray
     */
    private $queue;

    /**
     * The size property, it specifies the current size of the Priority Queue.
     * @access private
     * @var int
     */
    private $size = 0;

    /**
     * Constructor of PriorityQueue Class, it initializes the PriorityQueue through various possible mechanism.
     * @param int|Collective $param
     * @param Comparative $comparator
     * @access public
     * @return void
     */
    public function __construct($param = 10, /**
     * The comparator property, it defines the comparator used to order elements inside Priority Queue.
     * @access private
     */
    private readonly ?\Resource\Utility\Comparative $comparator = null)
    {
        if (is_int($param)) $this->queue = new MysArray($param);
        elseif ($param instanceof Collective) $this->initialize($param);
        else throw new IllegalArgumentException;
    }

    /**
     * The add method, append an object to the PriorityQueue.
     * @param Objective $object
     * @access public
     * @return bool
     */
    public function add(Objective $object): bool
    {
        return $this->offer($object);
    }

    /**
     * The clear method, drops all objects currently stored in this PriorityQueue.
     * @access public
     * @return void
     */
    public function clear(): void
    {
        $this->queue = new Arrays($this->size);
        $this->size = 0;
    }

    /**
     * The comparator method, returns the Comparator used to order elements inside this PriorityQueue.
     * @access public
     * @return Comparative
     */
    public function comparator()
    {
        return $this->comparator();
    }

    /**
     * The contains method, checks if a given object is already on the PriorityQueue.
     * @param Objective $object
     * @access public
     * @return bool
     */
    public function contains(Objective $object): bool
    {
        return ($this->indexOf($object) != -1);
    }

    /**
     * The delete method, removes an Object at the supplied index and returns the deleted object.
     * @param int $index
     * @access public
     * @return Objective
     */
    public function delete($index)
    {
        assert($i >= 0 and $i < $this->size);
        $size = --$this->size;
        if ($size == $index) $this->queue[$index] = null;
        else {
            $moved = $this->queue[$size];
            $this->queue[$size] = null;
            $this->siftDown($index, $moved);
            if ($this->queue[$index] == $moved) {
                $this->siftUp($index, $moved);
                if ($this->queue[$index] != $moved) return $moved;
            }
        }
        return null;
    }

    /**
     * The getArray method, retrieves an instance of the array object that contains data inside this PriorityQueue.
     * @access public
     * @return MysArray
     */
    public function getArray()
    {
        $array = new MysArray($this->size);
        for ($i = 0; $i < $this->size; $i++) {
            $array[$i] = $this->queue[$i];
        }
        return $array;
    }

    /**
     * The grow method, increases the size of the internal queue array so that it can hold more objects.
     * @param int $capacity
     * @access private
     * @return void
     */
    private function grow($capacity)
    {
        $this->queue->setSize($capacity);
    }

    /**
     * The heapify method, establishes the heap invariant inside the PriorityQueue.
     * @access private
     * @return void
     */
    private function heapify()
    {
        for ($i = $this->size >> 1; $i >= 0; $i--) {
            $this->siftDown($i, $this->queue[$i]);
        }
    }

    /**
     * The indexOf method, returns the first index found for a given object.
     * @param Objective $object
     * @access public
     * @return int
     */
    public function indexOf(Objective $object)
    {
        if ($object != null) {
            for ($i = 0; $i < $this->size; $i++) {
                if ($object->equals($this->queue[$i])) return $i;
            }
        }
        return -1;
    }

    /**
     * The initialize method, handles initialization of the PriorityQueue from Collection Object.
     * @param Collective $collection
     * @access private
     * @return void
     */
    private function initialize(Collective $collection)
    {
        $this->queue = $collection->getArray();
        $this->size = $collection->size();
        $this->heapify();
    }

    /**
     * The iterator method, acquires an instance of the QueueIterator object of this PriorityQueue.
     * @access public
     * @return PriorityQueueIterator
     */
    public function iterator(): \Iterator
    {
        return new PriorityQueueIterator($this);
    }

    /**
     * The offer method, inserts a specific Object into the PriorityQueue
     * @param Objective $object
     * @access public
     * @return bool
     */
    public function offer(Objective $object)
    {
        $index = $this->size;
        if ($index >= $this->queue->length()) $this->grow($index * 2);
        $this->size = $index + 1;
        if ($index == 0) $this->queue[$index] = $object;
        else $this->siftUp($index, $object);
        return true;
    }

    /**
     * The peek method, retrieves but not remove the first Object inside PriorityQueue
     * This method returns null if PriorityQueue is empty.
     * @access public
     * @return Objective
     */
    public function peek()
    {
        if ($this->size == 0) return null;
        return $this->queue[0];
    }

    /**
     * The poll method, retrieves and removes the first Object inside PriorityQueue at the same time.
     * @access public
     * @return Objective
     */
    public function poll()
    {
        if ($this->size == 0) return null;
        $size = --$this->size;
        $result = $this->queue[0];
        $element = $this->queue[$size];
        $this->queue[$size] = null;
        if ($size != 0) $this->siftDown(0, $element);
        return $result;
    }

    /**
     * The remove method, removes a supplied Object from this PriorityQueue.
     * @param Objective $object
     * @access public
     * @return bool
     */
    public function remove(Objective $object): bool
    {
        $i = $this->indexOf($object);
        if ($i == -1) return false;
        else {
            $this->delete($i);
            return true;
        }
    }

    /**
     * The removeEq method, removes a supplied Object from this PriorityQueue using reference equality.
     * @param Objective $object
     * @access public
     * @return bool
     */
    public function removeEq(Objective $object)
    {
        for ($i = 0; $i < $this->size; $i++) {
            if ($object == $this->queue[$i]) {
                $this->delete($i);
                return true;
            }
        }
        return false;
    }

    /**
     * The siftDown method, maintains max heap invariant depending on whether comparator object is set.
     * @param int $index
     * @param Objective $object
     * @access private
     * @return void
     */
    private function siftDown($index, Objective $object)
    {
        if ($this->comparator instanceof Comparative) $this->siftDownComparator($index, $object);
        else $this->siftDownComparable($index, $object);
    }

    /**
     * The siftDownComparable method, maintains max heap invariant without a given Comparator Object.
     * @param int $index
     * @param Objective $object
     * @access private
     * @return void
     */
    private function siftDownComparable($index, Objective $object)
    {
        $element = $object;
        $half = $this->size >> 1;
        while ($index < $half) {
            $current = ($index << 1) + 1;
            $child = $this->queue[$current];
            $right = $current + 1;
            if ($right < $this->size and $child->compareTo($this->queue[$right]) > 0) $child = $this->queue[$current = $right];
            if ($element->compareTo($child) <= 0) break;
            $this->queue[$index] = $child;
            $index = $current;
        }
        $this->queue[$index] = $element;
    }

    /**
     * The siftDownComparator method, maintains max heap invariant with a given Comparator Object.
     * @param int $index
     * @param Objective $object
     * @access private
     * @return void
     */
    private function siftDownComparator($index, Objective $object)
    {
        $half = $this->size >> 1;
        while ($index < $half) {
            $current = ($index << 1) + 1;
            $child = $this->queue[$current];
            $right = $current + 1;
            if ($right < $this->size and $this->comparator->compare($child, $this->queue[$right]) > 0) $child = $this->queue[$current = $right];
            if ($this->comparator->compare($object, $child) <= 0) break;
            $this->queue[$index] = $child;
            $index = $current;
        }
        $this->queue[$index] = $object;
    }

    /**
     * The siftUp method, maintains min heap invariant depending on whether comparator object is set.
     * @param int $index
     * @param Objective $object
     * @access private
     * @return void
     */
    private function siftUp($index, Objective $object)
    {
        if ($this->comparator instanceof Comparative) $this->siftUpComparator($index, $object);
        else $this->siftUpComparable($index, $object);
    }

    /**
     * The siftUpComparable method, maintains min heap invariant without a given Comparator Object.
     * @param int $index
     * @param Objective $object
     * @access private
     * @return void
     */
    private function siftUpComparable($index, Objective $object)
    {
        $element = $object;
        while ($index > 0) {
            $current = ($index - 1) >> 1;
            $parent = $this->queue[$current];
            if ($element->compareTo($parent) >= 0) break;
            $this->queue[$index] = $parent;
            $index = $current;
        }
        $this->queue[$index] = $element;
    }

    /**
     * The siftUpComparator method, maintains min heap invariant with a given Comparator Object.
     * @param int $index
     * @param Objective $object
     * @access private
     * @return void
     */
    private function siftUpComparator($index, Objective $object)
    {
        $element = $object;
        while ($index > 0) {
            $current = ($index - 1) >> 1;
            $parent = $this->queue[$current];
            if ($this->comparator->compare($element, $parent) >= 0) break;
            $this->queue[$index] = $parent;
            $index = $current;
        }
        $this->queue[$index] = $element;
    }

    /**
     * The size method, returns the current size of this PriorityQueue.
     * @access public
     * @return int
     */
    public function size(): int
    {
        return $this->size;
    }

    /**
     * The toArray method, acquires the data stored in PriorityQueue in Array format.
     * @access public
     * @return Array
     */
    public function toArray(): array
    {
        $array = $this->getArray();
        return $array->toArray();
    }
}
