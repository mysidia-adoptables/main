<?php

namespace Resource\GUI\Component;

use Resource\GUI\Component;

/**
 * The Abstract InputComponent Class, extends from abstract GUI Component class.
 * It is parent to all GUI Input type GUI components, but cannot be instantiated itself.
 * @category Resource
 * @package GUI
 * @subpackage Component
 * @author Hall of Famer
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 * @abstract
 *
 */
abstract class InputComponent extends Component
{

    /**
     * The value property, specifies the value of this input component.
     * @access protected
     * @var String
     */
    protected $value;

    /**
     * The autofocus property, checks if the input component is autofocused.
     * @access protected
     * @var bool
     */
    protected $autofocus = false;

    /**
     * The disabled property, checks if the input component is disabled.
     * @access protected
     * @var bool
     */
    protected $disabled = false;

    /**
     * Constructor of InputComponent Class, which performs basic operations for all input types.
     * @param String $name
     * @param Mixed $value
     * @param String $event
     * @access public
     * @return void
     */
    public function __construct($name = "", $value = "", $event = "")
    {
        if (!empty($name)) {
            $this->setName($name);
            $this->setID($name);
        }
        if (!empty($value) || $value == 0) $this->setValue($value);
        if (!empty($event)) $this->setEvent($event);
    }

    /**
     * The getValue method, getter method for property $value.
     * @access public
     * @return String
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * The setValue method, setter method for property $value.
     * @param String $value
     * @access public
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * The isAutofocus method, getter method for property $autofocus.
     * @access public
     * @return bool
     */
    public function isAutofocus()
    {
        return $this->autofocus;
    }

    /**
     * The setAutofocus method, setter method for property $autofocus.
     * @param bool $autofocus
     * @access public
     * @return void
     */
    public function setAutofocus($autofocus = true)
    {
        $this->autofocus = $autofocus;
        $this->setAttributes("Autofocus");
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
     * Magic method __toString for InputComponent class, it reveals that the class belong to GUI package.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is the InputComponent Class.";
    }
}
