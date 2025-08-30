<?php

namespace Resource\GUI\Container;

use ArrayObject;
use Resource\GUI\GUIException;

/**
 * The Table Class, extends from abstract TableContainer class.
 * It is a flexible PHP table class, can perform a series of operations.
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

class Table extends TableContainer
{
    /**
     * The bordered property, defines if the table comes with a border
     * @access protected
     * @var Boolean
    */
    protected $bordered = true;

    /**
     * The collapsed property, it specifies whether or not table borders should be collapsed.
     * @access protected
     * @var Boolean
    */
    protected $collapsed = false;

    /**
     * The spacing property, determines the distance between the borders of adjacent cells.
     * @access protected
     * @var String
    */
    protected $spacing;

    /**
     * The caption property, stores the placement of a table caption.
     * @access protected
     * @var String
    */
    protected $caption;

    /**
     * The empty property, it specifies whether or not to display borders and background on empty cells in a table
     * @access protected
     * @var String
    */
    protected $empty;

    /**
     * The layout property, sets the layout algorithm to be fixed or auto.
     * @access protected
     * @var Boolean
    */
    protected $fixed;

    /**
     * Constructor of Table Class, sets up basic Table properties and calls parent constructor.
     * @param String  $name
     * @param String  $width
     * @param Boolean  $bordered
     * @param String  $event
     * @param ArrayObject  $components
     * @access public
     * @return void
     */
    public function __construct($name = "", $width = "", $bordered = true, $event = "", $components = "")
    {
        parent::__construct($name, $width, $event, $components);
        if ($bordered) {
            $this->setBordered(true);
        }
    }

    /**
     * The isBordered method, getter method for property $bordered.
     * @access public
     * @return Boolean
     */
    public function isBordered()
    {
        return $this->bordered;
    }

    /**
     * The setBordered method, setter method for property $bordered.
     * @param Boolean  $bordered
     * @access public
     * @return void
     */
    public function setBordered($bordered)
    {
        $this->bordered = $bordered;
        $this->setAttributes("Bordered");
    }

    /**
     * The isCollapsed method, getter method for property $collapsed.
     * @access public
     * @return Boolean
     */
    public function isCollapsed()
    {
        return $this->collapsed;
    }

    /**
     * The setCollapsed method, setter method for property $collapsed.
     * @param Boolean  $collapsed
     * @access public
     * @return void
     */
    public function setCollapsed($collapsed)
    {
        $this->collapsed = $collapsed;
        $this->setTableAttributes("Collapsed");
    }

    /**
     * The getSpacing method, getter method for property $spacing.
     * @access public
     * @return String
     */
    public function getSpacing()
    {
        return $this->spacing;
    }

    /**
     * The setSpacing method, setter method for property $spacing.
     * @param int|String  $spacing
     * @access public
     * @return void
     */
    public function setSpacing($spacing)
    {
        if (is_numeric($spacing)) {
            $this->spacing = "{$spacing}px";
        } else {
            $this->spacing = $spacing;
        }
        $this->setTableAttributes("Spacing");
    }

    /**
     * The getCaption method, getter method for property $caption.
     * @access public
     * @return String
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * The setCaption method, setter method for property $caption.
     * @param String  $caption
     * @access public
     * @return void
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
        $this->setTableAttributes("Caption");
    }

    /**
     * The getEmpty method, getter method for property $empty.
     * @access public
     * @return String
     */
    public function getEmpty()
    {
        return $this->empty;
    }

    /**
     * The setEmpty method, setter method for property $empty.
     * @param String  $empty
     * @access public
     * @return void
     */
    public function setEmpty($empty)
    {
        $this->empty = $empty;
        $this->setTableAttributes("Empty");
    }

    /**
     * The isFixed method, getter method for property $fixed.
     * @access public
     * @return Boolean
     */
    public function isFixed()
    {
        return $this->fixed;
    }

    /**
     * The setFixed method, setter method for property $fixed.
     * @param Boolean  $fixed
     * @access public
     * @return void
     */
    public function setFixed($fixed)
    {
        $this->fixed = $fixed;
        $this->setTableAttributes("Fixed");
    }

    /**
     * The fill method, fill in this table with table rows and cells.
     * @param Array  $rows
     * @param Array  $cells
     * @param int  $index
     * @access public
     * @return void
     */
    public function fill($rows, $cells = "", $index = -1)
    {
        if ($index != -1) {
            $this->currentIndex = $index;
        } elseif (!is_array($rows)) {
            throw new GUIException("Cannot fill table rows/cells into this table.");
        }

        for ($i = 0; $i < count($rows); $i++) {
            if (!($rows[$i] instanceof TRow)) {
                throw new GUIException("The supplied row is not an instance of TableRow.");
            }
            if ($cells) {
                $rows[$i]->fill($cells[$i]);
            }
            $this->add($rows[$i], $index);
            if ($index != -1) {
                $index++;
            }
        }
    }

    /**
     * Magic method __toString for Table class, it reveals that the class is a Table.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is The Table class.";
    }
}
