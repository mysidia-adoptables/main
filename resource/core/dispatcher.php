<?php

namespace Resource\Core;
use Exception;
use Resource\Collection\HashMap;
use Resource\Native\MysString;

/**
 * The Dispatcher Class, it uses information from Router to generate resources.
 * It fills in the input class get property with useful information.
 * @category Resource
 * @package Utility
 * @author Hall of Famer 
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo The dispatcher class will be revised once the input class is overhauled.
 */

final class Dispatcher extends Core{

	/**
	 * The router property, holds a reference to the Router Object.
	 * @access private
	 * @var Router
     */	
    private $router;
	
	/**
	 * The map property, stores all get variables that will be available in Input Object.
	 * @access private
	 * @var Map
     */		
	private $map;
    
	/**
     * Constructor of Dispatcher Class, it assigns a reference if Router to its property.   
     * @param Router  $router
     * @access public
     * @return void
     */
	public function __construct(Router $router){
	    $this->router = $router;
	}

	/**
     * The getRouter method, getter method for property $getRouter. 
     * @access public
     * @return Router
     */
	public function getRouter(){
	    return $this->router;
	}

	/**
     * The dispatch method, it is where information from router is converted into resources. 
     * @access public
     * @return void
     */	
	public function dispatch(){
        if($this->map) throw new Exception("Request already dispatched previously...");
	    $mysidia = Registry::get("mysidia");
		$this->map = new HashMap;
		
        $frontcontroller = $this->router->getFrontController();	
        $this->map->put(new MysString("frontcontroller"), new MysString($mysidia->input->secure($frontcontroller)));		
		
		$appcontroller = $this->router->getAppController();	
        $this->map->put(new MysString("appcontroller"), new MysString($mysidia->input->secure($appcontroller)));		
		
		$action = $this->router->getAction();
		$this->map->put(new MysString("action"), new MysString($mysidia->input->secure($action)));
		
		$parameters = $this->router->getParams();
        $params = [];
		if($parameters){
		    foreach($parameters as $key => $param){
                $params[] = $param;
			    $this->map->put(new MysString($key), new MysString($mysidia->input->secure($param)));
			}
		}
        
        $page = $this->router->getPage();
        if($page) $this->map->put(new MysString("page"), new MysString($page));
		
        $mysidia->input->setMap($this->map);
        $mysidia->input->setParams($params);
		$mysidia->lang->load();
	}
}    