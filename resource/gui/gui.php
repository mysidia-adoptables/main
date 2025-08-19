<?php

namespace Resource\GUI;

use ArrayObject;
use Resource\Native\MysObject;

/**
 * The Abstract GUI Class, extends from abstract MysObject class.
 * It is parent to all Mysidia GUI classes, but cannot be instantiated itself.
 * @category Resource
 * @package GUI
 * @author Hall of Famer
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 * @abstract
 *
 */
abstract class GUI extends MysObject
{

    /**
     * The id property, specifies the id of this component.
     * @access protected
     * @var String
     */
    protected $id;

    /**
     * The class property, specifies the class of this component.
     * @access protected
     * @var String
     */
    protected $class;

    /**
     * The event property, it holds information for javascript events.
     * @access protected
     * @var String
     */
    protected $event;

    /**
     * The attributes property, which stores an array of attributes pending to render.
     * @access protected
     * @var ArrayObject
     */
    protected $attributes;

    /**
     * The renderer property, which stores a reference to the render object.
     * @access protected
     * @var Renderer
     */
    protected $renderer;

    /**
     * The getID method, getter method for property $id.
     * @access public
     * @return String
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * The setID method, setter method for property $id.
     * @param String $id
     * @access public
     * @return void
     */
    public function setID($id)
    {
        $this->id = $id;
        $this->setAttributes("ID");
    }

    /**
     * The getClass method, getter method for property $class.
     * @access public
     * @return String
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * The setClass method, setter method for property $class.
     * @param String $class
     * @access public
     * @return void
     */
    public function setClass($class)
    {
        $this->class = $class;
        $this->setAttributes("Class");
    }

    /**
     * The getEvent method, getter method for property $event.
     * @access public
     * @return String
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * The setEvent method, setter method for property $event.
     * @param String $event
     * @access public
     * @return void
     */
    public function setEvent($event)
    {
        $this->event = $event;
        $this->setAttributes("Event");
    }

    /**
     * The getAttributes method, getter method for property $attributes.
     * @access public
     * @return ArrayObject
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * The setAttributes method, setter method for property $attributes.
     * This method is reserved for the GUI class to use only.
     * @param String $attributes
     * @access protected
     * @return void
     */
    public function setAttributes($attributes)
    {
        if (!$this->attributes) $this->attributes = new ArrayObject;
        $this->attributes->offsetSet($attributes, true);
    }

    /**
     * The getRenderer method, getter method for property $renderer.
     * @access public
     * @return Renderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * The setRenderer method, setter method for property $renderer.
     * @param Renderer $renderer
     * @access public
     * @return void
     */
    public function setRenderer(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Magic method __toString for GUI class, it reveals that the class belong to GUI package.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is The base GUI Class.";
    }
}
