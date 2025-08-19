<?php

namespace Resource\GUI\Document;

use ArrayObject;
use Resource\GUI\Component;
use Resource\GUI\Container;
use Resource\GUI\Renderer\DocumentRenderer;

/**
 * The Paragraph Class, extends from abstract GUI Container class.
 * It defines a paragraph type container with <p> tag, can be easily styled.
 * A paragraph can be viewed as a well formatted collection of GUI texts.
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
class Paragraph extends Container
{

    /**
     * The comments property, it is useful if the paragraph contains comments type GUI Component.
     * @access protected
     * @var ArrayObject
     */
    protected $comments;

    /**
     * Constructor of Paragraph Class, sets up basic paragraph properties.
     * The parameter $component can be a collection of components, a comment type GUI Component, or a simple string.
     * @param Comment|ArrayObject $components
     * @param String $name
     * @param String $event
     * @access public
     * @return void
     */
    public function __construct($components = "", $name = "", $event = "")
    {
        parent::__construct($components);
        if (!empty($name)) {
            $this->setName($name);
            $this->setID($name);
        }

        $this->comments = new ArrayObject;
        if ($components instanceof Comment) $this->comments->append($components);
        if (!empty($event)) $this->setEvent($event);
        $this->lineBreak = false;
        $this->renderer = new DocumentRenderer($this);
    }

    /**
     * The getComments method, getter method for property $comments.
     * @access public
     * @return ArrayObject
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * The setComments method, setter method for property $comments.
     * @param Comment $comment
     * @access public
     * @return void
     */
    public function setComments(Comment $comment)
    {
        $this->comments->append($comment);
    }

    /**
     * The add method, append a GUI Component to this paragraph.
     * @param Component $component
     * @param int $index
     * @access public
     * @return void
     */
    public function add(Component $component, $index = -1)
    {
        parent::add($component, $index);
        if ($component instanceof Comment) $this->comments->append($component);
    }

    /**
     * Magic method __toString for Paragraph class, it reveals that the class is a Paragraph.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is The Paragraph class.";
    }
}
