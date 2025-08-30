<?php

namespace Resource\Core;

use PDO;
use PDOException;
use Model\DomainModel\Member;
use Model\DomainModel\Visitor;

/**
 * The Mysidia Class, also known as the System Class.
 * It acts as an initializer and wrapper for core system objects.
 * It is a final class, no child class may inherit from Mysidia.
 * An instance of Mysidia object is available from Registry, it is easy to use.
 * @category Resource
 * @package Core
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.2
 * @todo Coding on methods such as loadCrons() and loadPlugins().
 *
 */

final class Mysidia extends Core
{
    /**
     * version constant, displays the version of Mysidia Adoptables in String format.
     */
    public const version = "1.3.6";

    /**
     * vercode constant, reveals the version code of Mysidia Adoptables in Int format.
     */
    public const vercode = 136;

    /**
     * The path property, which is not yet fully developed at this point.
     * @access public
     * @var Path
     */
    public $path;

    /**
     * The file property, it stores information of the current file being processed.
     * @access public
     * @var File
     */
    public $file;

    /**
     * The db property, most useful in retrieving/handling of database queries/commands.
     * @access public
     * @var PDO|Database
     */
    public $db;

    /**
     * The cookies property, which stores mysidia cookie variables in a secure and convenient manner.
     * @access public
     * @var Cookies
     */
    public $cookies;

    /**
     * The session property, which stores mysidia session variables in a secure and convenient manner
     * @access public
     * @var Session
     */
    public $session;

    /**
     * The creator property, it loads a creator class used for massive object generation.
     * Default creator class loaded is the UserCreator class.
     * @access public
     * @var Creator
     */
    public $creator;

    /**
     * The user property, or the current user property to be precise.
     * Current user can be an instance of Member or Visitor class, it depends on circumstances.
     * @access public
     * @var User
     */
    public $user;

    /**
     * The usergroup property, also as a sub-property for Mysidia::$user property.
     * The usergroup property is referenced from Mysidia::$user to give it an easier access.
     * @access public
     * @var Usergroup
     */
    public $usergroup;

    /**
     * The settings property, it stores information of global settings for Mysidia Adoptables.
     * @access public
     * @var GlobalSettings
     */
    public $settings;

    /**
     * The frame property, which contains important details about the frame being browsed.
     * @access public
     * @var Frame
     */
    public $frame;

    /**
     * The lang property, loads controller-related language vars from folder /lang.
     * Language class development is still in beta-stage.
     * @access public
     * @var Language
     */
    public $lang;

    /**
     * The template property, reference of the template var being used.
     * This is a feature planned but not yet developed in Mysidia Adoptables script.
     * The staff team recommends Smarty as template engine, though Twig is a good candidate too.
     * @access public
     * @var Template
     */
    public $template;

    /**
     * The request property, which reveals the request method.
     * @access public
     * @var String
     */
    public $request;

    /**
     * The input property, it holds user input values in a secure manner.
     * This property allows user input to be accessed through OO way.
     * @access public
     * @var Input
     */
    public $input;

    /**
     * The plugins property, which wraps all available plugins on the system.
     * @access public
     * @var Plugins
     */
    public $plugins;

    /**
     * The debug property, reveals whether the site is in debug mode or not
     * This feature is planned but not yet developed in current version.
     * @access public
     * @var Boolean
     */
    public $debug = false;


    /**
     * Constructor of Mysidia Class, it initializes basic system properties.
     * @access public
     * @return void
     */
    public function __construct()
    {
        Registry::set("mysidia", $this, true, true);
        $this->locatePath();
        $this->loadCurrentFile();
        $this->loadDb();
        $this->loadPlugins();
        $this->getCookies();
        $this->getSession();
        $this->getSettings();
        $this->getSystems();
        $this->getCurrentUser();
        $this->getFrame();
        $this->getTemplate();
        $this->parseInput();
        $this->getLanguage();
    }

