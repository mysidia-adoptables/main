<?php

namespace Resource\Core;

use Exception;
use Resource\Native\Objective;
use ReturnTypeWillChange;
use SplFileInfo;

/**
 * The File Class, extending from SplFileInfo class. It is one of Mysidia system core classes.
 * It acts as an initializer and wrapper for Mysidia-specific files.
 * It implements PHP basic file functions, and adds enhanced features upon them.
 * Similar to Database class, it does not extend from Abstract Core class.
 * An instance of File class is generated upon Mysidia system object's creation.
 * This specific instance is available from Registry, just like any other Mysidia core objects.
 * @category Resource
 * @package Core
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.2
 * @todo Complete the method move().
 */
class File extends SplFileInfo implements Objective, Initializable
{

    /**
     * The extension property, it stores the extension info of this current file.
     * @access private
     * @var String
     */
    private $extension;

    /**
     * The base property, which defines the base file name.
     * @access private
     * @var String
     */
    private $base;

    /**
     * Constructor of File Class, it initializes basic file properties.
     * @access public
     * @return void
     */
    public function __construct($fileurl)
    {
        parent::__construct($fileurl);
        $this->initialize();
    }

    /**
     * The initialize method, which handles basic include path operations.
     * @access public
     * @return Void
     */
    public function initialize()
    {
        $this->extension = "." . $this->getExtension();
        if ($this->checkExtension()) $this->base = parent::getBasename($this->extension);
    }

    /**
     * The checkExtension method, it checks whether the file extension is supported in this system.
     * This method returns a boolean value true upon successful extension validation.
     * @access public
     * @return bool
     */
    protected function checkExtension()
    {
        $extensions = [".php", ".js", ".css", ".html", ".htm", ".xml", ".yaml", ".tpl", ".jpg", ".gif", ".png", ".txt", ".ttf", ".psd", ".db", ".htaccess"];
        if (!in_array($this->extension, $extensions)) throw new Exception('Invalid file extension.');
        else return true;
    }

    /**
     * The getBasename method, overrides SplFileInfo's getBasename method and offers its own definition.
     * @param String $suffix
     * @access public
     * @return void
     */
    #[ReturnTypeWillChange]
    public function getBasename($suffix = null)
    {
        return $this->base;
    }

    /**
     * The equals method, checks whether target object is equivalent to this one.
     * @param Objective $object
     * @access public
     * @return bool
     */
    public function equals(Objective $object)
    {
        return ($this == $object);
    }

    /**
     * The getClassName method, returns class name of an instance.
     * @access public
     * @return String
     */
    public function getClassName()
    {
        return static::class;
    }

    /**
     * The hashCode method, returns the hash code for the very file.
     * @access public
     * @return int
     */
    public function hashCode()
    {
        return hexdec(spl_object_hash($this));
    }

    /**
     * The move method, which can move a file to desired directory.
     * This is a feature planned but not yet developed in current version.
     * @access public
     * @return void
     */
    public function move()
    {

    }

    /**
     * The serialize method, serializes this File Object into string format.
     * @access public
     * @return String
     */
    public function serialize()
    {
        return serialize($this);
    }

    /**
     * The unserialize method, decode a string to its object representation.
     * @param String $string
     * @access public
     * @return String
     */
    public function unserialize($string)
    {
        return unserialize($string);
    }

    /**
     * Magic method __toString() for File class, returns file information.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "File Object.";
    }
}
