<?php

namespace Model\ViewModel;

use Model\DomainModel\Advertisement;
use Model\DomainModel\Widget;
use Resource\Core\Mysidia;
use Resource\Core\Registry;
use Resource\GUI\Component;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Division;
use Resource\GUI\Document\Paragraph;

/**
 * The Footer Class, defines a standard HTML footer component.
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
class Footer extends WidgetViewModel
{
    /**
     * The ads property, specifies the advertisement block on the footer.
     * @access protected
     * @var Division
     */
    protected $ads;

    /**
     * The credits property, stores the credits content of the site.
     * @access protected
     * @var Paragraph
     */
    protected $credits;

    /**
     * Constructor of Footer Class, it initializes basic footer properties
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct(new Widget("footer"));
    }

    /**
     * The setDivision method, setter method for property $division.
     * It is set internally upon object instantiation, cannot be accessed in client code.
     * @param Component $module
     * @access protected
     * @return void
     */
    protected function setDivision(Component $module)
    {
        if (!$this->division) {
            $this->division = new Division();
            $this->division->setClass("footer");
        }
        $this->division->add($module);
    }

    /**
     * The getAds method, getter method for property $ads.
     * @access public
     * @return Paragraph
     */
    public function getAds()
    {
        return $this->ads;
    }

    /**
     * The setAds method, setter method for property $ads.
     * It is set internally upon object instantiation, cannot be accessed in client code.
     * @access protected
     * @return Void
     */
    protected function setAds()
    {
        $mysidia = Registry::get("mysidia");
        $this->ads = new Division();
        $page = $mysidia->file->getBasename();
        $text = "";
        $dto = $mysidia->db->select("ads", [], "page = '{$page}' AND status = 'active' ORDER BY RAND() LIMIT 1")->fetchObject();

        if (is_object($dto)) {
            $ad = new Advertisement($dto->id, $dto);
            $text = stripslashes((string) $ad->getText());
            $ad->updateImpressions();
        }
        $this->ads->add(new Comment($text));
        $this->setDivision($this->ads);
    }

    /**
     * The getCredits method, getter method for property $credits.
     * @access public
     * @return Paragraph
     */
    public function getCredits()
    {
        return $this->credits;
    }

    /**
     * The setCredits method, setter method for property $credits.
     * It is set internally upon object instantiation, cannot be accessed in client code.
     * @access protected
     * @return Void
     */
    protected function setCredits()
    {
        $this->credits = new Paragraph();
        $this->credits->add(new Comment("&#9733; Powered by ", false));
        $creditsLink = new Component\Link("http://www.mysidiaadoptables.com");
        $creditsLink->setText("Mysidia Adoptables v" . Mysidia::version);
        $this->credits->add($creditsLink);
        $this->credits->add(new Comment("&#9733;"));
        $this->credits->add(new Comment("Copyright &copy; 2011-2021 Mysidia RPG, Inc. All rights reserved."));
        $this->setDivision($this->credits);
    }
}
