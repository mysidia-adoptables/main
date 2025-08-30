<?php

namespace Resource\GUI\Element;

use Resource\GUI\Element;

/**
 * The Align Class, extends from abstract Element class.
 * It defines a standard align element to be used in HTML.
 * @category Resource
 * @package GUI
 * @subpackage Element
 * @author Hall of Famer
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 *
 */

class Align extends Element
{
    /**
     * The horizontal property, specifies the horizontal alignment of the component.
     * @access protected
     * @var Int
    */
    protected $horizontal;

    /**
     * The vertical property, defines the vertical alignment of the component.
     * @access protected
     * @var String
    */
    protected $vertical;

    /**
     * Constructor of Align Class, which assigns basic alignment properties.
     * @param String  $horizontal
     * @param String  $vertical
     * @param String  $id
     * @param String  $event
     * @access public
     * @return Void
     */
    public function __construct($horizontal = "", $vertical = "")
    {
        parent::__construct();
        if (!empty($horizontal)) {
            $this->setHorizontal($horizontal);
        }
        if (!empty($vertical)) {
            $this->setVertical($vertical);
        }
    }

    /**
     * The getHorizontal method, getter method for property $horizontal.
     * @access public
     * @return String
     */
    public function getHorizontal()
    {
        return $this->horizontal;
    }

    /**
     * The setHorizontal method, setter method for property $horizontal.
     * @param Int  $horizontal
     * @access public
     * @return Void
     */
    public function setHorizontal($horizontal)
    {
        $this->horizontal = $horizontal;
        $this->setAttributes("Horizontal");
    }

    /**
     * The getVertical method, getter method for property $vertical.
     * @access public
     * @return String
     */
    public function getVertical()
    {
        return $this->vertical;
    }

    /**
     * The setVertical method, setter method for property $vertical.
     * @param String  $vertical
     * @access public
     * @return Void
     */
    public function setVertical($vertical)
    {
        $this->vertical = $vertical;
        $this->setAttributes("Vertical");
    }

    /**
     * Magic method __toString for Align class, it reveals that it is an alignment object.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia Align class.";
    }
}
