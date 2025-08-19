<?php

namespace Resource\Native;

use Resource\Exception\InvalidArgumentException;
use Resource\Utility\Comparable;

/**
 * The bool Class, extending the root Object class.
 * This class serves as a wrapper class for primitive data type boolean.
 * It is a final class, no child class shall derive from bool.
 * @category Resource
 * @package Native
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.2
 * @todo Not much at this point.
 * @final
 *
 */
final class Boolean extends MysObject implements Comparable, Primitive
{

    /**
     * Size constant, specifies the size a boolean value occupies.
     */
    const Size = 8;

    /**
     * boolTrue constant, defines the True value for bool.
     */
    const booltrue = true;

    /**
     * boolfalse constant, defines the False value for bool.
     */
    const boolfalse = false;

    /**
     * The value property, which stores the primitive value for this bool object.
     * @access private
     * @var bool
     */
    private $value;

    /**
     * Constructor of bool Class, initializes the bool wrapper class.
     * If supplied argument is not of boolean type, type casting will be converted.
     * @param Any $param
     * @access public
     * @return Void
     */
    public function __construct($param)
    {
        if (!is_bool($param)) $param = (bool)$param;
        $this->value = $param;
    }

    /**
     * The getValue method, returns the primitive boolean value.
     * @access public
     * @return bool
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * The compareTo method, compares a boolean object to another.
     * @param Objective $target
     * @access public
     * @return int
     */
    public function compareTo(Objective $target)
    {
        if (!($target instanceof bool)) throw new InvalidArgumentException("Supplied argument must be a boolean value!");
        return ($this->equals($target)) ? 0 : ($this->value ? 1 : -1);
    }

    /**
     * Magic method to_String() for bool class, casts boolean value into string.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return (string)$this->value;
    }

    /**
     * Magic method __invoke() for bool class, it returns the primitive data value for manipulation.
     * @access public
     * @return Number
     */
    public function __invoke()
    {
        return $this->value;
    }
}
