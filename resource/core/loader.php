<?php

namespace Resource\Core;

use ArrayObject;
use Exception;
use Resource\Native\MysObject;

/**
 * The Loader Class, it is vital to class autoloading mechanism of this script.
 * It is capable of loading every Mysidia classes, while ignores third party classes.
 * @category Resource
 * @package Core
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.2
 * @todo Not much at this point.
 *
 */

class Loader extends MysObject
{
    /**
     * The classes property, stores a list of classes already loaded.
     * @access protected
     * @var ArrayObject
     */
    protected $classes;

    /**
     * The dir property, defines relative directory for loading process.
     * @access protected
     * @var String
     */
    protected $dir;

    /**
     * Constructor of Loader Class, it assigns $dir property and registers loader to PHP.
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->classes = new ArrayObject();
        $this->dir = "";
        spl_autoload_register($this->load(...));
    }

    /**
     * The extract method, returns a list of registered auto-loader methods.
     * @access public
     * @return void
     */
    public function extract()
    {
        return spl_autoload_function();
    }

    /**
     * The getClasses method, returns a set of loaded classes.
     * @access public
     * @return ArrayObject
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * The load method, it is where class auto-loading is handled.
     * @access public
     * @return void
     */
    public function load($class)
    {
        if (str_contains((string) $class, "Smarty")) {
            return;
        }

        $this->classes->append($class);
        $classPath = strtolower((string) $class);
        if (str_contains((string) $class, "\\")) {
            $classPath = strtolower(str_replace("\\", "/", $classPath));
        }

        if (file_exists("{$this->dir}{$classPath}.php")) {
            require("{$this->dir}{$classPath}.php");
        } else {
            throw new Exception("Fatal Error: Class {$class} either does not exist, or has its include path misconfigured!");
        }
    }
}
