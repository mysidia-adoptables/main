<?php

namespace Resource\GUI\Container;

use Resource\GUI\Component\Legend;
use Resource\GUI\Container;
use Resource\GUI\Renderer\ListRenderer;

/**
 * The FieldSet Class, extends from the abstract GUI Container class.
 * It specifies a standard fieldset object that defines a section.
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
class FieldSet extends Container
{
    /**
     * The disabled property, checks if elements in this fieldset is disabled.
     * @access protected
     * @var bool
     */
    protected $disabled = false;

    /**
     * Constructor of FieldSet Class, which assigns basic property to this set
     * It is possible to create a FieldSet with an ID/Name, or with a legend.
     * @param String|Legend $name
     * @param ArrayObject $components
     * @param String $event
     * @access public
     * @return void
     */
    public function __construct($name = "", $components = "", $event = "")
    {
        parent::__construct($components);
        if ($name instanceof Legend) {
            $this->setName($name->getText());
            $this->add($name);
        } elseif (!empty($name)) {
            $this->setName($name);
        } else {
            $this->name = "";
        }

        if (!empty($event)) {
            $this->setEvent($event);
        }
        $this->renderer = new ListRenderer($this);
    }

    /**
     * The isDisabled method, getter method for property $disabled.
     * @access public
     * @return bool
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * The setDisabled method, setter method for property $disabled.
     * @param bool $disabled
     * @access public
     * @return void
     */
    public function setDisabled($disabled = true)
    {
        $this->disabled = $disabled;
        $this->setAttributes("Disabled");
    }

    /**
     * Magic method __toString for FieldSet class, it reveals that the object is a fieldset.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia FieldSet class.";
    }
}
