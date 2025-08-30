<?php

namespace Resource\GUI\Component;

use Resource\GUI\GUIException;
use Resource\GUI\Renderer\TextRenderer;

/**
 * The Abstract TextComponent Class, extends from abstract InputComponent class.
 * It is parent to all GUI Text type GUI components, but cannot be instantiated itself.
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

abstract class TextComponent extends InputComponent
{
    /**
     * The maxLength property, specifies the max length allowed for this text component.
     * @access protected
     * @var int
    */
    protected $maxLength;

    /**
     * The readOnly property, specifies if the text component is read only.
     * @access protected
     * @var Boolean
    */
    protected $readOnly = false;

    /**
     * Constructor of TextComponent Class, which performs basic operations for all text types.
     * @access public
     * @return void
     */
    public function __construct($name = "", $value = "", $event = "")
    {
        parent::__construct($name, $value, $event);
        $this->renderer = new TextRenderer($this);
    }

    /**
     * The getMaxLength method, getter method for property $maxLength.
     * @access public
     * @return String
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * The setMaxLength method, setter method for property $maxLength.
     * @param int  $length
     * @access public
     * @return void
     */
    public function setMaxLength($length)
    {
        if (!is_numeric($length)) {
            throw new GUIException("The supplied max length value is not numeric!");
        }
        $this->maxLength = $length;
        $this->setAttributes("MaxLength");
    }

    /**
     * The getReadOnly method, getter method for property $readOnly.
     * @access public
     * @return Boolean
     */
    public function isReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * The setReadOnly method, setter method for property $readOnly.
     * @param Boolean  $readOnly
     * @access public
     * @return void
     */
    public function setReadOnly($readOnly)
    {
        $this->readOnly = $readOnly;
        $this->setAttributes("ReadOnly");
    }

    /**
     * The render method for TextComponent class, it renders text data fields into HTML readable format.
     * @access public
     * @return String
     */
    public function render()
    {
        if ($this->renderer->getStatus() == "ready") {
            $this->renderer->start();
            parent::render()->renderValue()->end();
        }
        return $this->renderer->getRender();
    }

    /**
     * Magic method __toString for TextComponent class, it reveals that the class belong to GUI package.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is the TextComponent Class.";
    }
}
