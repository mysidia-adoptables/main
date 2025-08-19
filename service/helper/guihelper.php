<?php

namespace Service\Helper;

use Resource\GUI\GUI;

/**
 * The Abstract GUIHelper Class, extends from abstract GUI class.
 * It is parent to all GUI helpers classes, they are not renderable.
 * @category Service
 * @package Helper
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not much at this point.
 * @abstract
 *
 */
abstract class GUIHelper extends GUI
{

    /**
     * The resource property, specifies which GUI resource this helper belongs to.
     * @access protected
     * @var GUI
     */
    protected $resource;

    /**
     * Constructor of GUIHelper Class, assigns the proper resource object.
     * @access public
     * @return void
     */
    public function __construct(GUI $resource)
    {
        $this->resource = $resource;
    }

    /**
     * The getResource method, getter method for property $resource
     * @access public
     * @return GUI
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * The setResource method, setter method for property $resource
     * @param GUI $resource
     * @access public
     * @return void
     */
    public function setResource(GUI $resource)
    {
        $this->resource = $resource;
    }

    /**
     * The getField method, returns the data field to be used by GUIBuilder.
     * @param String $field
     * @access public
     * @return String
     */
    public function getField($field)
    {
        if (str_contains($field, "::")) {
            $field = explode("::", $field);
            $field = $field[0];
        }
        return $field;
    }

    /**
     * Magic method __toString for GUIHelper class, it reveals that the class is a GUIHelper
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is the GUI Helper Class.";
    }
}
