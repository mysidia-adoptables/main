<?php

namespace Resource\GUI\Component;

use Resource\GUI\Resizable;
use Resource\Utility\URL;

/**
 * The Image Class, extends from abstract GUIAccessory class.
 * It defines a standard image element to be used in HTML.
 * @category Resource
 * @package GUI
 * @subpackage Component
 * @author Hall of Famer
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 *
 */
class Image extends Accessory implements Resizable
{
    /**
     * The alt property, defines the alt text for image object.
     * @access protected
     * @var String
     */
    protected $alt;

    /**
     * The src property, stores the src of this image object.
     * @access protected
     * @var Link
     */
    protected $src;

    /**
     * The width property, specifies the width of this image.
     * @access protected
     * @var int
     */
    protected $width;

    /**
     * The height property, specifies the height for this image.
     * @access protected
     * @var int
     */
    protected $height;

    /**
     * The action property, it holds information for javascript actions.
     * @access protected
     * @var String
     */
    protected $action;

    /**
     * The type property, determines the image type as background or else.
     * @access protected
     * @var String
     */
    protected $type;

    /**
     * Constructor of Image Class, which assigns basic image properties.
     * @param String $src
     * @param String $alt
     * @param int $dimension
     * @param String $event
     * @access public
     * @return void
     */
    public function __construct($src = "", $alt = "", $dimension = "", $event = "")
    {
        parent::__construct($alt);
        $this->setSrc(($src instanceof URL) ? $src : new URL($src));
        if (!empty($alt)) {
            $this->setAlt($alt);
        }
        if (is_numeric($dimension)) {
            $this->setWidth($dimension);
            $this->setHeight($dimension);
        }
        if (!empty($event)) {
            $this->setEvent($event);
        }
    }

    /**
     * The getAlt method, getter method for property $alt.
     * @access public
     * @return String
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * The setAlt method, setter method for property $alt.
     * @param String $alt
     * @access public
     * @return void
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
        $this->setAttributes("Alt");
    }

    /**
     * The getSrc method, getter method for property $src.
     * @access public
     * @return URL
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * The setSrc method, setter method for property $src.
     * @param URL $src
     * @access public
     * @return void
     */
    public function setSrc(URL $src)
    {
        $this->src = $src;
        $this->setAttributes("Src");
    }

    /**
     * The getWidth method, getter method for property $width.
     * @access public
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * The setWidth method, setter method for property $width.
     * @param int $width
     * @access public
     * @return void
     */
    public function setWidth($width = 40)
    {
        $this->width = $width;
        $this->setAttributes("Width");
    }


    /**
     * The getHeight method, getter method for property $height.
     * @access public
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * The setHeight method, setter method for property $height.
     * @param int $height
     * @access public
     * @return void
     */
    public function setHeight($height = 40)
    {
        $this->height = $height;
        $this->setAttributes("Height");
    }

    /**
     * The setDimension method, getter method for property $width.
     * @param int $width
     * @param int $height
     * @access public
     * @return void
     */
    public function setDimension($width, $height = null)
    {
        if (!$height) {
            $height = $width;
        }
        $this->setWidth($width);
        $this->setHeight($height);
    }

    /**
     * The resize method, resizes the width and height simultaneous while keeping aspect ratio.
     * @param Int $dimension
     * @param bool $percent
     * @access public
     * @return void
     */
    public function resize($dimension, $percent = false)
    {
        if ($percent) {
            $this->width *= $dimension;
            $this->height *= $dimension;
        } else {
            $this->width = $dimension;
            $this->height = $dimension;
        }
        $this->setAttributes("Width");
        $this->setAttributes("Height");
    }

    /**
     * The getAction method, getter method for property $action.
     * @access public
     * @return String
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * The setAction method, setter method for property $action.
     * @param String $action
     * @access public
     * @return void
     */
    public function setAction($action)
    {
        $this->action = $action;
        $this->setAttributes("Action");
    }

    /**
     * The getType method, getter method for property $type.
     * @access public
     * @return String
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * The setType method, setter method for property $type.
     * @param String $type
     * @access public
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * The render method for Image class, it renders image data fields into HTML readable format.
     * @access public
     * @return String
     */
    public function render()
    {
        if ($this->renderer->getStatus() == "ready") {
            if ($this->type == "Background") {
                $this->renderer->renderBackground();
            } else {
                $this->renderer->start();
                parent::render()->pause();
            }
        }
        return $this->renderer->getRender();
    }

    /**
     * Magic method __toString for Image class, it reveals that the object is an image.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia Image class.";
    }
}
