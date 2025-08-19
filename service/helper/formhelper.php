<?php

namespace Service\Helper;

use Resource\Core\Registry;
use Resource\GUI\Component\Option;
use Resource\GUI\Container\DropdownList;

/**
 * The FormHelper Class, extends from abstract GUIHelper class.
 * It is a standard helper for tables to aid certain form construction operations.
 * @category Resource
 * @package GUI
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not much at this point.
 *
 */

class FormHelper extends GUIHelper
{
    /**
     * Constructor of FormHelper Class, which assigns basic helper properties
     * @access public
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * The execMethod method, returns the field content after executing the method.
     * @param String  $field
     * @access public
     * @return String
     */
    public function execMethod($field, $method)
    {
        if (!$this->resource->getParams()) {
            return $this->$method($field);
        } else {
            $params = $this->resource->getMethods->offsetGet($field);
            return $this->$method($field, $params);
        }
    }

    /**
     * The buildImageList method, builds a dropdown list for admin uploaded images.
     * @param String  $name
     * @access public
     * @return DropdownList
     */
    public function buildImageList($name)
    {
        $mysidia = Registry::get("mysidia");
        $imageList = new DropdownList($name);
        $stmt = $mysidia->db->select("filesmap", ["friendlyname", "wwwpath"], "1 ORDER BY id");
        $images = $mysidia->db->fetchMap($stmt);
        $imageList->add(new Option("Select an Image", "none"));
        $imageList->fill($images);
        return $imageList;
    }

    /**
     * The buildUsernameList method, builds a dropdown list for available usernames.
     * @param String  $name
     * @access public
     * @return DropdownList
     */
    public function buildUsernameList($name)
    {
        $mysidia = Registry::get("mysidia");
        $usernameList = new DropdownList($name);
        $stmt = $mysidia->db->select("users", ["username", "uid"], "1 ORDER BY uid");
        $usernames = $mysidia->db->fetchMap($stmt);
        $usernameList->add(new Option("Select a User...", ""));
        $usernameList->fill($usernames);
        return $usernameList;
    }

    /**
     * The buildAdoptTypeList method, builds a dropdown list for available adoptable types.
     * @param String  $name
     * @access public
     * @return DropdownList
     */
    public function buildAdoptTypeList($name)
    {
        $mysidia = Registry::get("mysidia");
        $typeList = new DropdownList($name);
        $stmt = $mysidia->db->select("adoptables", ["type", "id"], "1 ORDER BY id");
        $types = $mysidia->db->fetchMap($stmt);
        $typeList->add(new Option("Select an Adoptable...", "none"));
        $typeList->fill($types);
        return $typeList;
    }

    /**
     * The buildUsergroupList method, builds a dropdown list for available usergroups.
     * @param String  $name
     * @access public
     * @return DropdownList
     */
    public function buildUsergroupList($name)
    {
        $mysidia = Registry::get("mysidia");
        $groupList = new DropdownList($name);
        $stmt = $mysidia->db->select("groups", ["groupname", "gid"], "1 ORDER BY gid");
        $groups = $mysidia->db->fetchMap($stmt);
        $groupList->add(new Option("No Group Selected", "none"));
        $groupList->fill($groups);
        return $groupList;
    }

    /**
     * The buildAdoptShopList method, builds a dropdown list for available item shops.
     * @param String  $name
     * @access public
     * @return DropdownList
     */
    public function buildItemShopList($name)
    {
        $mysidia = Registry::get("mysidia");
        $shopList = new DropdownList($name);
        $stmt = $mysidia->db->select("shops", ["shopname", "sid"], "shoptype = 'itemshop' ORDER BY sid");
        $shops = $mysidia->db->fetchMap($stmt);
        $shopList->add(new Option("No Shop Selected", "none"));
        $shopList->fill($shops);
        return $shopList;
    }

