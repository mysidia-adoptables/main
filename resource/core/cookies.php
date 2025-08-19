<?php

namespace Resource\Core;

use Exception;

/**
 * The Cookies Class, it is one of Mysidia system core classes.
 * It acts as an initializer and wrapper for Mys-related cookies.
 * Cookies is a final class, no child class shall derive from it.
 * An instance of Cookies class is generated upon Mysidia system object's creation.
 * This specific instance is available from Registry, just like any other Mysidia core objects.
 * @category Resource
 * @package Core
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.2
 * @todo better naming of cookies methods.
 */
final class Cookies extends Core
{

    /**
     * The mysuid property, which stores the id of the current user.
     * For guest, this id is 0.
     * @access private
     * @var int
     */
    private $mysuid;

    /**
     * The myssession property, which stores the session var of the current user.
     * @access private
     * @var String
     */
    private $myssession;

    /**
     * The mysactivity property, which stores the timestamp for the current user's last activity.
     * @access private
     * @var int
     */
    private $mysactivity;

    /**
     * The mysloginattempt property, which stores how many failed login attempt made by this particular user on the main site.
     * @access private
     * @var int
     */
    private $mysloginattempt;

    /**
     * The mysadmloginattempt property, which stores how many failed login attempt made by this particular user on admin control panel.
     * @access private
     * @var int
     */
    private $mysadmloginattempt;


    /**
     * Constructor of Cookies Class, it loads mys-related cookie vars from $_COOKIE superglobals.
     * @access public
     * @return void
     */
    public function __construct()
    {
        $keyarray = ["mysuid", "myssession", "mysactivity", "mysloginattempt"];
        $cookies = filter_input_array(INPUT_COOKIE);
        if (!$cookies) return;
        foreach ($cookies as $key => $val) {
            if (in_array($key, $keyarray)) $this->$key = $val;
        }
    }

    /**
     * The getcookies method, which retrieves private cookie item from Cookies object.
     * If supplied argument is invalid, an exception will be thrown.
     * @param String $prop
     * @access public
     * @return bool
     */
    public function getcookies($prop)
    {
        if (!property_exists($this, $prop)) throw new Exception('The specified cookie is invalid...');
        return $this->$prop;
    }

    /**
     * The setcookies method, which handles the four basic cookies vars for user who has just successfully logged in.
     * If operation is successful, the method returns a bool value True, so it can be used in conditional statement.
     * @access public
     * @return bool
     */
    public function setcookies($username)
    {
        $mysidia = Registry::get("mysidia");
        ob_start();
        $Month = 2592000 + time();
        $this->mysuid = $mysidia->db->select("users", ["uid"], "username = :username", ["username" => $username])->fetchColumn();
        setcookie("mysuid", (string) $this->mysuid, ['expires' => $Month, 'path' => "/", 'domain' => (string) $_SERVER['HTTP_HOST']]);
        $session = $mysidia->session->getid();
        $this->myssession = md5($this->mysuid . $session);
        setcookie("myssession", $this->myssession, ['expires' => $Month, 'path' => "/", 'domain' => (string) $_SERVER['HTTP_HOST']]);
        $this->mysactivity = time();
        setcookie("mysactivity", $this->mysactivity, ['expires' => $Month, 'path' => "/", 'domain' => (string) $_SERVER['HTTP_HOST']]);
        $this->mysloginattempt = 0;
        setcookie("mysloginattempt", $this->mysloginattempt, ['expires' => $Month, 'path' => "/", 'domain' => (string) $_SERVER['HTTP_HOST']]);
        ob_flush();
        return true;
    }

    /**
     * The setAdminCookie method, which handles admincp related cookies.
     * If operation is successful, the method returns a bool value True, so it can be used in conditional statement.
     * @access public
     * @return bool
     */
    public function setAdminCookies()
    {
        $mysidia = Registry::get("mysidia");
        ob_start();
        $Month = 2592000 + time();
        $session = $mysidia->session->getid();
        $this->mysadmsession = sha1($this->mysuid . $session);
        setcookie("mysadmsession", $this->mysadmsession, ['expires' => $Month, 'path' => "/", 'domain' => (string) $_SERVER['HTTP_HOST']]);
        $this->mysadmloginattempt = 0;
        setcookie("mysadmloginattempt", $this->mysadmloginattempt, ['expires' => $Month, 'path' => "/", 'domain' => (string) $_SERVER['HTTP_HOST']]);
        ob_flush();
        return true;
    }

    /**
     * The deletecookies method, which gets rid of cookies to enable users to log out of the site.
     * If operation is successful, the method returns a bool value True, so it can be used in conditional statement.
     * @access public
     * @return bool
     */
    public function deletecookies()
    {
        $expire = time() - 2592000;
        ob_start();
        setcookie("mysuid", "", ['expires' => $expire, 'path' => "/", 'domain' => (string) $_SERVER['HTTP_HOST']]);
        setcookie("myssession", "", ['expires' => $expire, 'path' => "/", 'domain' => (string) $_SERVER['HTTP_HOST']]);
        setcookie("mysactivity", "", ['expires' => $expire, 'path' => "/", 'domain' => (string) $_SERVER['HTTP_HOST']]);
        setcookie("mysloginattempt", "", ['expires' => $expire, 'path' => "/", 'domain' => (string) $_SERVER['HTTP_HOST']]);
        ob_flush();
        return true;
    }

    /**
     * The logincookies method, which evaluates the login attempt of a guest user.
     * @access public
     * @return void
     */
    public function logincookies($reset = false)
    {
        if (!$reset) $this->mysloginattempt++;
        else $this->mysloginattempt = 0;
        ob_start();
        $Month = 2592000 + time();
        setcookie("mysloginattempt", $this->mysloginattempt, ['expires' => $Month, 'path' => "/", 'domain' => (string) $_SERVER['HTTP_HOST']]);
        ob_flush();
    }

    /**
     * The loginAdminCookies method, which evaluates the login attempt from admin control panel.
     * @access public
     * @return void
     */
    public function loginAdminCookies($reset = false)
    {
        if (!$reset) $this->mysadmloginattempt++;
        else $this->mysadmloginattempt = 0;
        ob_start();
        $Month = 2592000 + time();
        setcookie("mysadmloginattempt", $this->mysadmloginattempt, ['expires' => $Month, 'path' => "/", 'domain' => (string) $_SERVER['HTTP_HOST']]);
        ob_flush();
    }
}
