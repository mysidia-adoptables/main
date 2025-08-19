<?php

namespace Resource\Collection;

use Iterator;
use IteratorIterator;
use Resource\Exception\IllegalArgumentException;
use Resource\Native\MysArray;
use Resource\Native\Objective;

/**
 * The ArrayList Class, extending from abstract List class.
 * It defines a standard class to handle ArrayList type collections, similar to Java's ArrayList.
 * @category Resource
 * @package Collection
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not much at this point.
 *
 */
class ArrayList extends Lists
{
    /**
     * serialID constant, it serves as identifier of the object being ArrayList.
     */
    public const SERIALID = "8683452581122892189L";

    /**
     * The array property, it stores the data passed to this ArrayList.
     * @access private
     * @var MysArray
     */
    private $array;

    /**
     * The size property, it specifies the current size of the ArrayList.
     * @access private
     * @var int
     */
    private $size = 0;

    /**
     * Constructor of ArrayList Class, it initializes the ArrayList given its size or another Collection Object.
     * @param int|Collective $param
     * @access public
     * @return void
     */
    public function __construct($param = 10)
    {
        if (is_int($param)) {
            $this->array = new MysArray($param);
        } elseif ($param instanceof Collective) {
            $this->size = $param->size();
            $this->array = new MysArray($this->size);
            $iterator = $param->iterator();
            for ($i = 0; $i < $this->size; $i++) {
                $this->array[$i] = $iterator->next();
            }
        } else {
            throw new IllegalArgumentException();
        }
    }

    /**
     * The size method, returns the current size of this ArrayList.
     * @access public
     * @return int
     */
    public function size(): int
    {
        return $this->size;
    }

    public function iterator(): Iterator
    {
        return new IteratorIterator($this->array);
    }

    /**
     * The addAll method, append a collection of objects to the end of the ArrayList.
     * @param Collective $collection
     * @access public
     * @return bool
     */
    public function addAll(Collective $collection): bool
    {
        $iterator = $collection->iterator();
        $boolean = false;
        while ($iterator->hasNext()) {
            $boolean = $this->add($iterator->next());
        }
        return $boolean;
    }

    /**
     * The add method, append an object to the end of the ArrayList.
     * @param Objective $object
     * @access public
     * @return bool
     */
    public function add(Objective $object): bool
    {
        $this->ensureCapacity($this->size + 1);
        $this->array[$this->size++] = $object;
        return true;
    }

    /**
     * The ensureCapacity method, ensures the capacity of the internal array holding the List's data.
     * @param int $capacity
     * @access public
     * @return Void
     */
    public function ensureCapacity($capacity)
    {
        if ($capacity > $this->array->length()) {
            $default = $this->array->length() * 2;
            if ($capacity > $default) {
                $this->grow($capacity);
            } else {
                $this->grow($default);
            }
        }
    }

    /**
     * The grow method, increases the size of the internal array so that it can hold more objects.
     * @param int $capacity
     * @access private
     * @return void
     */
    private function grow($capacity)
    {
        $this->array->setSize($capacity);
    }

    /**
     * The clear method, drops all objects currently stored in ArrayList.
     * @access public
     * @return void
     */
    public function clear(): void
    {
        $this->size = 0;
        $this->array = new MysArray(10);
    }

    /**
     * The contains method, checks if a given object is already on the ArrayList.
     * @param Objective $object
     * @access public
     * @return bool
     */
    public function contains(Objective $object): bool
    {
        return ($this->indexOf($object) >= 0);
    }

    /**
     * The indexOf method, returns the first index found for a given object.
     * @param Objective $object
     * @access public
     * @return int
     */
    public function indexOf(Objective $object)
    {
        if ($object == null) {
            for ($i = 0; $i < $this->size; $i++) {
                if ($this->array[$i] == null) {
                    return $i;
                }
            }
        } else {
            for ($i = 0; $i < $this->size; $i++) {
                if ($object->equals($this->array[$i])) {
                    return $i;
                }
            }
        }
        return -1;
    }

    /**
     * The delete method, removes an Object at the supplied index and returns the deleted object.
     * @param int $index
     * @access public
     * @return Objective
     */
    public function delete($index)
    {
        $this->rangeCheck($index);
        $deleted = $this->array[$index];
        $newSize = $this->size - 1;

        for ($i = $index; $i < $newSize; $i++) {
            $this->array[$i] = $this->array[$i + 1];
            $this->array[$i + 1] = null;
        }
        $this->size--;
        return $deleted;
    }

    /**
     * The get method, acquires the object stored at a given index.
     * @param int $index
     * @access public
     * @return Objective
     */
    public function get($index)
    {
        $this->rangeCheck($index);
        return $this->array[$index];
    }

    /**
     * The getArray method, retrieves an instance of the internal array object.
     * @access public
     * @return MysArray
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * The insert method, inserts an object to any given index available in the ArrayList.
     * @param int $index
     * @param Objective $object
     * @access public
     * @return void
     */
    public function insert($index, Objective $object)
    {
        $this->rangeCheck($index);
        $this->ensureCapacity($this->size + 1);
        $last = $this->size - 1;
        for ($i = $last; $i >= $index; $i--) {
            $this->array[$i + 1] = $this->array[$i];
        }
        $this->array[$index] = $object;
        $this->size++;
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
        $this->rangeCheck($index);
        $this->ensureCapacity($this->size + $collection->size());
        $last = $this->size - 1;
        $offset = $collection->size();
        for ($i = $last; $i >= $index; $i--) {
            $this->array[$i + $offset] = $this->array[$i];
        }

        $iterator = $collection->iterator();
        for ($i = $index; $i < $index + $offset; $i++) {
            $this->array[$i] = $iterator->next();
        }
        $this->size += $offset;
    }

    /**
     * The lastIndexOf method, returns the last index found for a given object.
     * @param Objective $object
     * @access public
     * @return int
     */
    public function lastIndexOf(Objective $object)
    {
        if ($object == null) {
            for ($i = $this->size - 1; $i >= 0; $i--) {
                if ($this->array[$i] == null) {
                    return $i;
                }
            }
        } else {
            for ($i = $this->size - 1; $i >= 0; $i--) {
                if ($object->equals($this->array[$i])) {
                    return $i;
                }
            }
        }
        return -1;
    }

    /**
     * The removeRange method, removes a collection of objects from a starting to ending index.
     * @param int $fromIndex
     * @param int $toIndex
     * @access public
     * @return Void
     */
    public function removeRange($fromIndex, $toIndex)
    {
        $this->rangeCheck($fromIndex);
        for ($i = $fromIndex; $i < $toIndex; $i++) {
            $this->array[$i] = null;
        }

        $offset = $toIndex - $fromIndex;
        for ($i = $fromIndex; $i < $this->size - $offset; $i++) {
            $this->array[$i] = $this->array[$i + $offset];
            $this->array[$i + $offset] = null;
        }
        $this->size -= $offset;
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
        $this->rangeCheck($index);
        $element = $this->array[$index];
        $this->array[$index] = $object;
        return $element;
    }

    /**
     * The toArray method, acquires the data stored in ArrayList in Array format.
     * @access public
     * @return Array
     */
    public function toArray(): array
    {
        $this->trimSize();
        return $this->array->toArray();
    }

    /**
     * The trimSize method, cuts down the internal array's size to the current ArrayList size.
     * @access public
     * @return void
     */
    public function trimSize(): void
    {
        if ($this->size < $this->array->length()) {
            $this->array->setSize($this->size);
        }
    }
}
