<?php

namespace Resource\GUI\Renderer;

use Resource\GUI\Component\Button;
use Resource\GUI\Component\ButtonComponent;
use Resource\GUI\Component\Image;
use Resource\GUI\Renderer;

/**
 * The ButtonRenderer Class, extends from abstract GUI Renderer class.
 * It is responsible for rendering button type GUI Components.
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

class ButtonRenderer extends Renderer
{
    /**
     * Constructor of ButtonRenderer Class, assigns the button reference and determines its tag.
     * @param ButtonComponent  $button
     * @access public
     * @return Void
     */
    public function __construct(ButtonComponent $button)
    {
        parent::__construct($button);
        if ($button instanceof Button) {
            $this->tag = "button";
        } else {
            $this->tag = "input";
        }
    }

    /**
     * The renderImage method, renders the image of an Button Object.
     * @access public
     * @return ButtonRenderer
     */
    public function renderImage()
    {
        $image = $this->component->getImage();
        if ($image instanceof Image) {
            $this->setRender($image->render());
        }
        return $this;
    }

    /**
     * The renderValue method, renders the value of a Button Object.
     * @access public
     * @return ButtonRenderer
     */
    public function renderValue()
    {
        $this->setRender(" value='{$this->component->getValue()}'");
        return $this;
    }

    /**
     * The renderChecked method, renders the checked property of a RadioButton or CheckBox Object.
     * @access public
     * @return ButtonRenderer
     */
    public function renderChecked()
    {
        $this->setRender(" checked='checked'");
        return $this;
    }
}