    /**
     * The buildAdoptShopList method, builds a dropdown list for available adoptable shops.
     * @param String  $name
     * @access public
     * @return DropdownList
     */
    public function buildAdoptShopList($name)
    {
        $mysidia = Registry::get("mysidia");
        $shopList = new DropdownList($name);
        $stmt = $mysidia->db->select("shops", ["shopname", "sid"], "shoptype = 'adoptshop' ORDER BY sid");
        $shops = $mysidia->db->fetchMap($stmt);
        $shopList->add(new Option("No Shop Selected", "none"));
        $shopList->fill($shops);
        return $shopList;
    }

    /**
     * The buildItemNameList method, builds a dropdown list for available item names.
     * @param String  $name
     * @access public
     * @return DropdownList
     */
    public function buildItemNameList($name)
    {
        $mysidia = Registry::get("mysidia");
        $itemList = new DropdownList($name);
        $stmt = $mysidia->db->select("items", ["itemname", "id"], "1 ORDER BY id");
        $items = $mysidia->db->fetchMap($stmt);
        $itemList->add(new Option("Select an Item", "none"));
        $itemList->fill($items);
        return $itemList;
    }

    /**
     * The buildItemFunctionList method, builds a dropdown list for available item functions.
     * @param String  $name
     * @access public
     * @return DropdownList
     */
    public function buildItemFunctionList($name)
    {
        $mysidia = Registry::get("mysidia");
        $functionList = new DropdownList($name);
        $stmt = $mysidia->db->select("items_functions", ["function"], "1 ORDER BY ifid");
        $functions = $mysidia->db->fetchList($stmt);
        $functionList->fill($functions);
        return $functionList;
    }

    /**
     * The buildParentLinkList method, builds a dropdown list for available parent links.
     * @param String  $name
     * @access public
     * @return DropdownList
     */
    public function buildParentLinkList($name)
    {
        $mysidia = Registry::get("mysidia");
        $linkList = new DropdownList($name);
        $stmt = $mysidia->db->select("links", ["linktext", "id"], "1 ORDER BY id");
        $links = $mysidia->db->fetchMap($stmt);
        $linkList->add(new Option("No Link Selected", "none"));
        $linkList->fill($links);
        return $linkList;
    }

    /**
     * The buildThemeList method, builds a dropdown list for available themes.
     * @param String  $name
     * @access public
     * @return DropdownList
     */
    public function buildThemeList($name)
    {
        $mysidia = Registry::get("mysidia");
        $themeList = new DropdownList($name);
        $stmt = $mysidia->db->select("themes", ["themename", "themefolder"], "1 ORDER BY id");
        $themes = $mysidia->db->fetchMap($stmt);
        $themeList->add(new Option("Select a Theme", "none"));
        $themeList->fill($themes);
        return $themeList;
    }

    /**
     * The buildWidgetList method, builds a dropdown list for available widgets.
     * @param String  $name
     * @access public
     * @return DropdownList
     */
    public function buildWidgetList($name)
    {
        $mysidia = Registry::get("mysidia");
        $widgetList = new DropdownList($name);
        $stmt = $mysidia->db->select("widgets", ["name", "wid"], "1 ORDER BY wid");
        $widgets = $mysidia->db->fetchMap($stmt);
        $widgetList->add(new Option("Select a Widget", "none"));
        $widgetList->fill($widgets);
        return $widgetList;
    }

    /**
     * The buildParentWidgetList method, builds a dropdown list for available parent widgets for modules.
     * @param String  $name
     * @access public
     * @return DropdownList
     */
    public function buildParentWidgetList($name)
    {
        $mysidia = Registry::get("mysidia");
        $widgetList = new DropdownList($name);
        $stmt = $mysidia->db->select("widgets", ["name", "wid"], "wid > 3 ORDER BY wid");
        $widgets = $mysidia->db->fetchMap($stmt);
        $widgetList->fill($widgets);
        return $widgetList;
    }

    /**
     * Magic method __toString for FormHelper class, it reveals that the object is a form helper.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia FormHelper class.";
    }
}
