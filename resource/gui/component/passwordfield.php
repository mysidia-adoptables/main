<?php

namespace Resource\GUI\Component;

use Resource\GUI\GUIException;

/**
 * The PasswordField Class, extends from TextField class.
 * It is useful for protected text field types such as Password and Hidden Fields.
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
class PasswordField extends TextField
{
    /**
     * The type property, specifies the type of this PasswordField.
     * @access protected
     * @var String
     */
    protected $type;

    /**
     * Constructor of PasswordField Class, which assigns basic password field properties.
     * @param String $type
     * @param String $name
     * @param String $value
     * @param bool $lineBreak
     * @param int $length
     * @param String $event
     * @access public
     * @return void
     */
    public function __construct($type = "", $name = "", $value = "", $lineBreak = false, $length = "", $event = "")
    {
        parent::__construct($name, $value, $length, $event);
        $this->setType($type);
        if (!$lineBreak) {
            $this->lineBreak = false;
        }
    }

    /**
     * The getType method, getter method for property $type.
     * @access public
     * @return String
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * The setType method, setter method for property $type.
     * @param String $type
     * @access public
     * @return void
     */
    public function setType($type)
    {
        $types = ["password", "email", "hidden"];
        if (!in_array($type, $types)) {
            throw new GUIException("The password field type is invalid...");
        }
        $this->type = $type;
        $this->setAttributes("Type");
    }

    /**
     * Magic method __toString for PasswordField class, it reveals that the object is a password field.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia PasswordField class.";
    }
}
