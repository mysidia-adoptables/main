<?php

namespace Resource\Native;

/**
 * The Null Class, extending from root Object Class.
 * It defines a Null Object that does not do anything but to serve as placeholder or Null Values.
 * @category Resource
 * @package Native
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not much at this point.
 *
 */
class MysNull extends MysObject implements Primitive
{

    /**
     * The value property, which stores the primitive value for this Null object.
     * @access private
     * @var Null
     */
    private $value = null;

    /**
     * The getValue method, returns the primitive null value.
     * @access public
     * @return Null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Magic method __toString() for Null class, returns null.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "null";
    }
}
