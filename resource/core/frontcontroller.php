<?php

namespace Resource\Core;

use Exception, ReflectionException, ReflectionMethod;
use Resource\Exception\BlankFieldException;
use Resource\Exception\DuplicateIDException;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;
use Resource\Exception\UnsupportedFileException;
use Resource\Native\MysString;

/**
 * The Abstract FrontController Class, extends from abstract controller class.
 * It is parent to all frontcontroller type classes, there's one front controller for main site, ACP and installation wizard.
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
abstract class FrontController extends Controller
{

    /**
     * The appController property, holds a reference to the app-controller available for this front-controller.
     * @access protected
     * @var appController
     */
    protected $appController;

    /**
     * The metaController property, stores a reference to the root meta-controller object.
     * @access protected
     * @var metaController
     */
    protected $metaController;

    /**
     * Constructor of FrontController Class, which initializes basic controller properties.
     * @access public
     * @return void
     */
    public function __construct()
    {
        $mysidia = Registry::get("mysidia");
        $this->name = $mysidia->input->get("frontcontroller")->toLowerCase();
    }

    /**
     * The getappController method, getter method for property $appController.
     * @access public
     * @return AppController
     */
    public function getAppController()
    {
        return $this->appController;
    }

    /**
     * The getRequest method, it acquires user request and applies basic operations.
     * @access public
     * @return bool
     */
    public function getRequest()
    {
        $mysidia = Registry::get("mysidia");
        $file = "{$mysidia->path->getRoot()}controller/{$this->name}/{$mysidia->input->get("appcontroller")}controller.php";
        return $this->hasAppController($file);
    }

    /**
     * The getView method, getter method for property $view.
     * @access public
     * @return View
     */
    public function getView()
    {
        if (!$this->view) {
            if ($this->appController instanceof AppController) $this->view = $this->appController->getView();
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
        $viewClass = "View\\{$name->capitalize()}\\IndexView";
        $this->view = new $viewClass($this);
    }

    /**
     * The hasAppController method, checks if the app-controller exists in the given directory.
     * @param String $file
     * @access private
     * @return bool
     */
    private function hasAppController($file)
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->get("appcontroller") == "index") $appControllerExist = false;
        elseif (file_exists($file)) $appControllerExist = true;
        else $appControllerExist = false;
        return $appControllerExist;
    }

    /**
     * The render method, it loads the corresponding view
     * @access public
     * @return void
     */
    public function render()
    {
        if ($this->flags) $this->view->triggerError($this->flags);
        else {
            $action = $this->action ? (string)$this->action : "index";
            $this->view->$action();
        }
        $this->view->render();
    }

    /**
     * The appFrontController method, setter method for property $appController.
     * @param AppController $appController
     * @access public
     * @return FrontController
     */
    public function setAppController(AppController $appController)
    {
        $this->appController = $appController;
    }

    /**
     * The handleRequest method, triggers controller action and handle generic exceptions.
     * @access public
     * @return void
     */
    public function handleRequest()
    {
        try {
            $this->triggerAction();
            return true;
        } catch (InvalidActionException $iae) {
            $this->setFlags("global_action_title", $iae->getMessage());
            return false;
        } catch (NoPermissionException $npe) {
            $this->setFlags("global_error", $npe->getMessage());
            return false;
        } catch (BlankFieldException $bfe) {
            $this->setFlags("global_blank_title", $bfe->getMessage());
            header("Refresh:3; URL='../index'");
            return false;
        } catch (InvalidIDException $iie) {
            $this->setFlags("global_id_title", $iie->getMessage());
            return false;
        } catch (DuplicateIDException $die) {
            $this->setFlags("global_id_title", $die->getMessage());
            return false;
        } catch (UnsupportedFileException $ufe) {
            $this->setFlags("global_error", $ufe->getMessage());
            return false;
        } catch (ReflectionException) {
            $this->setFlags("global_action_title", "global_action");
            return false;
        } catch (Exception $e) {
            $error = strtolower(str_replace("Exception", "_error", $e::class));
            $this->setFlags($error, $e->getMessage());
            return false;
        }
    }

    /**
     * The triggerAction method, handles the invocation of controller action.
     * This method is protected and may be overridden by child classes to provide additional implementation.
     * @access protected
     * @return void
     */
    protected function triggerAction()
    {
        $mysidia = Registry::get("mysidia");
        $controllerClass = "Controller\\{$this->name}\\{$mysidia->input->get("appcontroller")}Controller";
        $this->action = $mysidia->input->action();
        $this->appController = new $controllerClass;
        $this->appController->setFrontController($this);
        $actionClass = new ReflectionMethod($this->appController, $this->action);
        $numRequiredArgs = $actionClass->getNumberOfRequiredParameters();
        if (count($mysidia->input->params()) < $numRequiredArgs) throw new ReflectionException;
        $actionClass->invokeArgs($this->appController, $mysidia->input->params());
    }
}
