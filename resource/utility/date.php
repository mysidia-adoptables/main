<?php

namespace Resource\Utility;

use DateInterval;
use DateTime;
use DateTimeZone;
use Resource\Native\Objective;
use ReturnTypeWillChange;

/**
 * The Date Class, it is part of the utility package and extends from the DateTime Class.
 * It implements the root Object interface, and defines a __toString method.
 * @category Resource
 * @package Utility
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo change parent class to DateTimeImmutable in next version when PHP 5.4 support is dropped.
 */
class Date extends DateTime implements Objective
{
    /**
     * The format property, it defines output format for the Date Object.
     * @access private
     * @var String
     */
    private $format = "Y-m-d";


    /**
     * The constructor for Date Class, it calls parent constructor and sets format property if necessary.
     * @param String $time
     * @param DateTimeZone $timezone
     * @param Format $format
     * @access public
     * @return Void
     */
    public function __construct($time = "now", DateTimeZone $timezone = null, $format = null)
    {
        parent::__construct($time, $timezone);
        if ($format) {
            $this->setFormat($format);
        }
    }

    /**
     * The equals method, checks whether target date is equivalent to this one.
     * @param Objective $object
     * @access public
     * @return bool
     */
    public function equals(Objective $object)
    {
        if ($object instanceof Date) {
            return ($this->hashCode() == $object->hashCode());
        }
        return false;
    }

    /**
     * The hashCode method, returns the hash code for the very Date.
     * @access public
     * @return Int
     */
    public function hashCode()
    {
        return hexdec(spl_object_hash($this));
    }

    /**
     * The getClassName method, returns class name of this very class.
     * @access public
     * @return String
     */
    public function getClassName()
    {
        return $this->getClass();
    }

    /**
     * The getFormat method, getter method for property $format.
     * @access public
     * @return String
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * The setFormat method, setter method for property $format.
     * @param String $format
     * @access public
     * @return Void
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * The serialize method, serializes the date into string format.
     * @access public
     * @return String
     */
    public function serialize()
    {
        return serialize($this);
    }

    /**
     * The unserialize method, decode a string to its date representation.
     * @param String $string
     * @access public
     * @return String
     */
    public function unserialize($string)
    {
        return unserialize($string);
    }

    /**
     * The add method, adds the specified DateInterval object to the specified Date object.
     * This method is implemented to not modify the original Date object, similar to DateTimeImmutable in PHP 5.5+.
     * @param DateInterval $interval
     * @access public
     * @return Date
     */
    #[ReturnTypeWillChange]
    public function add(DateInterval $interval)
    {
        $cloneDate = clone $this;
        $cloneDate->addMutable($interval);
        return $cloneDate;
    }

    /**
     * The addMutable method, adds the specified DateInterval object to the specified Date object.
     * This method should only be used internally to implement immutable date by mutating cloned date.
     * @param DateInterval $interval
     * @access public
     * @return void
     */
    public function addMutable(DateInterval $interval)
    {
        return parent::add($interval);
    }

    /**
     * The modify method, creates a new Date object with modified time interval.
     * This method is implemented to not modify the original Date object, similar to DateTimeImmutable in PHP 5.5+.
     * @param String $modifier
     * @access public
     * @return Date
     */
    #[ReturnTypeWillChange]
    public function modify($modifier)
    {
        $cloneDate = clone $this;
        $cloneDate->modifyMutable($modifier);
        return $cloneDate;
    }

    /**
     * The modifyMutable method, mutate the Date object with modified time interval.
     * This method should only be used internally to implement immutable date by mutating cloned date.
     * @param String $modifier
     * @access public
     * @return void
     */
    public function modifyMutable($modifier)
    {
        parent::modify($modifier);
    }

    /**
     * Magic method __toString() for Date class, outputs date information.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return $this->format($this->format);
    }
}
