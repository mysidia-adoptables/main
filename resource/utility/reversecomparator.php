<?php

namespace Resource\Utility;

use Resource\Native\MysObject;
use Resource\Native\Objective;

/**
 * The ReverseComparator Class, it is part of the utility package and extends from the Object Class.
 * It specifies a unique comparator that returns the opposite result as a standard comparator.
 * @category Resource
 * @package Utility
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not sure, but will come in handy.
 */
class ReverseComparator extends MysObject implements Comparative
{

    /**
     * serialID constant, it serves as identifier of the object being ReverseComparator.
     */
    const SERIALID = "4374092139857L";

    /**
     * The constructor for ReverseComparator Class, it initializes basic properties for reverse comparator.
     * @param Comparative $comparator
     * @access public
     * @return void
     */
    public function __construct(
        /**
         * The comparator property, it stores a reference to the standard comparator.
         * If unspecified, it will use the object's compareTo method instead.
         * @access private
         */
        private readonly ?\Resource\Utility\Comparative $comparator = null
    )
    {
    }

    /**
     * The comparator method, returns the comparator object used to order the keys.
     * @access public
     * @return Comparative
     */
    public function comparator()
    {
        return $this->comparator;
    }

    /**
     * The compare method, compares two objects with each other with its internal algorithm.
     * @param Objective $object
     * @param Objective $object2
     * @access public
     * @return int
     */
    public function compare(Objective $object, Objective $object2)
    {
        if ($this->comparator == null) return ($object2->compareTo($object));
        else return $this->comparator->compare($object2, $object);
    }

    /**
     * The equals method, checks whether target comparator is equivalent to this one.
     * @param Objective $object
     * @access public
     * @return bool
     */
    public function equals(Objective $object)
    {
        return ($object == $this or ($object instanceof ReverseComparator and $this->comparator->equals($object->comparator())));
    }

    /**
     * The hashCode method, returns the hash code for the reverse comparator.
     * Interestingly, the hashCode is exactly the opposite as the comparator property.
     * @access public
     * @return int
     */
    public function hashCode()
    {
        return (-1 * $this->comparator->hashCode());
    }
}
