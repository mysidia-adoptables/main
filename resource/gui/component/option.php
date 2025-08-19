<?php

namespace Resource\GUI\Component;

/**
 * The Option Class, extends from abstract GUI Accessory class.
 * It defines a standard Option Element to be used in SelectList or DataList
 * @category Resource
 * @package GUI
 * @subpackage Component
 * @author Hall of Famer
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 *
 */
class Option extends Accessory
{
    /**
     * The text property, stores the text visible to the user from this option.
     * @access protected
     * @var String
     */
    protected $text;

    /**
     * The value property, defines the value sent to the server upon form submission.
     * @access protected
     * @var String
     */
    protected $value;

    /**
     * The label property, stores a label text of this option.
     * @access protected
     * @var String
     */
    protected $label;

    /**
     * The disabled property, specifies if the option is disabled.
     * @access protected
     * @var bool
     */
    protected $disabled = false;

    /**
     * The selected property, specifies if the option is selected by default.
     * @access protected
     * @var bool
     */
    protected $selected = false;

    /**
     * Constructor of Option Class, which assigns basic option properties.
     * @param String $text
     * @param String $value
     * @param String $event
     * @access public
     * @return void
     */
    public function __construct($text = "", $value = "", $event = "")
    {
        parent::__construct($value);
        if (!empty($text)) {
            $this->setText($text);
        }

        if (!empty($value)) {
            $this->setValue($value);
        } elseif (!empty($text) and empty($value)) {
            $this->setValue($text);
        } else {
            $this->value = "";
        }

        if (!empty($event)) {
            $this->setEvent($event);
        }
        $this->setLineBreak(false);
        $this->containers = ["DataList", "DropdownList", "SelectList"];
    }

    /**
     * The getText method, getter method for property $text.
     * @access public
     * @return String
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * The setText method, setter method for property $text.
     * @param String $text
     * @access public
     * @return void
     */
    public function setText($text)
    {
        $this->text = $text;
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
        $this->setAttributes("Value");
    }

    /**
     * The getLabel method, getter method for property $label.
     * @access public
     * @return String
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * The setLabel method, setter method for property $label.
     * @param String $label
     * @access public
     * @return void
     */
    public function setLabel($label)
    {
        $this->label = $label;
        $this->setAttributes("Label");
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
     * The isSelected method, getter method for property $selected.
     * @access public
     * @return bool
     */
    public function isSelected()
    {
        return $this->selected;
    }

    /**
     * The setSelected method, setter method for property $selected.
     * @param bool $selected
     * @access public
     * @return void
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
        $this->setAttributes("Selected");
    }

    /**
     * The render method for Option class, it renders option data fields into html readable format.
     * @access public
     * @return void
     */
    public function render()
    {
        if ($this->renderer->getStatus() == "ready") {
            $this->renderer->start();
            parent::render()->renderText()->end();
        }
        return $this->renderer->getRender();
    }

    /**
     * Magic method __toString for Option class, it reveals that the object is an option.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia Option class.";
    }
}
