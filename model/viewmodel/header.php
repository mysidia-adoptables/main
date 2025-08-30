<?php

namespace Model\ViewModel;

use ArrayObject;
use Resource\Core\Registry;

/**
 * The Header Class, defines a standard HTML header component.
 * It extends from the WidgetViewModel class, while adding its own implementation.
 * @category Model
 * @package ViewModel
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not much at this point.
 *
 */

class Header extends WidgetViewModel
{
    /**
     * The browserTitle property, it stores the information of browser title.
     * @access protected
     * @var String
     */
    protected $browserTitle;

    /**
     * The favicon property, it specifies the favicon of the site.
     * @access protected
     * @var String
     */
    protected $favicon;

    /**
     * The styles property, it holds information of the stylesheets attached into the header.
     * @access protected
     * @var ArrayList
     */
    protected $styles;

    /**
     * The additionalStyle property, it specifies if additional style is defined.
     * @access protected
     * @var String
     */
    protected $additionalStyle;

    /**
     * The scripts property, it defines a list of javascript files loaded on the header.
     * @access protected
     * @var ArrayObject
     */
    protected $scripts;

    /**
     * The additionalScript property, it specifies if additional script is defined.
     * @access protected
     * @var String
     */
    protected $additionalScript;


    /**
     * Constructor of Header Class, it initializes basic header properties
     * @access public
     * @return void
     */
    public function __construct()
    {
        $mysidia = Registry::get("mysidia");
        $this->browserTitle = $mysidia->settings->browsertitle;
        $this->styles = new ArrayObject();
        $this->scripts = new ArrayObject();
    }

    /**
     * The getBrowserTitle method, getter method for property $browserTitle.
     * @access public
     * @return String
     */
    public function getBrowserTitle()
    {
        return $this->browserTitle;
    }

    /**
     * The getFavicon method, getter method for property $favicon.
     * @access public
     * @return String
     */
    public function getFavicon()
    {
        return $this->favicon;
    }

    /**
     * The loadFavicon method, loads the favicon into the html header.
     * @param String  $icon
     * @access public
     * @return String
     */
    public function loadFavicon($icon)
    {
        $this->favicon = $icon;
        return "<link rel='shortcut icon' href='{$icon}' type='image/x-icon'/>\n";
    }

    /**
     * The getStyles method, getter method for property $styles.
     * @access public
     * @return ArrayList
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * The addStyle method, appends a stylesheet to the css list.
     * @param String  $style
     * @access public
     * @return void
     */
    public function addStyle($style)
    {
        $this->styles[] = $style;
    }

    /**
     * The loadStyle method, loads a stylesheet into the html header.
     * @param String  $style
     * @access public
     * @return String
     */
    public function loadStyle($style)
    {
        return "<link rel='stylesheet' href='{$style}' type='text/css'/>\n";
    }

    /**
     * The loadAdditionalStyle method, loads an additional css stylesheet into the html header.
     * @access public
     * @return String
     */
    public function loadAdditionalStyle()
    {
        if ($this->additionalStyle) {
            $this->addStyle($this->additionalStyle);
            return "<link rel='stylesheet' href='{$this->additionalStyle}' type='text/css'/>\n";
        }
    }

    public function loadStyles()
    {
        $render = "";
        if ($this->styles) {
            foreach ($this->styles as $style) {
                $render .= $this->loadStyle($style);
            }
        }
        return $render;
    }

    /**
     * The setAdditionalStyle method, sets the file path for the additional css to be loaded.
     * @param String  $style
     * @access public
     * @return void
     */
    public function setAdditionalStyle($style)
    {
        $this->additionalStyle = $style;
    }

    /**
     * The getScripts method, getter method for property $scripts.
     * @access public
     * @return ArrayList
     */
    public function getScripts()
    {
        return $this->scripts;
    }

    /**
     * The addScript method, append a javascript file to the script list.
     * @param String  $script
     * @access public
     * @return void
     */
    public function addScript($script)
    {
        $this->scripts[] = $script;
    }

    /**
     * The loadScript method, loads a javascript file into the html header.
     * @param String  $script
     * @access public
     * @return String
     */
    public function loadScript($script)
    {
        return "<script type='text/javascript' src='{$script}'></script>\n";
    }

    /**
     * The loadAdditionalScript method, loads an additional javascript file into the html header.
     * @access public
     * @return String
     */
    public function loadAdditionalScript()
    {
        if ($this->additionalScript) {
            $this->addScript($this->additionalScript);
            return "<script type='text/javascript' src='{$this->additionalScript}'></script>\n";
        }
    }

    public function loadScripts()
    {
        $render = "";
        if ($this->scripts) {
            foreach ($this->scripts as $script) {
                $render .= $this->loadScript($script);
            }
        }
        return $render;
    }

    /**
     * The setAdditionalScript method, sets the file path for the additional javascript to be loaded.
     * @param String  $script
     * @access public
     * @return void
     */
    public function setAdditionalScript($script)
    {
        $this->additionalScript = $script;
    }

    /**
     * The render method, will render any custom styles and scripts to the header.
     * @access public
     * @return Header
     */
    public function render()
    {
        return $this;
    }
}
