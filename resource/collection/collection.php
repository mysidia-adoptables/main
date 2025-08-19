<?php

namespace Resource\Collection;

use Iterator;
use Resource\Exception\UnsupportedOperationException;
use Resource\Native\MysObject;
use Resource\Native\Objective;

/**
 * The abstract Collection Class, extending from the root Object Class and implements Collective Interface.
 * It is parent to all Collection objects, subclasses have access to all its defined methods.
 */
abstract class Collection extends MysObject implements Collective
{
    /**
     * The addAll method, append a collection of objects to the end of the Collection.
     */
    public function addAll(Collective $collection): bool
    {
        $added = false;
        foreach ($collection as $object) {
            if ($this->add($object)) {
                $added = true;
            }
        }
        return $added;
    }

    /**
     * The add method, append an object to the end of the collection.
     */
    public function add(Objective $object): bool
    {
        throw new UnsupportedOperationException();
    }

    /**
     * The clear method, drops all objects currently stored in Collection.
     */
    public function clear(): void
    {
        $iterator = $this->iterator();
        while ($iterator->valid()) {
            $iterator->next();
            $iterator->remove();
        }
    }

    /**
     * The abstract iterator method, must be implemented by child class.
     */
    abstract public function iterator(): Iterator;

    /**
     * The remove method, removes a supplied Object from this collection.
     */
    public function remove(Objective $object): bool
    {
        $iterator = $this->iterator();
        while ($iterator->valid()) {
            if ($object->equals($iterator->next())) {
                $iterator->remove();
                return true;
            }
        }
        return false;
    }

    /**
     * The containsAll method, checks if a collection of objects are all available on the Collection.
     */
    public function containsAll(Collective $collection): bool
    {
        foreach ($collection as $object) {
            if (!$this->contains($object)) {
                return false;
            }
        }
        return true;
    }

    /**
     * The contains method, checks if a given object is already on the Collection.
     */
    public function contains(Objective $object): bool
    {
        $iterator = $this->iterator();
        while ($iterator->valid()) {
            if ($object->equals($iterator->next())) {
                return true;
            }
        }
        return false;
    }

    /**
     * The count method, alias to the size() method.
     */
    public function count(): int
    {
        return $this->size();
    }

    /**
     * The abstract size method, must be implemented by child class.
     */
    abstract public function size(): int;

    /**
     * The getIterator method, alias to the iterator() method.
     */
    public function getIterator(): Iterator
    {
        return $this->iterator();
    }

    /**
     * The hashCode method, returns the hash code for this collection.
     */
    public function hashCode(): int
    {
        // No return value since null isn't valid for string return type
        return 0;
    }

    /**
     * The isEmpty method, checks if the collection is empty or not.
     */
    public function isEmpty(): bool
    {
        return $this->size() === 0;
    }

    /**
     * The removeAll method, removes a collection of objects from this collection.
     */
    public function removeAll(Collective $collection): bool
    {
        $removed = false;
        $iterator = $this->iterator();
        while ($iterator->valid()) {
            if ($collection->contains($iterator->next())) {
                $iterator->remove();
                $removed = true;
            }
        }
        return $removed;
    }

    /**
     * The retainAll method, removes everything but the given collection of objects from this collection.
     */
    public function retainAll(Collective $collection): bool
    {
        $retained = false;
        $iterator = $this->iterator();
        while ($iterator->valid()) {
            if (!$collection->contains($iterator->next())) {
                $iterator->remove();
                $retained = true;
            }
        }
        return $retained;
    }

    /**
     * The toArray method, acquires an array copy of the objects stored in the collection.
     */
    public function toArray(): array
    {
        $iterator = $this->iterator();
        $array = [];
        while ($iterator->valid()) {
            $array[] = $iterator->next();
        }
        return $array;
    }

    /**
     * The magic method __toString, defines the string expression of the collection.
     */
    public function __toString(): string
    {
        $iterator = $this->iterator();
        if (!$iterator->valid()) {
            return "[]";
        }

        $stringBuilder = "[";
        while ($iterator->valid()) {
            $object = $iterator->next();
            $stringBuilder .= ($object === $this) ? "(this collection)" : $object;
            if (!$iterator->valid()) {
                return $stringBuilder . "]";
            }
            $stringBuilder .= ", ";
        }

        return $stringBuilder . "]";
    }
}
