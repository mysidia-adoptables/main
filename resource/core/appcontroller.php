<?php

namespace Resource\Core;

use ReflectionException, ReflectionMethod;
use Resource\Collection\HashMap;
use Resource\Exception\AlreadyLoggedinException;
use Resource\Exception\GuestNoaccessException;
use Resource\Exception\InvalidActionException;
use Resource\Native\MysString;
use Resource\Native\Objective;

/**
 * The Abstract AppController Class, extends from abstract controller class.
 * It is parent to all application controller type classes, they are vast in numbers.
 * @category Controller
 * @package Controller
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.2
 * @todo Not much at this point.
 * @abstract
 *
 */
abstract class AppController extends Controller
{

    /**
     * The fields property, stores a map of key-value pairs to be passed to View.
     * @access protected
     * @var HashMap
     */
    protected $fields;

    /**
     * The frontController property, holds a reference to the front-controller that delegates to this app-controller.
     * @access protected
     * @var frontController
     */
    protected $frontController;

    /**
     * The subController property, holds a reference to the sub-controller available for this app-controller.
     * @access protected
     * @var SubController
     */
    protected $subController;

    /**
     * Constructor of AppController Class, which initializes basic controller properties.
     * @param String $access
     * @access public
     * @return void
     */
    public function __construct(/**
     * The access property, specifies the access control of this controller.
     * @access protected
     */
    protected $access = "")
    {
        $mysidia = Registry::get("mysidia");
        $this->frontController = $mysidia->input->get("frontcontroller");
        $this->action = $mysidia->input->action();
        $this->name = $mysidia->input->get("appcontroller");
        $this->fields = new HashMap;

        if (!$this->hasAction()) {
            throw new InvalidActionException("global_action");
        }
        if (!empty($this->access)) $this->handleAccess();
    }

    /**
     * The getAccess method, getter method for property $access.
     * @access public
     * @return String
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * The getFields method, getter method for property $fields.
     * @access public
     * @return HashMap
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * The getFrontController method, getter method for property $frontController.
     * @access public
     * @return FrontController
     */
    public function getFrontController()
    {
        return $this->frontController;
    }

    /**
     * The getSubController method, getter method for property $subController.
     * @access public
     * @return SubController
     */
    public function getSubController()
    {
        return $this->subController;
    }

    /**
     * The getView method, getter method for property $view.
     * @access public
     * @return View
     */
    public function getView()
    {
        if (!$this->view) {
            if ($this->subController instanceof SubController) $this->view = $this->subController->getView();
            else $this->loadView($this->name);
        }
        return $this->view;
    }

    /**
     * The loadView method, it loads the corresponding view for the controller.
     * @param String $name
     * @access public
     * @return View
     */
    public function loadView(MysString $name)
    {
        $viewClass = "View\\{$this->frontController}\\{$name->capitalize()}View";
        $this->view = new $viewClass($this);
    }

    /**
     * The handleAccess method, carries out basic access control
     * At this point it only distinguishes member-only and guest-only pages, but in future it will handle more.
     * This method is protected since AppController::handleAccess() can be invoked by child classes at any time.
     * @access protected
     * @return void
     */
    protected function handleAccess()
    {
        $mysidia = Registry::get("mysidia");
        if ($this->access == "member" && !$mysidia->user->isLoggedIn()) {
            throw new GuestNoaccessException($mysidia->lang->global_guest);
        }
        if ($this->access == "guest" && $mysidia->user->isLoggedIn()) {
            throw new AlreadyLoggedinException($mysidia->lang->global_login);
        }
    }

    /**
     * The hasAction method, checks if an action exists in this controller.
     * @access private
     * @return bool
     */
    private function hasAction()
    {
        try {
            $method = new ReflectionMethod($this, $this->action);
            return $method->isPublic();
        } catch (ReflectionException) {
            return false;
        }
    }

    /**
     * The index method, construct a default index page.
     * The actual view construction is delegated to view class, this method exists for the sole purpose for reflection method to work.
     * @access public
     * @return void
     */
    public function index()
    {
    }

    /**
     * The setField method, inserts a specific key-value pair into the field map.
     * @param String $key
     * @param Objective $value
     * @access public
     * @return void
     */
    public function setField($key, Objective $value = null)
    {
        $this->fields->put(new MysString($key), $value);
    }

    /**
     * The setFields method, setter method for property $fields.
     * @param HashMap $fields
     * @access public
     * @return void
     */
    public function setFields(HashMap $fields)
    {
        $this->fields = $fields;
    }

    /**
     * The setFlags method, setter method for property flags.
     * @param String $param
     * @param String $param2
     * @access protected
     * @return void
     */
    public function setFlags($param, $param2 = null)
    {
        $this->frontController->setFlags($param, $param2);
    }

    /**
     * The setFrontController method, setter method for property $frontController.
     * @param FrontController $frontController
     * @access public
     * @return FrontController
     */
    public function setFrontController(FrontController $frontController)
    {
        $this->frontController = $frontController;
    }

    /**
     * The setSubController method, setter method for property $subController.
     * @param SubController $subController
     * @access public
     * @return void
     */
    public function setSubController(SubController $subController)
    {
        $this->subController = $subController;
    }
}