    /**
     * The checkVersion method, returns a description of current mysidia Adoptables version.
     * If the version returned is outdated, the admin will have an option to download the latest script from ACP.
     * @access public
     * @return Boolean
     */
    public function checkVersion()
    {
        $versions = explode(",", trim(file_get_contents("http://www.mysidiaadoptables.com/version.txt")));
        if (self::vercode >= $versions[0]) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * The locatePath method, returns a Path object with detailed information.
     * @access public
     * @return Path
     */
    public function locatePath()
    {
        $this->path = new Path();
        Registry::set("path", $this->path, true, true);
        return $this->path;
    }

    /**
     * The loadCurrentFile method, gets the current file information from the server.
     * @access public
     * @return File
     */
    public function loadCurrentFile()
    {
        $this->file = new File($_SERVER['SCRIPT_FILENAME']);
        Registry::set("file", $this->file, true, true);
        return $this->file;
    }

    /**
     * The loadDb method, creates an instance of the Database Object to be used in the system.
     * @access public
     * @return Database
     */
    public function loadDb()
    {
        try {
            $this->db = new Database(DBNAME, DBHOST, DBUSER, DBPASS, PREFIX);
            Registry::set("database", $this->db, true, true);
        } catch (PDOException $pe) {
            die("Could not connect to database, the following error has occurred: <br><b>{$pe->getmessage()}</b>");
        }
        return $this->db;
    }

    /**
     * The getCookies method, retrieves cookie information from server.
     * @access public
     * @return Cookies
     */
    public function getCookies()
    {
        $this->cookies = new Cookies();
        Registry::set("cookies", $this->cookies, true, true);
        return $this->cookies;
    }

    /**
     * The getSession method, acquires session information from server.
     * @access public
     * @return Session
     */
    public function getSession()
    {
        $this->session = new Session();
        Registry::set("session", $this->session, true, true);
        return $this->session;
    }

    /**
     * The getCurrentUser method, gets the current user information and assigns to Mysidia system properties.
     * The current user is generated using creator/factory method.
     * @access public
     * @return User
     */
    public function getCurrentUser()
    {
        $uid = $this->secure($this->cookies->getcookies("mysuid"));
        $this->user = $this->loginCheck() ? new Member($uid) : new Visitor();
        $this->usergroup = $this->user->getUsergroup(Model::MODEL);
        Registry::set("user", $this->user, true, true);
        Registry::set("usergroup", $this->usergroup, true, true);
        return $this->user;
    }

    /**
     * The getSettings method, obtains global settings information from database table prefix.settings.
     * @access public
     * @return GlobalSettings
     */
    public function getSettings()
    {
        $this->settings = new GlobalSettings($this->db);
        Registry::set("settings", $this->settings, true, true);
        return $this->settings;
    }

    /**
     * The getSystems method, obtains system settings information from database table prefix.systems_settings.
     * @access public
     * @return SystemSettings
     */
    public function getSystems()
    {
        $this->systems = new SystemSettings($this->db);
        Registry::set("systems", $this->systems, true, true);
        return $this->systems;
    }

    /**
     * The getFrame method, handles frame object and information creation/processing.
     * If no specific input is provided, the page is a system document. Otherwise it is a custom document.
     * @access public
     * @return Frame
     */
    public function getFrame()
    {
        $this->frame = new Frame();
        Registry::set("frame", $this->frame, true, true);
        return $this->frame;
    }

    /**
     * The getLanguage method, retrieves language vars from directory /lang.
     * Initial instantiation of language object only takes care of global language vars.
     * For additional controller-specific language vars to be loaded, use $lang->load() method.
     * @access public
     * @return Language
     */
    public function getLanguage()
    {
        $this->lang = new Language($this->path, $this->file);
        Registry::set("lang", $this->lang, true, true);
        return $this->lang;
    }

    /**
     * The getTemplate method, will be added later once smarty or another template engine such as Twig is implemented.
     * @access public
     * @return Template
     */
    public function getTemplate()
    {
        $this->template = new Template($this->path);
        Registry::set("template", $this->template, true, true);
        return $this->template;
    }

    /**
     * The checkRequest method, it determines whether the request emthod is POST, GET or others.
     * This method returns TRUE if request method has been specified, otherwise it returns FALSE.
     * @access public
     * @return Boolean
     */
    public function checkRequest()
    {
        // This method checks if there is user input, and returns the request_method if evaluated to be true
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $this->request = "post";
        } elseif ($_SERVER['REQUEST_METHOD'] == "GET") {
            $this->request = "get";
        }

        Registry::set("request", $this->request, true, true);
        return $this->request ? true : false;
    }

    /**
     * The parseInput method, which handles user input in secure way and stores them in an ArrayObject.
     * In future the Input class may be incorporated into use, and thus this method will return an instance of Input class.
     * @access public
     * @return Input
     */
    public function parseInput()
    {
        $this->input = new Input();
        Registry::set("input", $this->input, true, true);
        return $this->input;
    }

