<?php

namespace Resource\Core;
use ArrayObject, Exception, ReflectionClass;

/**
 * The Router Class, it manages routes and assign important environment variables.
 * The router handles different routing methods for main site, ACP and so on.
 * @category Resource
 * @package Utility
 * @author Hall of Famer 
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not sure, but will come in handy.
 */

final class Router extends Core{

  	/**
	 * The path property, specifies the path of the route.
	 * @access private
	 * @var String
     */	  
	private $path;
	
	/**
	 * The url property, holds a reference to the site url
	 * After constructor operation, the url is cut down to exclude the path string.
	 * @access private
	 * @var String
     */	  
	private $url;
	
	/**
	 * The frontController property, specifies the Front Controller to process user request.
	 * @access private
	 * @var String
     */	 	
	private $frontController;
	
	/**
	 * The appController property, specifies the Application Controller to process user request.
	 * @access private
	 * @var String
     */	 
    private $appController;
	
	/**
	 * The action property, defines the action of the user request.
	 * @access private
	 * @var String
     */	 
	private $action;
	
	/**
	 * The params property, stores all parameters entered by the user.
	 * @access private
	 * @var ArrayObject
     */	
	private $params;
       
	/**
	 * The page property, specifies the page number for paginated views.
	 * @access private
	 * @var int
     */	    
    private $page;   

    
	/**
     * Constructor of Router Class, it initializes basic router properties. 
     * @param String  $url 
     * @access public
     * @return void
     */
    public function __construct($url){	
	    $this->setFrontController($url);
		$this->path = SCRIPTPATH . $this->path;
        $this->url = str_replace($this->path, "", $url);
    }
	
	/**
     * The getFrontController method, getter method for property $frontController. 
     * @access public
     * @return String
     */
	public function getFrontController(){
		return $this->frontController;
	}

	/**
     * The setFrontController method, setter method for property $frontController. 
	 * This method is only available upon Router Object Instantiation, its private so cannot be accessed externally.
	 * @param String  $url
     * @access public
     * @return String
     */
	private function setFrontController($url){
	 	if(strpos($url, "admincp") !== FALSE){
            $this->frontController = "AdminCP";
        }
        elseif(strpos($url, "install") !== FALSE){
            $this->frontController = "Install";
	    }
	    else{
		    $this->frontController = "Main";
		}		
	}

	/**
     * The getAppController method, getter method for property $appController. 
     * @access public
     * @return String
     */
    public function getAppController(){
        return $this->appController;
    }

	/**
     * The setAppController method, setter method for property $appController. 
	 * This method is only available during routing process, its private so cannot be accessed externally.
	 * @param String  $controller
     * @access public
     * @return String
     */
	private function setAppController($controller){	
	    $this->appController = $controller ? $controller : "index";
	}
	
	/**
     * The getAction method, getter method for property $action. 
     * @access public
     * @return String
     */
    public function getAction(){
        return $this->action;
    }

	/**
     * The setAction method, setter method for property $action. 
	 * This method is only available during routing process, its private so cannot be accessed externally.
	 * @param String  $action
     * @access public
     * @return String
     */	
	private function setAction($action){
        if($action && strpos($action, "page-") !== FALSE){
            $this->action = "index";
            $this->setPage($action);
        }
        else $this->action = $action ? $action : "index";
	}

	/**
     * The getParams method, getter method for property $params. 
     * @access public
     * @return ArrayObject
     */
    public function getParams(){
        return $this->params;
    }
    
    /**
     * The getPage method, getter method for property $page. 
     * @access public
     * @return int
     */
    public function getPage(){
        return $this->page;
    }
	
	/**
     * The setParams method, setter method for property $params. 
	 * This method is only available during routing process, its private so cannot be accessed externally.
	 * @param Array  $params
     * @access public
     * @return String
     */	
    private function setParams($params){
        if(!$this->action || !$params) return;
        $className = "\\Controller\\{$this->frontController}\\{$this->appController}Controller";
		$class = new ReflectionClass($className);
        $parameters = $class->getMethod($this->action)->getParameters();
        if(!$params) return;
        $this->params = new ArrayObject;
        $index = 0;
        foreach($parameters as $parameter){
            $param = isset($params[$index]) ? $params[$index] : NULL;
            if(!empty($param) && strpos($param, "page-") !== FALSE) $this->setPage($param);
            else $this->params[$parameter->getName()] = $param;
            $index++;
        }
    }	
    
    /**
     * The hasPage method, checks if the url query has page number defined.
	 * @param Array  $query
     * @access public
     * @return Boolean
     */	
    private function hasPage($query){
        return (strpos(end($query), "page-") !== FALSE);
    }

    /**
     * The setPage method, setter method for property $page. 
	 * This method is only available during routing process, its private so cannot be accessed externally.
	 * @param String  $pageQuery
     * @access public
     * @return String
     */	
    private function setPage($pageQuery){
        $pageArray = explode("-", $pageQuery);
        $this->page = (int)$pageArray[1];        
    }

	/**
     * The route method, this is where the routing process takes place as the router interprets URL.
     * @access public
     * @return String
     */		
	public function route(){		
		try{
		    $query = explode("/", $this->url);
            array_shift($query);
            if($this->frontController != "Main") array_shift($query);
            if($this->hasPage($query)) $this->setPage(array_pop($query));
			$this->setAppController(array_shift($query));
            $this->setAction(array_shift($query));
			$this->setParams($query);
        }
        catch(Exception $e){
            die($e->getmessage());
        }
	}
}