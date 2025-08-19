<?php

namespace Model\Settings;

use Resource\Core\Database;
use Resource\Core\Settings;

/**
 * The TradeSettings Class, extending from the abstract Settings class.
 * It acts as a wrapper for all settings for the trade system.
 * TradeSetting is a final class, no child class shall derive from it.
 * @category Model
 * @package Settings
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not much at this point.
 */

final class TradeSettings extends Settings
{
    /**
     * The system property, defines if the trade system is enabled.
     * @access public
     * @var String
     */
    public $system;

    /**
     * The multiple property, defines if multi-adopts and multi-item trades are allowed.
     * @access public
     * @var String
     */
    public $multiple;

    /**
     * The partial property, specifies whether partial/incomplete trade offers are permitted to submit.
     * @access public
     * @var String
     */
    public $partial;

    /**
     * The public property, specifies whether public trade offers in trade stations can be made/seen.
     * @access public
     * @var String
     */
    public $public;

    /**
     * The species property, stores a list of adoptables species not available to trade.
     * @access public
     * @var Array
     */
    public $species;

    /**
     * The interval property, the number of hours successive trade offers can be made.
     * @access public
     * @var int
     */
    public $interval;

    /**
     * The number property, the maximum number of adoptables/items that can be involved in one trade offer.
     * @access public
     * @var int
     */
    public $number;

    /**
     * The duration property, the number of days when the trade offers expire.
     * @access public
     * @var int
    */
    public $duration;

    /**
     * The tax property, the amount of money need to make a trade offer.
     * @access public
     * @var int
     */
    public $tax;

    /**
     * The usergroup property, the usergroup(s) allowed to use trade station.
     * The default value is 'all', which means no limitation on usergroup.
     * @access public
     * @var String|Array
     */
    public $usergroup;

    /**
     * The item property, the item(s) certificate required to make trade offers.
     * @access public
     * @var String|Array
     */
    public $item;

    /**
     * The moderate property, specifies whether trade offers are moderated by admins/mods.
     * @access public
     * @var String
     */
    public $moderate;

    /**
     * Constructor of TradeSettings Class, it initializes basic setting parameters.
     * @param Database  $db
     * @access public
     * @return void
     */
    public function __construct(Database $db)
    {
        parent::__construct($db);
        if ($this->species) {
            $this->species = explode(",", $this->species);
        }
        if ($this->usergroup != "all") {
            $this->usergroup = explode(",", $this->usergroup);
        }
        if ($this->item) {
            $this->item = explode(",", $this->item);
        }
    }

    /**
     * The fetch method, returns all fields of TradeSettings object by fetching information from database.
     * @access public
     * @return void
     */
    public function fetch($db)
    {
        $stmt = $db->select("trade_settings", []);
        while ($row = $stmt->fetchObject()) {
            $property = $row->name;
            $this->$property = $row->value;
        }
    }

    /**
     * The set method, set a field of TradeSettings object with a specific value.
     * @param String  $property
     * @param String|Number  $value
     * @access public
     * @return void
     */
    public function set($property, $value)
    {
        $this->$property = $value;
    }
}
