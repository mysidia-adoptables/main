<?php

use Resource\Core\Bundles;
use Resource\Core\Loader;
use Resource\Core\Mysidia;
use Resource\Core\Registry;
use Resource\Native\MysObject;
use Service\ApplicationService\OnlineService;

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die('It looks like you have not installed the dependencies yet.');
}

require_once './vendor/autoload.php';
require(__DIR__ . "/resource/native/objective.php");
require(__DIR__ . "/resource/native/mysobject.php");
require(__DIR__ . "/resource/core/loader.php");

class IndexController extends MysObject
{
    private $frontController;

    public static function main()
    {
        $application = new self();
        $application->initialize();
        $application->run();
    }

    public function initialize()
    {
        $config = "config.php";
        if (!file_exists($config)) {
            exit("The file config.php cannot be found. If this is a new installation, please rename config_adopts.php to config.php and try again.");
        }
        require $config;
        if (!defined("DBHOST") || !defined("DBUSER")) {
            $this->redirectToInstall();
        }
        $this->frontController = $this->isAdminCP($_SERVER['REQUEST_URI']) ? "AdminCP" : "Main";
        $this->initErrorHandler();
        $this->initLoader();
        $this->initBundles();
        $this->initMysidia($_SERVER['REQUEST_URI']);
    }

    private function redirectToInstall(): never
    {
        $protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
        $redirectURL = $protocol . $_SERVER['HTTP_HOST'] . str_replace("index.php", "install", $_SERVER['PHP_SELF']);
        header("Location: {$redirectURL}");
        exit;
    }

    private function isAdminCP($uri)
    {
        return (str_contains((string)$uri, "/admincp"));
    }

    private function initErrorHandler()
    {
        if (PHP_MAJOR_VERSION >= 7) {
            set_error_handler(fn ($errno, $errstr) => str_starts_with((string)$errstr, 'Declaration of'), E_WARNING);
        }
        error_reporting(E_ALL);
    }

    private function initLoader()
    {
        $loader = new Loader();
        $registry = Registry::getInstance();
        if ($registry) {
            Registry::set("loader", $loader, true, true);
        }
    }

    private function initBundles()
    {
        $bundles = new Bundles();
        $bundles->register("smarty", __DIR__ . "/vendor/smarty/smarty/src/", "Smarty.php");
        $bundles->register("htmlpurifier", __DIR__ . "/vendor/ezyang/htmlpurifier/library/", "HTMLPurifier.auto.php");
        $registry = Registry::getInstance();
        if ($registry) {
            Registry::set("bundles", $bundles, true, true);
        }
    }

    private function initMysidia($uri)
    {
        $mysidia = new Mysidia();
        $mysidia->handle($uri);
        $wol = new OnlineService();
        $wol->update();
        Registry::set("wol", $wol);
    }

    public function run()
    {
        $frontControllerClass = "\\Controller\\{$this->frontController}\\IndexController";
        $frontController = new $frontControllerClass();
        if ($frontController->getRequest()) {
            $frontController->handleRequest();
        } else {
            $frontController->index();
        }
        $frontController->getView();
        $frontController->render();
    }
}

IndexController::main();
