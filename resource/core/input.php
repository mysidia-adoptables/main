<?php

namespace Resource\Core;

use Resource\Collection\HashMap;
use Resource\Native\MysString;
use Resource\Utility\Autoboxer;

/**
 * The Input Class, it is one of Mysidia system core classes.
 * It acts as a secure wrapper for user input in $_GET and $_POST.
 * Input is a final class, no child class shall derive from it.
 * An instance of Input class is generated upon Mysidia system object's creation.
 * This specific instance is available from Registry, just like any other Mysidia core objects.
 * @category Resource
 * @package Core
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.2
 * @todo incorporate input class in Mysidia adoptables system.
 */

final class Input extends Core implements Initializable
{
    /**
     * The action property, which specifies users action.
     * @access private
     * @var String
     */
    private $action;

    /**
     * The params property, which stores an array of input params.
     * @access private
     * @var Array
     */
    private $params;

    /**
     * The autoboxer property, it can be useful converting between primitive types from/to their wrapper types.
     * @access private
     * @var Autoboxer
     */
    private $autoboxer;

    /**
     * The get property, it stores all user input vars in $_GET.
     * @access private
     * @var HashMap
     */
    private $get;

    /**
     * The post property, it stores all user input vars in $_POST.
     * @access private
     * @var HashMap
     */
    private $post;

    /**
     * The request property, which holds request method information: get, post or else.
     * @access public
     * @var String
     */
    public $request;

    /**
     * Constructor of Input Class, it generates basic properties for an input object.
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->checkrequest();
        $this->initialize();
    }

    /**
     * The initialize method, which handles parsing of user input vars.
     * @access public
     * @return void
     */
    public function initialize()
    {
        $this->autoboxer = new Autoboxer();
        $post = filter_input_array(INPUT_POST);
        if ($post) {
            $this->post = new HashMap();
            foreach ($post as $key => $value) {
                $this->post->put(new MysString($key), $this->autoboxer->wrap($value));
            }
        }
    }

    /**
     * The post method, returns a user input var stored in Input::$post property.
     * @param String  $key
     * @access public
     * @return Mixed
     */
    public function post($key = "", $secure = true)
    {
        if (!$this->post) {
            return null;
        } elseif (empty($key)) {
            return $this->post;
        } else {
            $value = $this->post->get(new MysString($key));
            $rawValue = ($value == null) ? null : $this->autoboxer->unwrap($value);
            return $secure ? $this->secure($rawValue) : $rawValue;
        }
    }

    /**
     * The rawPost method, returns a user input var in the raw form without secure.
     * @param String  $key
     * @access public
     * @return String
     */
    public function rawPost($key = "")
    {
        return $this->post($key, false);
    }

    /**
     * The get method, returns a user input var stored in Input::$get property.
     * @param String  $key
     * @access public
     * @return Object
     */
    public function get($key = "")
    {
        if (empty($key) and $this->get instanceof HashMap) {
            return $this->get;
        }
        return $this->get->get(new MysString($key));
    }

    /**
     * The setMap method, assign dispatcher's variables into Input object's get and action properties.
     * @param HashMap  $map
     * @access public
     * @return void
     */
    public function setMap(HashMap $map)
    {
        $this->get = $map;
        $this->action = $map->get(new MysString("action"));
    }

    /**
     * The action method, verifies whether a specified action is taken by this user.
     * @access private
     * @return Mixed
     */
    public function action()
    {
        if (!$this->action) {
            return null;
        }
        return $this->action->getValue();
    }

    /**
     * The params method, fetches all parameters in the URL query.
     * @access private
     * @return Array
     */
    public function params()
    {
        if (!$this->params) {
            return [];
        }
        return $this->params;
    }

    /**
     * The setParams method, setter method for params property.
     * @param Array  $params
     * @access private
     * @return void
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * The checkRequest method, checks to see the request method of a particular user
     * @access private
     * @return Boolean
     */
    private function checkRequest()
    {
        // This method checks if there is user input, and returns the request_method if evaluated to be true
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $this->request = "post";
            return true;
        } elseif ($_SERVER['REQUEST_METHOD'] == "GET") {
            $this->request = "get";
            return true;
        } else {
            $this->request = false;
        }
    }

    /**
     * The selected method, checks if a user input var is selected.
     * A field is considered selected unless it is empty or 'none'.
     * @param String  $key
     * @access public
     * @return Boolean
     */
    public function selected($key)
    {
        if (!$this->post || empty($key)) {
            return false;
        }
        $value = $this->post($key);
        return ($value && $value != "none");
    }

    /**
     * The secure method, parse user input in a safe manner.
     * @param Mixed  $data
     * @access public
     * @return Mixed
     */
    public function secure($data)
    {
        if (is_string($data)) {
            $data = str_replace("%20", " ", addslashes(htmlentities(strip_tags($data))));
        }
        return $data;
    }
}
