<?php

namespace Resource\GUI\Container;

use Resource\Collection\Collective;
use Resource\Collection\Listable;
use Resource\Collection\Mappable;
use Resource\GUI\Component\Option;
use Resource\GUI\Container;
use Resource\GUI\GUIException;
use Resource\GUI\Renderer\ListRenderer;

/**
 * The DropdownList Class, extends from abstract Container class.
 * It specifies a standard single-item dropdown list.
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
class DropdownList extends Container
{

    /**
     * The autofocus property, checks if the input component is autofocused.
     * @access protected
     * @var bool
     */
    protected $autofocus = false;

    /**
     * The disabled property, checks if the input component is disabled.
     * @access protected
     * @var bool
     */
    protected $disabled = false;

    /**
     * Constructor of DropdownList Class, which assigns basic property to this list
     * @param String $name
     * @param Array|ArrayObject $components
     * @param String $identity
     * @param String $event
     * @access public
     * @return Void
     */
    public function __construct($name = "", $components = "", $identity = "", $event = "")
    {
        if (!empty($name)) {
            $this->setName($name);
            $this->setID($name);
        }
        if (!empty($identity)) $this->select($identity);
        if (!empty($event)) $this->setEvent($event);

        parent::__construct($components);
        $this->renderer = new ListRenderer($this);
    }

    /**
     * The isAutofocus method, getter method for property $autofocus.
     * @access public
     * @return bool
     */
    public function isAutofocus()
    {
        return $this->autofocus;
    }

    /**
     * The setAutofocus method, setter method for property $autofocus.
     * @param bool $autofocus
     * @access public
     * @return void
     */
    public function setAutofocus($autofocus = true)
    {
        $this->autofocus = $autofocus;
        $this->setAttributes("Autofocus");
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
     * The add method, sets an Option Object to a specific index.
     * @param Option|OptGroup $option
     * @param int $index
     * @access public
     * @return void
     */
    public function add($option, $index = -1)
    {
        if (!($option instanceof Option) and !($option instanceof OptGroup)) throw new GUIException("Cannot add a non-option type component to dropdown list.");
        parent::add($option, $index);
    }

    /**
     * The select method, determines which option in this list should be set selected.
     * @param String $identity
     * @access public
     * @return void
     */
    public function select($identity)
    {
        foreach ($this->components as $components) {
            if ($components->getValue() == $identity) $components->setSelected(true);
        }
    }

    /**
     * The fill method, fill in this dropdownlist with options from database starting at a given index.
     * To use it, you need PDO or MySQLi to fetch all rows with one or two properties to serve as collection list or map.
     * @param Collective $collection
     * @param String $identity
     * @param int $index
     * @access public
     * @return Void
     */
    public function fill(Collective $collection, $identity = "", $index = -1)
    {
        if ($index != -1) $this->currentIndex = $index;
        if ($collection instanceof Listable) $this->fillList($collection, $identity, $index);
        elseif ($collection instanceof Mappable) $this->fillMap($collection, $identity, $index);
        else throw new GUIException("Cannot fill option objects inside this dropdownlist");
    }

    /**
     * The fillList method, fill in this dropdownlist with elements from a LinkedList.
     * @param Listable $list
     * @param String $identity
     * @param Int $index
     * @access protected
     * @return void
     */
    protected function fillList(Listable $list, $identity = "", $index = -1)
    {
        $iterator = $list->iterator();
        while ($iterator->hasNext()) {
            $field = (string)$iterator->next();
            $option = new Option($field, $field);
            if ($option->getValue() == $identity) $option->setSelected(true);
            $this->add($option, $index);
            if ($index != -1) $index++;
        }
    }

    /**
     * The fillMap method, fill in this dropdownlist with entries from a LinkedHashMap.
     * @param Mappable $map
     * @param String $identity
     * @param int $index
     * @access protected
     * @return void
     */
    protected function fillMap(Mappable $map, $identity = "", $index = -1)
    {
        $iterator = $map->iterator();
        while ($iterator->hasNext()) {
            $field = $iterator->next();
            $option = new Option((string)$field->getKey(), (string)$field->getValue());
            if ($option->getValue() == $identity) $option->setSelected(true);
            $this->add($option, $index);
            if ($index != -1) $index++;
        }
    }

    /**
     * The fillReverse method, fill in this dropdownlist with entries from a LinkedHashMap reversely.
     * @param Mappable $map
     * @param String $identity
     * @param Int $index
     * @access protected
     * @return Void
     */
    public function fillReverse(Mappable $map, $identity = "", $index = -1)
    {
        $iterator = $map->iterator();
        while ($iterator->hasNext()) {
            $field = $iterator->next();
            $option = new Option((string)$field->getValue(), (string)$field->getKey());
            if ($option->getValue() == $identity) $option->setSelected(true);
            $this->add($option, $index);
            if ($index != -1) $index++;
        }
    }

    /**
     * Magic method __toString for DropdownList class, it reveals that the object is a dropdown list.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia DropDownList class.";
    }
}
