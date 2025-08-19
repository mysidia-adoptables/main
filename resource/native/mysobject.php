<?php

namespace Resource\Native;

/**
 * The Abstract MysObject Class, root of all Mysidia library files.
 * Contrary to Java's Object root class, this one is abstract.
 * For this reason, one cannot instantiate an object of this class.
 * @category Resource
 * @package Native
 * @author Hall of Famer
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.2
 * @todo Restructure the namespace
 * @abstract
 *
 */
abstract class MysObject implements Objective
{

    /**
     * Magic method __clone() for Object Class, returns a copy of Object.
     * @access public
     * @return Object
     */
    public function __clone()
    {
    }

    /**
     * The equals method, checks whether target object is equivalent to this one.
     * @param Objective $object
     * @access public
     * @return bool
     */
    public function equals(Objective $object)
    {
        return ($this == $object);
    }

    /**
     * The getClassName method, returns class name of an instance.
     * The return value may differ depending on child classes.
     * @access public
     * @return String
     */
    public function getClassName()
    {
        return static::class;
    }

    /**
     * The hashCode method, returns the hash code for the very Object.
     * @access public
     * @return Int
     */
    public function hashCode()
    {
        return hexdec(spl_object_hash($this));
    }

    /**
     * The hasMethod method, examines if the object has a certain method.
     * @param String $method
     * @access public
     * @return bool
     */
    public function hasMethod($method)
    {
        return method_exists($this, $method);
    }

    /**
     * The hasProperty method, finds if the object contains a certain property.
     * @param String $property
     * @access public
     * @return bool
     */
    public function hasProperty($property)
    {
        return property_exists($this, $property);
    }

    /**
     * The serialize method, serializes an object into string format.
     * A serialized string can be stored in Constants, Database and Sessions.
     * @access public
     * @return String
     */
    public function serialize()
    {
        return serialize($this);
    }

    /**
     * The unserialize method, decode a string to its object representation.
     * This method can be used to retrieve object info from Constants, Database and Sessions.
     * @param String $string
     * @access public
     * @return String
     */
    public function unserialize($string = "")
    {
        return unserialize($string);
    }

    /**
     * Magic method to_String() for Object class, returns object information.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return $this->getClassName();
    }
}
