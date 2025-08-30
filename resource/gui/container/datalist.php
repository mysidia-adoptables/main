<?php

namespace Resource\GUI\Container;

use Resource\GUI\Component\Option;
use Resource\GUI\Container;
use Resource\GUI\GUIException;
use Resource\GUI\Renderer\ListRenderer;

/**
 * The DataList Class, extends from abstract Container class.
 * It specifies a datalist with options available while typing.
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

class DataList extends Container
{
    /**
     * Constructor of DataList Class, which assigns basic property to this list
     * @param String  $name
     * @param Array|ArrayObject  $components
     * @access public
     * @return void
     */
    public function __construct($name = "", $components = "")
    {
        if (!empty($name)) {
            $this->setName($name);
            $this->setID($name);
        }
        $this->lineBreak = false;

        parent::__construct($components);
        $this->renderer = new ListRenderer($this);
    }

    /**
     * The add method, sets an Option Object to a specific index.
     * @param Option  $option
     * @param int  $index
     * @access public
     * @return void
     */
    public function add(Option $option, $index = -1)
    {
        if (!$option->getValue()) {
            throw new GUIException("Cannot add an option without a value to DataList!");
        }
        parent::add($option, $index);
    }

    /**
     * The fill method, fill in this selectlist with options from database starting at a given index.
     * To use it, you need PDO or MySQLi to fetch all rows with one or two properties to serve as Texts and Values.
     * It is designed to be flexible, but still need further development to make it better.
     * @param Array  $texts
     * @param Array  $values
     * @param String  $identity
     * @param int  $index
     * @access public
     * @return void
     */
    public function fill($texts, $values = "", $identity = "", $index = -1)
    {
        if ($index != -1) {
            $this->currentIndex = $index;
        } elseif (!is_array($values)) {
            $values = $texts;
        } elseif (count($texts) != count($values)) {
            throw new GUIException("Cannot fill option objects inside this selectlist");
        }

        for ($i = 0; $i < count($texts); $i++) {
            $option = new Option($texts[$i], $values[$i]);
            if ($option->getValue() == $identity) {
                $option->setSelected(true);
            }
            $this->add($option, $index);
        }
    }

    /**
     * Magic method __toString for DataList class, it reveals that the object is a datalist.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia DataList class.";
    }
}
