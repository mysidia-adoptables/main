<?php

namespace Resource\GUI\Renderer;

use Resource\GUI\Container;
use Resource\GUI\Renderer;

/**
 * The ListRenderer Class, extends from abstract GUI Renderer class.
 * It is responsible for rendering of GuiList type containers.
 * @category Resource
 * @package GUI
 * @subpackage Renderer
 * @author Hall of Famer
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 *
 */

class ListRenderer extends Renderer
{
    /**
     * Constructor of ListRenderer Class, assigns the component reference.
     * @param Container  $component
     * @access public
     * @return Void
     */
    public function __construct(Container $component)
    {
        parent::__construct($component);
        if ($component instanceof Container\LinksList) {
            $this->tag = $component->getType();
        } elseif ($component instanceof Container\FieldSet) {
            $this->tag = "fieldset";
        } elseif ($component instanceof Container\DataList) {
            $this->tag = "datalist";
        } elseif ($component instanceof Container\DropdownList) {
            $this->tag = "select";
        } else {
            $this->tag = "";
        }
    }

    /**
     * The renderList method, helps with the rendering process of a DataList Object.
     * @access public
     * @return ListRenderer
     */
    public function renderList()
    {
        if ($this->component->getID()) {
            $this->setRender(" list='{$this->component->getID()}'");
        }
        return $this;
    }

    /**
     * The renderLabel method, helps with the rendering process of an OptGroup Object.
     * @access public
     * @return ListRenderer
     */
    public function renderLabel()
    {
        $this->setRender(" label='{$this->component->getLabel()}'");
        return $this;
    }

    /**
     * The renderSize method, renders the size property of a SelectList Object.
     * @access public
     * @return ListRenderer
     */
    public function renderSize()
    {
        $this->setRender(" size='{$this->component->getSize()}'");
        return $this;
    }

    /**
     * The renderMultiple method, renders the multiple property of a SelectList Object.
     * @access public
     * @return ListRenderer
     */
    public function renderMultiple()
    {
        $this->setRender(" multiple='multiple'");
        return $this;
    }

    /**
     * The start method for ListRenderer class, initialize the rendering process.
     * In most circumstances it just calls parent overridden method, but for datalist it is complex.
     * @access public
     * @return ListRenderer
     */
    public function start()
    {
        if ($this->tag == "datalist") {
            $this->setRender("\n<input");
            $this->renderList()->renderName();
            $this->pause();
        }
        parent::start();
        return $this;
    }
}
