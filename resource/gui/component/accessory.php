<?php

namespace Resource\GUI\Component;

use Resource\GUI\Component;
use Resource\GUI\Renderer\AccessoryRenderer;

/**
 * The Abstract Accessory Class, extends from abstract GUI Component class.
 * It is parent to all GUI Accessories type classes, but cannot be instantiated itself.
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
abstract class Accessory extends Component
{
    /**
     * The containers property, specifies which containers can hold this accessory object.
     * @access protected
     * @var Array
     */
    protected $containers = [];

    /**
     * Constructor of GUIAccessory Class, assigns the proper renderer object.
     * @access public
     * @return Void
     */
    public function __construct($id)
    {
        if (!empty($id)) {
            $this->setID($id);
        }
        $this->setLineBreak(false);
        $this->renderer = new AccessoryRenderer($this);
    }

    /**
     * The getContainers method, getter method for property $containers
     * @access public
     * @return Array
     */
    public function getContainers()
    {
        return $this->containers;
    }

    /**
     * Magic method __toString for Accessory class, it reveals that the class is an assessory type class.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is the GUI Accessory Class.";
    }
}