    /**
     * The loadCrons method, a feature planned but not yet developed in this version.
     * @access public
     * @return void
     */
    public function loadCrons()
    {
        return false;
    }

    /**
     * The loadPlugins method, a feature planned but not yet developed in this version.
     * @access public
     * @return Plugins
     */
    public function loadPlugins()
    {
        $this->plugins = new Plugins();
        Registry::set("plugins", $this->plugins, true, true);
        return $this->plugins;
    }

    /**
     * The displayError method, it shows basic system errors that may appear in any part of the script.
     * @param String  $error
     * @access public
     * @return void
     */
    public function displayError($error)
    {
        $document = $this->frame->getDocument();
        switch ($error) {
            case "register":
                $document->setTitle($this->lang->global_register_title);
                $document->addLangvar($this->lang->global_register);
                break;
            case "login":
                $document->setTitle($this->lang->global_login_title);
                $document->addLangvar($this->lang->global_login);
                break;
            case "guest":
                $document->setTitle($this->lang->global_guest_title);
                $document->addLangvar($this->lang->global_guest);
                break;
            case "id":
                $document->setTitle($this->lang->global_id_title);
                $document->addLangvar($this->lang->global_id);
                break;
            case "action":
                $document->setTitle($this->lang->global_action_title);
                $document->addLangvar($this->lang->global_action);
                break;
            case "session":
                $document->setTitle($this->lang->global_session_title);
                $document->addLangvar($this->lang->global_session);
                break;
            case "access":
                $document->setTitle($this->lang->global_access_title);
                $document->addLangvar($this->lang->global_access);
                break;
            default:
                $document->setTitle($this->lang->global_error);
                $document->addLangvar($this->lang->global_error);
        }
    }

    /**
     * The exists method, checks if a variable is available or not.
     * It serves an extension to PHP's isset() function, but supports both variable and expression.
     * @access public
     * @return Boolean
     */
    public function exists($var)
    {
        return isset($var);
    }

    /**
     * The isEmpty method, checks if a variable is empty or not.
     * It serves an extension to PHP's empty() function, but supports both variable and expression.
     * @access public
     * @return Boolean
     */
    public function isEmpty($var)
    {
        return empty($var);
    }

    /**
     * The secure method, performs security filters on incoming data.
     * @param Mixed  $data
     * @access public
     * @return String
     */
    #[\ReturnTypeWillChange]
    public function secure($data)
    {
        return strip_tags(addslashes(htmlentities($data ?? '')), '');
    }

    /**
     * The format method, formats text from rich text editor.
     * @param String  $data
     * @param Boolean  $removeExtra
     * @access public
     * @return String
     */
    public function format($data, $removeExtra = true)
    {
        $text = stripslashes(html_entity_decode($data));
        return $removeExtra ? str_replace("\r\n", "", $text) : $text;
    }

    /**
     * The loginCheck method, checks if the current user is a member(logged in), or a guest(not logged in).
     * @access public
     * @return Boolean
     */
    public function loginCheck()
    {
        if (!$this->cookies->getcookies("mysuid") || !$this->cookies->getcookies("myssession")) {
            return false;
        } else {
            $uid = $this->secure($this->cookies->getcookies("mysuid"));
            $session = $this->secure($this->cookies->getcookies("myssession"));

            //Run login operation
            $luser = $this->db->select("users", ["uid", "session"], "uid = :uid", ["uid" => $uid])->fetchObject();
            $luid = $luser->uid ?? null;
            $lsess = isset($luser->uid) ? $luser->session : null;

            if ($uid == $luid && $session == $lsess) {
                return true;
            } else {
                if (isset($_COOKIE['mysuid'])) {
                    setcookie("mysuid", $uid, ['expires' => time() - 10]);
                }
                if (isset($_COOKIE['myssession'])) {
                    setcookie("myssession", $session, ['expires' => time() - 10]);
                }
                return false;
            }
        }
    }

    /**
     * The handle method, handles routing and dispatching of URI to controller actions.
     * @param String  $uri
     * @access public
     * @return void
     */
    public function handle($uri)
    {
        $router = new Router($uri);
        $router->route();
        $dispatcher = new Dispatcher($router);
        $dispatcher->dispatch();
    }

    /**
     * The output method, the very last method to call in a script to show the output to users.
     * @access public
     * @return void
     */
    public function output()
    {
        $this->template->output();
    }
}
