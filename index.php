<?php

use Resource\Core\Bundles;
use Resource\Core\Loader;
use Resource\Core\Mysidia;
use Resource\Core\Registry;
use Resource\Native\MysObject;
use Service\ApplicationService\OnlineService;

require(__DIR__ . "/resource/native/objective.php");
require(__DIR__ . "/resource/native/mysobject.php");
require(__DIR__ . "/resource/core/loader.php");

class IndexController extends MysObject{
    
    private $frontController;
    
    public function initialize(){
        $config = "config.php";
        if(!file_exists($config)) exit("The file config.php cannot be found. If this is a new installation, please rename config_adopts.php to config.php and try again.");
        require $config;
        if(!defined("DBHOST") || !defined("DBUSER")) $this->redirectToInstall();
        $this->frontController = $this->isAdminCP($_SERVER['REQUEST_URI']) ? "AdminCP" : "Main";
        $this->initErrorHandler();
        $this->initLoader();
        $this->initBundles();
        $this->initMysidia($_SERVER['REQUEST_URI']);
    }
    
    private function isAdminCP($uri){
        return (strpos($uri, "/admincp") !== FALSE);
    }
    
    private function redirectToInstall(){
        $protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
        $redirectURL = $protocol . $_SERVER['HTTP_HOST'] . str_replace("index.php", "install", $_SERVER['PHP_SELF']); 
        header("Location: {$redirectURL}");
        exit;        
    }
    
    private function initErrorHandler(){
        if (PHP_MAJOR_VERSION >= 7){
            set_error_handler(function($errno, $errstr){
                return strpos($errstr, 'Declaration of') === 0;
            }, E_WARNING);
        }
        error_reporting(E_ALL & ~E_STRICT);
    }
    
    private function initLoader(){
        $loader = new Loader;
        $registry = Registry::getInstance();
        if($registry) Registry::set("loader", $loader, TRUE, TRUE);
    }
    
    private function initBundles(){
        $bundles = new Bundles;
        $bundles->register("smarty", "bundles/smarty/", "Smarty.class.php");
        $bundles->register("htmlpurifier", "bundles/htmlpurifier/", "HTMLPurifier.auto.php");    
        $registry = Registry::getInstance();
        if($registry) Registry::set("bundles", $bundles, TRUE, TRUE);        
    }
    
    private function initMysidia($uri){
        $mysidia = new Mysidia;
        $mysidia->handle($uri);
        $wol = new OnlineService;
        $wol->update();
        Registry::set("wol", $wol);   
    }
    
    public function run(){
        $frontControllerClass = "\\Controller\\{$this->frontController}\\IndexController";
        $frontController = new $frontControllerClass;
        if($frontController->getRequest()) $frontController->handleRequest();
        else $frontController->index();
		$frontController->getView();	
        $frontController->render();	
    }
	
    public static function main(){
        $application = new self;
        $application->initialize();
        $application->run();
	}	
}

IndexController::main();