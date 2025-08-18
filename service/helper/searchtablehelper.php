<?php

namespace Service\Helper;
use Resource\Core\Registry;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;

/**
 * The SearchTableHelper Class, extends from the TableHelper class.
 * It is a specific helper for tables that involves operations for searches.
 * @category Service
 * @package Helper
 * @author Hall of Famer 
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not much at this point.
 *
 */

class SearchTableHelper extends TableHelper{

    /**
     * Constructor of SearchTableHelper Class, it simply serves as a wrap-up.
     * @access public
     * @return Void
     */
	public function __construct(){
	    parent::__construct();    
	}

	/**
     * The getUserProfile method, returns the user profile link.
     * @param int  $uid
     * @param String  $username
     * @access public
     * @return Link
     */
    public function getUserProfile($uid, $username){
		return new Link("profile/view/{$uid}", $username);	
    }

	/**
     * The getUsergroup method, retrieves the usergroup of this user.
     * @param int  $gid
     * @access public
     * @return String
     */
    public function getUsergroup($gid){
        $mysidia = Registry::get("mysidia");
        $usergroup = $mysidia->db->select("groups", ["groupname"], "gid = :gid", ["gid" => $gid])->fetchColumn();
		return $usergroup;	
    }

	/**
     * The getFriendRequest method, obtains the friend request link for the user
     * @param int  $uid
     * @access public
     * @return Link
     */
    public function getFriendRequest($uid){
        return new Link("friends/request/{$uid}", new Image("templates/icons/fr.gif"));
    }
	
	/**
     * The getTradeOffer method, obtains the trade offer link for the user
     * @param int  $uid
     * @access public
     * @return Link
     */
    public function getTradeOffer($uid){
        return new Link("trade/offer/user/{$uid}", new Image("templates/icons/trade.gif"));
    }	
	
	/**
     * The getAdoptName method, fetches the adoptable name with levelup link
	 * @param int $aid
     * @param String  $name
     * @access public
     * @return Link
     */
    public function getAdoptName($aid, $name){	
	    return new Link("levelup/click/{$aid}", $name);			
    }

	/**
     * The getGenderImage method, returns the gender image of an adoptable.
     * @param String  $gender
     * @access public
     * @return Image
     */
    public function getGenderImage($gender){
		return new Image("picuploads/{$gender}.png");			
    }
	
	/**
     * The getTradeStatus method, fetches a link or an 'N/A' string for trade.
	 * @param int  $aid
     * @param String  $tradestatus
     * @access public
     * @return Link|String
     */
    public function getTradeStatus($aid, $tradestatus){
		if($tradestatus == "fortrade") return new Link("trade/offer/adopt/{$aid}", new Image("templates/icons/trade.gif"));
		else return "N/A";
    }

	/**
     * The getShopLink method, fetches a link or an 'N/A' string for the itemshop.
     * @param String  $shop
     * @access public
     * @return Link|String
     */
    public function getShopLink($shop){
		if(!empty($shop)) return new Link("shop/browse/{$shop}", new Image("templates/icons/next.gif"));
		else return "N/A";
    }
		
	/**
     * Magic method __toString for SearchTableHelper class, it reveals that the object is a search table helper.
     * @access public
     * @return String
     */
    public function __toString(){
	    return "This is an instance of Mysidia SearchTableHelper class.";
	}    
} 