<?php

namespace Resource\Collection;

use Iterator;
use Resource\Exception\IllegalArgumentException;
use Resource\Exception\UnsupportedOperationException;
use Resource\Native\Objective;

/**
 * The HashSet Class, extending from the abstract MapSet Class.
 * It defines a standard Set class for the simplest collection manipulation.
 * @category Resource
 * @package Collection
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not much at this point.
 *
 */
class HashSet extends MapSet
{
    /**
     * serialID constant, it serves as identifier of the object being HashSet.
     */
    public const SERIALID = "-5024744406713321676L";

    /**
     * Constructor of HashSet Class, it initializes the HashSet given its capacity or another Collection Object.
     * @param int|Collective $param
     * @param Float $loadFactor
     * @param bool $linked
     * @access public
     * @return void
     */
    public function __construct($param = HashMap::DEFAULTCAPACITY, $loadFactor = HashMap::DEFAULTLOAD, $linked = false)
    {
        if ($linked) {
            $this->map = new LinkedHashMap($param, $loadFactor);
        } elseif (is_int($param)) {
            $this->map = new HashMap($param, $loadFactor);
        } elseif ($param instanceof Collective) {
            $capacity = (int)($param->size() / self::DEFAULTLOAD + 1);
            $this->map = new HashMap($capacity);
            $this->addAll($param);
        } else {
            throw new IllegalArgumentException("Invalid Argument specified.");
        }
    }

    /**
     * The add method, append a specific object to the HashSet if it is not already present.
     * @param Objective $object
     * @access public
     * @return bool
     */
    public function add(Objective $object): bool
    {
        return $this->map->put($object, null);
    }

    /**
     * The contains method, checks if a given object is already on the HashSet.
     * @param Objective $object
     * @access public
     * @return bool
     */
    public function contains(Objective $object): bool
    {
        return $this->map->containsKey($object);
    }

    /**
     * The iterator method, acquires an instance of the iterator object of the HashSet.
     * @access public
     * @return KeyIterator
     */
    public function iterator(): Iterator
    {
        return $this->map->keySet()->iterator();
    }

    /**
     * The remove method, removes a supplied Object from the HashSet if it is present.
     * @param Objective $object
     * @access public
     * @return bool
     */
    public function remove(Objective $object): bool
    {
        return ($this->map->remove($object) == $this->dummy);
    }

    /**
     * The subSet method, acquires a portion of the Set ranging from the supplied two elements.
     * @param Objective $fromElement
     * @param Objective $toElement
     * @access public
     * @return Settable
     */
    public function subSet(Objective $fromElement, Objective $toElement): never
    {
        throw new UnsupportedOperationException();
    }
}
