<?php

namespace Model\ViewModel;

use Model\DomainModel\Widget;
use Resource\Core\Registry;
use Resource\GUI\Component;
use Resource\GUI\Component\Link;
use Resource\GUI\Container\LinksList;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Division;
use Resource\GUI\Document\Paragraph;
use Service\Builder\FormBuilder;

/**
 * The Sidebar Class, defines a standard HTML Sidebar component.
 * It extends from the WidgetViewModel class, while adding its own implementation.
 * @category Model
 * @package ViewModel
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not much at this point.
 *
 */

class Sidebar extends WidgetViewModel
{
    /**
     * The moneyBar property, specifies the money/donation bar for members.
     * @access protected
     * @var Paragraph
     */
    protected $moneyBar;

    /**
     * The linksBar property, stores all useful links for members.
     * @access protected
     * @var Paragraph
     */
    protected $linksBar;

    /**
     * The wolBar property, determines the who's online url in the sidebar.
     * @access protected
     * @var Link
     */
    protected $wolBar;

    /**
     * The loginBar property, specifies the loginBar for guests.
     * @access protected
     * @var FormBuilder
     */
    protected $loginBar;


    /**
     * Constructor of Sidebar Class, it initializes basic sidebar properties
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct(new Widget("sidebar"));
    }

    /**
     * The setDivision method, setter method for property $division.
     * It is set internally upon object instantiation, cannot be accessed in client code.
     * @param Component  $module
     * @access protected
     * @return Void
     */
    protected function setDivision(Component $module)
    {
        if (!$this->division) {
            $this->division = new Division();
            $this->division->setClass("sidebar");
        }
        $this->division->add($module);
    }

    /**
     * The getMoneyBar method, getter method for property $moneyBar.
     * @access public
     * @return Paragraph
     */
    public function getMoneyBar()
    {
        return $this->moneyBar;
    }

    /**
     * The setMoneyBar method, setter method for property $moneyBar.
     * It is set internally upon object instantiation, cannot be accessed in client code.
     * @access protected
     * @return Void
     */
    protected function setMoneyBar()
    {
        $mysidia = Registry::get("mysidia");
        $this->moneyBar = new Paragraph();
        $this->moneyBar->add(new Comment("You have {$mysidia->user->getMoney()} {$mysidia->settings->cost}."));

        $donate = new Link("donate");
        $donate->setText("Donate Money to Friends");
        $this->moneyBar->add($donate);
        $this->setDivision($this->moneyBar);
    }

    /**
     * The getLinksBar method, getter method for property $linksBar.
     * @access public
     * @return Paragraph
     */
    public function getLinksBar()
    {
        return $this->linksBar;
    }

    /**
     * The setLinksBar method, setter method for property $linksBar.
     * It is set internally upon object instantiation, cannot be accessed in client code.
     * @access protected
     * @return Void
     */
    protected function setLinksBar()
    {
        $mysidia = Registry::get("mysidia");
        $this->linksBar = new Paragraph();
        $linkTitle = new Comment("{$mysidia->user->getUsername()}'s Links:");
        $linkTitle->setBold();
        $this->linksBar->add($linkTitle);

        $linksList = new LinksList("ul");
        $this->setLinks($linksList);

        $this->linksBar->add($linksList);
        $this->setDivision($this->linksBar);
    }

    /**
     * The setLinks method, append all links to the LinksBar.
     * It is set internally upon object instantiation, cannot be accessed in client code.
     * @access protected
     * @return Void
     */
    protected function setLinks(LinksList $linksList)
    {
        $mysidia = Registry::get("mysidia");
        $stmt = $mysidia->db->select("links", ["id", "linktext", "linkurl"], "linktype = 'sidelink' ORDER BY linkorder");
        if ($stmt->rowCount() == 0) {
            throw new Exception("There is an error with sidebar links, please contact the admin immediately for help.");
        }

        while ($sideLink = $stmt->fetchObject()) {
            $link = new Link($sideLink->linkurl);
            $link->setText($sideLink->linktext);
            if ($sideLink->linkurl == "messages") {
                $num = $mysidia->db->select("messages", ["touser"], "touser = '{$mysidia->user->getID()}' AND status = 'unread'")->rowCount();
                if ($num > 0) {
                    $link->setText("<b>{$link->getText()} ({$num})</b>");
                }
            }
            $link->setListed(true);
            $linksList->add($link);
        }

        if ($mysidia->user->isAdmin()) {
            $adminCP = new Link("admincp/", false, false);
            $adminCP->setText("Admin Control Panel");
            $adminCP->setListed(true);
            $linksList->add($adminCP);
        }
    }

    /**
     * The getWolBar method, getter method for property $wolBar.
     * @access public
     * @return LinksList
     */
    public function getWolBar()
    {
        return $this->wolBar;
    }

    /**
     * The setWolBar method, setter method for property $wolBar.
     * It is set internally upon object instantiation, cannot be accessed in client code.
     * @access protected
     * @return void
     */
    protected function setWolBar()
    {
        $mysidia = Registry::get("mysidia");
        $this->wolBar = new Link("online");
        $totalMembers = $mysidia->db->select("online", [], "username != 'Guest'")->rowCount();
        $totalGuests = $mysidia->db->select("online", [], "username = 'Guest'")->rowCount();
        $this->wolBar->setText("This site has {$totalMembers} members and {$totalGuests} guests online.");
        $this->setDivision($this->wolBar);
    }

    /**
     * The getLoginBar method, getter method for property $loginBar.
     * @access public
     * @return FormBuilder
     */
    public function getLoginBar()
    {
        return $this->loginBar;
    }

    /**
     * The setLoginBar method, setter method for property $loginBar.
     * It is set internally upon object instantiation, cannot be accessed in client code.
     * @access protected
     * @return Void
     */
    protected function setLoginBar()
    {
        $this->loginBar = new FormBuilder("login", "login", "post");
        $loginTitle = new Comment("Member Login:");
        $loginTitle->setBold();
        $loginTitle->setUnderlined();
        $this->loginBar->add($loginTitle);

        $this->loginBar->buildComment("username: ", false)
                       ->buildTextField("username")
                       ->buildComment("password: ", false)
                       ->buildPasswordField("password", "password", "", true)
                       ->buildButton("Log In", "submit", "submit")
                       ->buildComment("Don't have an account?");

        $register = new Link("register");
        $register->setText("Register New Account");
        $register->setLineBreak(true);
        $forgot = new Link("forgotpass");
        $forgot->setText("Forgot Password?");

        $this->loginBar->add($register);
        $this->loginBar->add($forgot);
        $this->setDivision($this->loginBar);
    }
}
