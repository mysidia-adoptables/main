<?php

namespace Resource\GUI\Container;

use Resource\GUI\GUIException;

/**
 * The SelectionList Class, extends from the DropdownList class.
 * It specifies a single or multiple selection list.
 * @category Resource
 * @package GUI
 * @subpackage Container
 * @author Hall of Famer
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 *
 */
class SelectionList extends DropdownList
{

    /**
     * The size property, determines how many options are viewable in SelectList at the same time.
     * @access protected
     * @var int
     */
    protected $size;

    /**
     * The multiple property, specifies if the selection list allows multiple choices.
     * @access protected
     * @var bool
     */
    protected $multiple = false;

    /**
     * Constructor of SelectionList Class, which assigns basic property to this list
     * @param String $name
     * @param bool $multiple
     * @param Array|ArrayObject $components
     * @param String $identity
     * @param String $event
     * @access public
     * @return void
     */
    public function __construct($name = "", $multiple = false, $components = "", $identity = "", $event = "")
    {
        parent::__construct($name, $components, $identity, $event);
        if ($multiple) $this->setMultiple(true);
    }

    /**
     * The getSize method, getter method for property $size.
     * @access public
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * The setSize method, setter method for property $size.
     * @param int $size
     * @access public
     * @return void
     */
    public function setSize($size)
    {
        if (!is_numeric($size)) throw new GUIException("The specified size is not numeric!");
        $this->size = $size;
        $this->setAttributes("Size");
    }

    /**
     * The isMultiple method, getter method for property $multiple.
     * @access public
     * @return bool
     */
    public function isMultiple()
    {
        return $this->multiple;
    }

    /**
     * The setMultiple method, setter method for property $multiple.
     * @param bool $multiple
     * @access public
     * @return void
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;
        $this->setAttributes("Multiple");
    }

    /**
     * Magic method __toString for SelectionList class, it reveals that the object is a selection list.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia SelectionList class.";
    }
}
