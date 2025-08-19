<?php

namespace Resource\GUI;

use ArrayObject;
use Resource\GUI\Container;
use Resource\GUI\Document\Comment;
use Resource\GUI\GUIException;
use Resource\GUI\Renderer\DocumentRenderer;

/**
 * The Document Class, extends from abstract GUI Container class.
 * Document is a top level container right now, it is where other GUI Components and containers are added.
 * In future when we allow for multiple frames, document will become second level container.
 * @category Resource
 * @package GUI
 * @author Hall of Famer
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 * @final
 *
 */
final class Document extends Container
{
    /**
     * The title property, holds the information of the document title.
     * @access protected
     * @var String
     */
    protected $title;

    /**
     * The content property, stores a collection of rendered html elements.
     * @access protected
     * @var String
     */
    protected $content;

    /**
     * Constructor of Document Class, it calls parent constructor and retrieves all links/images information.
     * @param String $title
     * @param ArrayObject $components
     * @access public
     * @return Void
     */
    public function __construct($title = "", $components = "")
    {
        parent::__construct($components);
        $this->renderer = new DocumentRenderer($this);
    }

    /**
     * The getTitle method, obtain the title of this document.
     * @access public
     * @return String
     */
    public function getTitle()
    {
        if (!$this->title) {
            throw new GUIException("This document has no title yet.");
        }
        return $this->title;
    }

    /**
     * The setTitle method, set the title of this document.
     * @param String $title
     * @access public
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * The getContent method, obtain the content of this document.
     * @access public
     * @return String
     */
    public function getContent()
    {
        if (!$this->content) {
            $this->content = $this->render();
        }
        return $this->content;
    }

    /**
     * The addLangvar method, append a language variable into the document.
     * @access public
     * @return Void
     */
    public function addLangvar($langvar, $lineBreak = false)
    {
        $this->add(new Comment($langvar, $lineBreak));
    }

    /**
     * Magic method __toString for Document class, it reveals that the class is a document.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is a Document object.";
    }
}
