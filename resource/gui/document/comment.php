<?php

namespace Resource\GUI\Document;

use ArrayObject;
use Resource\GUI\Component\Accessory;
use Resource\GUI\GUIException;
use Resource\GUI\Renderer\DocumentRenderer;

/**
 * The Comment Class, extends from abstract GUI Accessory class.
 * It defines a standard text/comment in HTML.
 * @category Resource
 * @package GUI
 * @subpackage Document
 * @author Hall of Famer
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 *
 */
class Comment extends Accessory
{
    /**
     * The text property, specifies the textual content of this comment.
     * @access protected
     * @var String
     */
    protected $text;

    /**
     * The heading property, specifies the heading of this comment.
     * @access protected
     * @var Int
     */
    protected $heading;

    /**
     * The styles property, contains a list of styles defined for this comment.
     * @access protected
     * @var ArrayObject
     */
    protected $styles;

    /**
     * Constructor of Comment Class, assigns basic comment property.
     * @param String $text
     * @param String|ArrayObject $styles
     * @access public
     * @return Void
     */
    public function __construct($text = "", $linebreak = true, $styles = "", $heading = "")
    {
        $this->styles = new ArrayObject();
        if (!empty($text)) {
            $this->text = $text;
        }
        if (!empty($styles)) {
            $this->setStyles($styles);
        }
        if ($linebreak == false) {
            $this->setLineBreak(false);
        }
        if (is_numeric($heading)) {
            $this->setHeading($heading);
        }
        $this->renderer = new DocumentRenderer($this);
    }

    /**
     * The getText method, getter method for property $text.
     * @access public
     * @return String
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * The setText method, setter method for property $text.
     * @param String $text
     * @access public
     * @return Void
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * The getHeading method, getter method for property $heading.
     * @access public
     * @return Int
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * The setHeading method, setter method for property $heading.
     * @param Int $heading
     * @access public
     * @return Void
     */
    public function setHeading($heading)
    {
        if ($heading > 6 or $heading < 1) {
            throw new GUIException("The heading is not a valid number.");
        }
        $this->heading = $heading;
    }

    /**
     * The setHeading method, unset property $heading.
     * @access public
     * @return Void
     */
    public function unsetHeading()
    {
        $this->heading = "";
    }

    /**
     * The getStyles method, getter method for property $styles.
     * @access public
     * @return ArrayObject
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * The setStyles method, setter method for property $styles.
     * @param String|ArrayObject $styles
     * @access public
     * @return Void
     */
    public function setStyles($styles)
    {
        if ($styles instanceof ArrayObject) {
            foreach ($styles as $style) {
                $this->styles->offsetSet($style, true);
            }
        } else {
            $this->styles->offsetSet($styles, true);
        }
    }

    /**
     * The unsetStyles method, unset all styles in one line.
     * @access public
     * @return Void
     */
    public function unsetStyles()
    {
        $this->styles = new ArrayObject();
    }

    /**
     * The setBold method, set the comment to be bold.
     * @access public
     * @return Void
     */
    public function setBold()
    {
        $this->styles->offsetSet("b", true);
    }

    /**
     * The unsetBold method, unset the comment to be bold.
     * @access public
     * @return Void
     */
    public function unsetBold()
    {
        $this->styles->offsetUnset("b");
    }

    /**
     * The setItalic method, set the comment to be italic.
     * @access public
     * @return Void
     */
    public function setItalic()
    {
        $this->styles->offsetSet("i", true);
    }

    /**
     * The unsetItalic method, unset the comment to be italic.
     * @access public
     * @return Void
     */
    public function unsetItalic()
    {
        $this->styles->offsetUnset("i");
    }

    /**
     * The setUnderlined method, set the comment to be underlined.
     * @access public
     * @return Void
     */
    public function setUnderlined()
    {
        $this->styles->offsetSet("u", true);
    }

    /**
     * The unsetUnderlined method, unset the comment to be underlined.
     * @access public
     * @return Void
     */
    public function unsetUnderlined()
    {
        $this->styles->offsetUnset("u");
    }

    /**
     * The setCentered method, set the comment to be centered.
     * @access public
     * @return Void
     */
    public function setCentered()
    {
        $this->styles->offsetSet("center", true);
    }

    /**
     * The unsetCentered method, unset the comment to be centered.
     * @access public
     * @return Void
     */
    public function unsetCentered()
    {
        $this->styles->offsetUnset("center");
    }

    /**
     * The setDeleted method, set the comment to be deleted.
     * @access public
     * @return Void
     */
    public function setDeleted()
    {
        $this->styles->offsetSet("del", true);
    }

    /**
     * The unsetDeleted method, unset the comment to be deleted.
     * @access public
     * @return Void
     */
    public function unsetDeleted()
    {
        $this->styles->offsetUnset("del");
    }

    /**
     * The setInserted method, set the comment to be inserted.
     * @access public
     * @return Void
     */
    public function setInserted()
    {
        $this->styles->offsetSet("ins", true);
    }

    /**
     * The unsetInserted method, unset the comment to be inserted.
     * @access public
     * @return Void
     */
    public function unsetInserted()
    {
        $this->styles->offsetUnset("ins");
    }

    /**
     * The setQuoted method, set or unset the comment to be quoted.
     * @param bool $quoted
     * @access public
     * @return Void
     */
    public function setQuoted($quoted = true)
    {
        $this->styles->offsetSet("q", $quoted);
    }

    /**
     * The unsetQuoted method, unset the comment to be quoted.
     * @access public
     * @return Void
     */
    public function unsetQuoted()
    {
        $this->styles->offsetUnset("q");
    }

    /**
     * The setListed method, set the comment to be listed.
     * @param bool $listed
     * @access public
     * @return Void
     */
    public function setListed($listed = true)
    {
        $this->styles->offsetSet("li", $listed);
    }

    /**
     * The unsetListed method, unset the comment to be listed.
     * @access public
     * @return Void
     */
    public function unsetListed()
    {
        $this->styles->offsetUnset("li");
    }

    /**
     * The setSubscript method, set the comment to be a subscript.
     * @param bool $subscript
     * @access public
     * @return Void
     */
    public function setSubscript($subscript = true)
    {
        $this->styles->offsetSet("sub", $subscript);
    }

    /**
     * The unsetSubscript method, unset the comment to be a subscript.
     * @access public
     * @return Void
     */
    public function unsetSubscript()
    {
        $this->styles->offsetUnset("sub");
    }

    /**
     * The setSuperscript method, set the comment to be a superscript.
     * @param bool $superscript
     * @access public
     * @return Void
     */
    public function setSuperscript($superscript = true)
    {
        $this->styles->offsetSet("sup", $superscript);
    }

    /**
     * The unsetSuperscript method, unset the comment to be a superscript.
     * @access public
     * @return Void
     */
    public function unsetSuperscript()
    {
        $this->styles->offsetUnset("sup");
    }

    /**
     * The render method for Comment class, it renders comment data field into html readable format.
     * @access public
     * @return Void
     */
    public function render()
    {
        if ($this->renderer->getStatus() == "ready") {
            $this->renderer->renderComment();
        }
        return $this->renderer->getRender();
    }

    /**
     * Magic method __toString for Comment class, it reveals that the object is a GUI Comment/Text.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia Comment class.";
    }
}
