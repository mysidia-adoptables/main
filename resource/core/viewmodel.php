<?php

namespace Resource\Core;

use Resource\Core\Model;
use Resource\Native\MysObject;

/**
 * The Abstract ViewModel Class, extends from root object class.
 * It is parent to all view model type classes, which stores view model properties.
 * @category Resource
 * @package Core
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.6
 * @todo Not much at this point.
 * @abstract
 *
 */

abstract class ViewModel extends MysObject
{
    /**
     * The model property, which stores a reference to the domain object related to the view model.
     * @access protected
     * @var Model
     */
    protected $model;

    /**
     * Constructor of ViewModel Class, which stores the internal domain object.
     * @param Model  $model
     * @access public
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * The getID method, obtain the ID for the view model.
     * @access public
     * @return id
     */
    public function getID()
    {
        return $this->model->getID();
    }

    /**
     * The getModel method, getter method for property $model.
     * @access public
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * The format method, format a given text with filters to make it safe for display.
     * @param String  $text
     * @access public
     * @return String
     */
    public function format($text)
    {
        $text = html_entity_decode($text);
        $text = str_replace("\r\n", "", $text);
        $text = stripslashes($text);
        return $text;
    }
}
