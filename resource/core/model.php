<?php

namespace Resource\Core;

use Resource\Native\MysObject;

/**
 * The Abstract Model Class, extends from abstract object class.
 * It is parent to all model type classes, which stores domain object properties.
 * @category Resource
 * @package Core
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not much at this point.
 * @abstract
 *
 */
abstract class Model extends MysObject
{

    /**
     * OBJ constant, stores the fetch mode Object.
     */
    const OBJ = "object";

    /**
     * MODEL constant, stores the fetch mode Model.
     */
    const MODEL = "model";

    /**
     * GUI constant, stores the fetch mode GUI.
     */
    const GUI = "gui";

    /**
     * INSERT constant, defines the assign mode Insert.
     */
    const INSERT = "insert";

    /**
     * UPDATE constant, defines the assign mode Update.
     */
    const UPDATE = "update";

    /**
     * DELETE constant, defines the assign mode Delete.
     */
    const DELETE = "delete";

    /**
     * IDNAME constant, defines the name of the model ID. By default, it is just id.
     */
    const IDKEY = "id";

    /**
     * Constructor of Model Class, which simply serves as a marker for child classes.
     * @param object $dto
     * @access public
     * @return void
     */
    public function __construct($dto)
    {
        if ($dto) $this->createFromDTO($dto);
    }

    /**
     * The createFromDTO method, populates the fields for the model from the supplied DTO param.
     * @param object $dto
     * @access protected
     * @return void
     */
    protected function createFromDTO($dto)
    {
        foreach ($dto as $key => $val) {
            $this->$key = $val;
        }
    }

    /**
     * The getID method, obtains the ID for the domain model.
     * @access public
     * @return int
     */
    public function getID()
    {
        $idKey = static::IDKEY;
        return $this->$idKey;
    }

    /**
     * The setID method, changes the ID for the domain model.
     * @param int $id
     * @access public
     * @return void
     */
    public function setID($id = 0)
    {
        $idKey = static::IDKEY;
        $this->$idKey = $id;
    }

    /**
     * The generateCode method, creates a random string with the supplied criteria.
     * @param int $length
     * @param bool $symbols
     * @access public
     * @return String
     */
    public function generateCode($length = 10, $symbols = false)
    {
        $set = ["a", "A", "b", "B", "c", "C", "d", "D", "e", "E", "f", "F", "g", "G", "h", "H", "i", "I", "j", "J", "k", "K", "l", "L", "m", "M", "n", "N", "o", "O", "p", "P", "q", "Q", "r", "R", "s", "S", "t", "T", "u", "U", "v", "V", "w", "W", "x", "X", "y", "Y", "z", "Z", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
        $str = '';
        if ($symbols) {
            $symbols = ["~", "`", "!", "@", "$", "#", "%", "^", "+", "-", "*", "/", "_", "(", "[", "{", ")", "]", "}"];
            $set = array_merge($set, $symbols);
        }

        for ($i = 1; $i <= $length; ++$i) {
            $ch = mt_rand(0, count($set) - 1);
            $str .= $set[$ch];
        }
        return $str;
    }

    /**
     * The isNew method, checks if the model is new and should be inserted to database.
     * @access public
     * @return bool
     */
    public function isNew()
    {
        return ($this->getID() == 0);
    }

    /**
     * The isAssoc method, checks if the supplied argument to the model is an associative array.
     * @param Array $field
     * @access public
     * @return bool
     */
    public function isAssoc($field)
    {
        return (is_array($field) && count(array_filter(array_keys($field), 'is_string')) == count($field));
    }

    /**
     * Magic method __toString for Model class, it reveals that the object is a Model object.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia Model class.";
    }

    /**
     * Abstract method save for Model class, it must be implemented by child domain model classes.
     * @access protected
     * @abstract
     */
    protected abstract function save($field, $value);
}
