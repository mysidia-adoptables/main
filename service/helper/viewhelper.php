<?php

namespace Service\Helper;

use Resource\Core\File;
use Resource\Core\Registry;
use Resource\Core\View;

/**
 * The ViewHelper Class, extends from abstract helper class.
 * It defines a standard helper for View Object, it can be extended by child classes to provide extra functionality.
 * @category Service
 * @package Helper
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not much at this point.
 *
 */

class ViewHelper extends Helper
{
    /**
     * The view property, it stores a reference to the calling View Object.
     * @access protected
     * @var View
    */
    protected $view;

    /**
     * Constructor of ViewHelper Class, it initializes basic ViewHelper properties.
     * @param View  $view
     * @access public
     * @return void
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * The getCSS method, loads an additional css stylesheet if the file exists.
     * @access public
     * @return File
     */
    public function getCSS()
    {
        $mysidia = Registry::get("mysidia");
        $file = "{$mysidia->path->getRoot()}css/{$this->view->getController()}.css";
        $css = new File($file);
        if ($css->isFile()) {
            $style = "{$mysidia->path->getTempRoot()}css/{$this->view->getController()}.css";
            $mysidia->frame->getHeader()->setAdditionalStyle($style);
            return $css;
        }
        return null;
    }

    /**
     * The getDocument method, obtains the document object stored in the registry.
     * @access public
     * @return Document
     */
    public function getDocument()
    {
        $document = $this->getFrame()->getDocument();
        return $document;
    }

    /**
     * The getFrame method, obtains the frame object stored in the registry.
     * @access public
     * @return Frame
     */
    public function getFrame()
    {
        $frame = Registry::get("frame");
        return $frame;
    }

    /**
     * The getJS method, loads an additional javascript file if the file exists.
     * @access public
     * @return File
     */
    public function getJS()
    {
        $mysidia = Registry::get("mysidia");
        $file = "{$mysidia->path->getRoot()}js/{$this->view->getController()}.js";
        $js = new File($file);
        if ($js->isFile()) {
            $script = "{$mysidia->path->getTempRoot()}js/{$this->view->getController()}.js";
            $mysidia->frame->getHeader()->setAdditionalScript($script);
            return $js;
        }
        return null;
    }

    /**
     * The getLangvars method, retrieves the local lang vars specific to this view.
     * @access public
     * @return Language
     */
    public function getLangvars()
    {
        $lang = Registry::get("lang");
        return $lang;
    }

    /**
     * The getTemplate method, obtains the template object stored in Registry.
     * @access public
     * @return Template
     */
    public function getTemplate()
    {
        $template = Registry::get("template");
        return $template;
    }

    /**
     * The getTheme method, acquires the theme for the client user.
     * @access public
     * @return String
     */
    public function getTheme()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->frame->getController() == "admincp") {
            $theme = "acp";
        } else {
            $theme = $mysidia->user->getTheme();
            if (!$theme) {
                $theme = $mysidia->settings->theme;
            }
        }
        return $theme;
    }

    /**
     * The loadPlugin method, assigns and executes the plugins for the view object.
     * @access public
     * @return void
     */
    public function loadPlugin()
    {
        // Not available now, so return Void for now.
        return;
    }

    /**
     * Magic method __toString for ViewHelper class, it reveals that te object is an instance of view helper.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia ViewHelper class.";
    }
}
