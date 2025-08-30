<?php

namespace Resource\GUI\Container;

use ArrayObject;
use Resource\GUI\GUIException;

/**
 * The TRow Class, extends from abstract TableContainer class.
 * It defines a standard table row with the tag <tr>, can be added to container Table.
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

class TRow extends TableContainer
{
    /**
     * The char property, specifies the alignment char for the table row.
     * @access protected
     * @var String
    */
    protected $char;

    /**
     * Constructor of TRow Class, sets up basic table properties and calls parent constructor.
     * @param String  $name
     * @param String  $width
     * @param String  $event
     * @param ArrayObject  $components
     * @access public
     * @return void
     */
    public function __construct($name = "", $width = "", $event = "", $components = "")
    {
        parent::__construct($name, $width, $event, $components);
    }

    /**
     * The getChar method, getter method for property $char.
     * @access public
     * @return String
     */
    public function getChar()
    {
        return $this->char;
    }

    /**
     * The setChar method, setter method for property $char.
     * @param String  $char
     * @access public
     * @return void
     */
    public function setChar($char)
    {
        $this->char = $char;
        $this->setAttributes("Char");
    }

    /**
     * The fill method, fill in this table row with table cells.
     * @param Array|ArrayObject  $cells
     * @param int  $index
     * @access public
     * @return void
     */
    public function fill($cells, $index = -1)
    {
        if ($index != -1) {
            $this->currentIndex = $index;
        } elseif (!is_array($cells) and !is_object($cells)) {
            throw new GUIException("Cannot fill table cells into this table row.");
        }

        foreach ($cells as $cell) {
            if ($cell instanceof TCell) {
                $this->add($cell, $index);
            } else {
                $this->add(new TCell($cell), $index);
            }
            if ($index != -1) {
                $index++;
            }
        }
    }

    /**
     * Magic method __toString for TRow class, it reveals that the class is a Table Row.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is The Table Row class.";
    }
}
