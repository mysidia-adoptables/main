<?php

namespace Resource\GUI;

use ArrayObject;
use Resource\GUI\Container;
use Resource\GUI\Element;

/**
 * The Abstract Component Class, extends from abstract GUI class.
 * It is parent to all Mysidia Component classes, but cannot be instantiated itself.
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
abstract class Component extends GUI implements Renderable
{
    /**
     * The name property, specifies the name of this component.
     * @access protected
     * @var String
     */
    protected $name;

    /**
     * The visible property, which determines if this GUI is visible.
     * @access protected
     * @var bool
     */
    protected $visible = true;

    /**
     * The align property, which specifies the align setting.
     * @access protected
     * @var Align
     */
    protected $align;

    /**
     * The font property, which specifies the font setting.
     * @access protected
     * @var Font
     */
    protected $font;

    /**
     * The foreground property, which stores the foreground color setting.
     * @access protected
     * @var Color
     */
    protected $foreground;

    /**
     * The background property, which stores the background color or image setting.
     * @access protected
     * @var Color|Image
     */
    protected $background;

    /**
     * The css property, which defines what inline styles have been set.
     * @access protected
     * @var ArrayObject
     */
    protected $css;

    /**
     * The container property, which stores a reference to the container that encloses this component.
     * @access protected
     * @var GUIContainer
     */
    protected $container;

    /**
     * The lineBreak property, which defines if a linebreak is automatically inserted between each component.
     * @access protected
     * @var bool
     */
    protected $lineBreak = true;

    /**
     * The getName method, getter method for property $name.
     * @access public
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * The setName method, setter method for property $name.
     * @param String $name
     * @access public
     * @return Void
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->setAttributes("Name");
    }

    /**
     * The isVisible method, getter method for property $visible.
     * @access public
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * The setVisible method, setter method for property $visible.
     * @param bool $visible
     * @access public
     * @return Void
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        $this->setAttributes("Visible");
    }

    /**
     * The getAlign method, getter method for property $align.
     * @access public
     * @return Align
     */
    public function getAlign()
    {
        return $this->align;
    }

    /**
     * The setAlign method, setter method for property $align.
     * @param Align $align
     * @access public
     * @return Void
     */
    public function setAlign(Element\Align $align)
    {
        $this->align = $align;
        $this->setCSS("Align");
    }

    /**
     * The getFont method, getter method for property $font.
     * @access public
     * @return Font
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * The setFont method, setter method for property $font.
     * @param Font $font
     * @access public
     * @return Void
     */
    public function setFont(Element\Font $font)
    {
        $this->font = $font;
        $this->setCSS("Font");
    }

    /**
     * The getForeground method, getter method for property $foreground.
     * @access public
     * @return Color
     */
    public function getForeground()
    {
        return $this->foreground;
    }

    /**
     * The setForeground method, setter method for property $foreground.
     * @param Color $foreground
     * @access public
     * @return Void
     */
    public function setForeground(Element\Color $foreground)
    {
        $this->foreground = $foreground;
        $this->setCSS("Foreground");
    }

    /**
     * The getBackground method, getter method for property $background.
     * @access public
     * @return Color
     */
    public function getBackground()
    {
        return $this->background;
    }

    /**
     * The setBackground method, setter method for property $background.
     * @param Color|Image $background
     * @access public
     * @return Void
     */
    public function setBackground($background)
    {
        $this->background = new Element\Background($background);
        $this->setCSS("Background");
    }

    /**
     * The getCSS method, getter method for property $css.
     * @access public
     * @return ArrayObject
     */
    public function getCSS()
    {
        return $this->css;
    }

    /**
     * The setCss method, setter method for property $css.
     * This method is reserved for GUIComponent to use itself.
     * @param String $css
     * @access public
     * @return ArrayObject
     */
    protected function setCSS($css)
    {
        if (!$this->css) {
            $this->css = new ArrayObject();
        }
        $this->css->offsetSet($css, true);
    }

    /**
     * The getContainer method, shows which container this GUI Object belongs to.
     * @access public
     * @return GUIContainer
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * The setContainer method, set the value for container property.
     * This method should NOT be invoked directly in client code.
     * @access public
     * @return Void
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * The isLineBreak method, getter method for property $likeBreak.
     * @access public
     * @return bool
     */
    public function isLineBreak()
    {
        return $this->lineBreak;
    }

    /**
     * The setLineBreak method, setter method for property $lineBreak.
     * @param bool $lineBreak
     * @access public
     * @return Void
     */
    public function setLineBreak($lineBreak)
    {
        $this->lineBreak = $lineBreak;
    }

    /**
     * The getForm method, returns the form that contains this component or boolean false.
     * It searches through the composite hierarchy, until it reaches the top or find a form.
     * @access public
     * @return bool|Form
     */
    public function getForm()
    {
        $container = $this->container;
        while ($container) {
            if ($container instanceof Form) {
                return $container;
            } else {
                $container = $container->container;
            }
        }
        return false;
    }

    /**
     * The getTable method, returns the table that contains this component or boolean false.
     * It searches through the composite hierarchy, until it reaches the top or find a table.
     * @access public
     * @return bool|Form
     */
    public function getTable()
    {
        $container = $this->container;
        while ($container) {
            if ($container instanceof Table) {
                return $container;
            } else {
                $container = $container->container;
            }
        }
        return false;
    }

    /**
     * The render method for GUIComponent class, it renders accessory field data into HTML readable format.
     * This is an incomplete implementation, must be handled further by child classes.
     * @access public
     * @return GUIRenderer
     */
    public function render()
    {
        if ($this->css instanceof ArrayObject) {
            $this->renderer->renderCSS();
        }

        if ($this->attributes instanceof ArrayObject) {
            foreach ($this->attributes as $attribute => $status) {
                $renderMethod = "render{$attribute}";
                $this->renderer->$renderMethod();
            }
        }
        return $this->renderer;
    }

    /**
     * Magic method __toString for GUIComponent class, it reveals that the class is a GUIComponent.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is The GUIComponent Class.";
    }
}
