<?php

namespace Resource\Core;

/**
 * The GlobalSettings Class, extending from the abstract Settings class.
 * It acts as a wrapper for all basic settings available throughout the site.
 * An instance of GlobalSettings class is generated upon Mysidia system object's creation.
 * This specific instance is available from Registry, just like any other Mysidia core objects.
 * Similar to class File and Database, it does not extend abstract Core class.
 * GlobalSetting is a final class, no child class shall derive from it.
 * @category Resource
 * @package Core
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.2
 * @todo Re-structure database table prefix_settings.
 */
final class GlobalSettings extends Settings
{
    /**
     * The theme property, contains information of default theme.
     * @access public
     * @var String
     */
    public $theme;

    /**
     * The browsetitle property, stores information of browser title.
     * @access public
     * @var String
     */
    public $browsertitle;

    /**
     * The sitename property, from which site name is readily available.
     * @access public
     * @var String
     */
    public $sitename;

    /**
     * The admincontact property, holds admin contact email info.
     * @access public
     * @var String
     */
    public $admincontact;

    /**
     * The slogan property, an interesting var for any adoptables site.
     * @access public
     * @var String
     */
    public $slogan;

    /**
     * The peppercode property, a site-specific encryption parameter.
     * This property is now deprecated and will be removed in Mys v1.4.0 with new password hashing algorithm.
     * @access public
     * @var String
     */
    public $peppercode;

    /**
     * The securityquestion property, which contains the information for site's security question.
     * @access public
     * @var String
     */
    public $securityquestion;

    /**
     * The securityanswer property, the correct answer to site's security question.
     * @access public
     * @var String
     */
    public $securityanswer;

    /**
     * The gdimage property, specifies whether gdimages are enabled.
     * @access public
     * @var bool
     */
    public $gdimages;

    /**
     * The usealtbbcode property, determines if alternative bbcode is allowed.
     * @access public
     * @var bool
     */
    public $usealtbbcode;

    /**
     * The systememail property, stores information of system email.
     * @access public
     * @var String
     */
    public $systememail;

    /**
     * The systemuser property, stores information of system user.
     * @access public
     * @var String
     */
    public $systemuser;

    /**
     * The cashenabled property, reveals whether cash system is enabled.
     * @access public
     * @var bool
     */
    public $cashenabled;

    /**
     * The cost property, displays the currency name used on the site.
     * @access public
     * @var String
     */
    public $cost;

    /**
     * The startmoney property, defines how much money a newly registered user begin with.
     * @access public
     * @var double
     */
    public $startmoney;

    /**
     * The pagination property, specifies how many rows to display for paginated views.
     * @access public
     * @var int
     */
    public $pagination;

    /**
     * The rewardmoney property, defines how much money is rewarded for users clicking adoptables.
     * This field is a string that contains a concatenation of minimum and maximum amount of rewarded cash.
     * @access public
     * @var String
     */
    public $rewardmoney;

    /**
     * The enabletrades property, shows whether trade system is enabled.
     * @access public
     * @var bool
     */
    public $enabletrades;

    /**
     * The tradecost property, specifies the amount of tax paid for a trade transaction.
     * @access public
     * @var double
     */
    public $tradecost;

    /**
     * The tradeoffercost property, determines the amount of tax paid for making a trade offer.
     * @access public
     * @var Double
     */
    public $tradeoffercost;

    /**
     * Constructor of GlobalSettings Class, it initializes basic setting parameters.
     * @param Database $db
     * @access public
     * @return void
     */
    public function __construct(Database $db)
    {
        parent::__construct($db);
    }

    /**
     * The fetch method, returns all fields of GlobalSettings object by fetching information from database.
     * @access public
     * @return void
     */
    public function fetch($db)
    {
        $stmt = $db->select("settings");
        while ($row = $stmt->fetchObject()) {
            $property = $row->name;
            $this->$property = $row->value;
        }
    }

    /**
     * The set method, set a field of GlobalSettings object with a specific value.
     * @param String $property
     * @param String|Number $value
     * @access public
     * @return void
     */
    public function set($property, $value)
    {
        $this->$property = $value;
    }
}
