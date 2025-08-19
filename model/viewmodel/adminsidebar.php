<?php

namespace Model\ViewModel;

use Resource\Collection\ArrayList;
use Resource\Core\Initializable;
use Resource\GUI\Component\Link;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Division;

/**
 * The AdminSidebar Class, defines a unique Admin Control Panel Sidebar.
 * It extends from the Sidebar class, although it does not really have much to do with the parent class.
 * @category Model
 * @package ViewModel
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not much at this point.
 *
 */
class AdminSidebar extends WidgetViewModel implements Initializable
{

    /**
     * Constructor of AdminSidebar Class, it initializes basic sidebar properties
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * The setDivisions method, setter method for property $division.
     * It is set internally upon object instantiation, cannot be accessed in client code.
     * @param ArrayList $components
     * @access protected
     * @return void
     */
    protected function setDivisions($components)
    {
        $this->division = new Division($components);
        $this->division->setClass("sidebar");
    }

    /**
     * The initialize method, sets up the entire admin sidebar.
     * @access public
     * @return void
     */
    public function initialize()
    {
        $components = new ArrayList;
        $components->add(new Division(new Link("admincp", "Dashboard")));

        $components->add(new Division(new Comment("Adoptable", false)));
        $adoptable = new Division;
        $adoptable->add(new Link("admincp/adopt/add", "Create New Adoptables"));
        $adoptable->add(new Link("admincp/adopt/edit", "Edit Existing Adoptables"));
        $components->add($adoptable);

        $components->add(new Division(new Comment("Adopt Levels", false)));
        $level = new Division;
        $level->add(new Link("admincp/level/add", "Add Levels"));
        $level->add(new Link("admincp/level/edit", "Edit Levels"));
        $level->add(new Link("admincp/level/delete", "Delete Levels"));
        $level->add(new Link("admincp/level/settings", "Level Settings"));
        $level->add(new Link("admincp/level/daycare", "Daycare Settings"));
        $components->add($level);

        $components->add(new Division(new Comment("Adopt Alternate Forms", false)));
        $alternate = new Division;
        $alternate->add(new Link("admincp/alternate/add", "Add Alternate Forms"));
        $alternate->add(new Link("admincp/alternate/edit", "Edit Alternate Forms"));
        $alternate->add(new Link("admincp/alternate/delete", "Delete Alternate Forms"));
        $components->add($alternate);

        $components->add(new Division(new Comment("Owned Adoptables", false)));
        $ownedAdoptable = new Division;
        $ownedAdoptable->add(new Link("admincp/ownedadopt/add", "Give Adopt to User"));
        $ownedAdoptable->add(new Link("admincp/ownedadopt/edit", "Manage Users Adopts"));
        $ownedAdoptable->add(new Link("admincp/ownedadopt/delete", "Delete Users Adopts"));
        $components->add($ownedAdoptable);

        $components->add(new Division(new Comment("Breeding", false)));
        $breeding = new Division;
        $breeding->add(new Link("admincp/breeding/add", "Create new Breed Adopt"));
        $breeding->add(new Link("admincp/breeding/edit", "Update Existing Breed Adopt"));
        $breeding->add(new Link("admincp/breeding/delete", "Delete Breed Adopt"));
        $breeding->add(new Link("admincp/breeding/settings", "Change Breeding Settings"));
        $components->add($breeding);

        $components->add(new Division(new Comment("Images", false)));
        $image = new Division;
        $image->add(new Link("admincp/image/upload", "Upload Images"));
        $image->add(new Link("admincp/image/delete", "Erase Images"));
        $image->add(new Link("admincp/image/settings", "Adoptable Signature Image/GD Settings"));
        $components->add($image);

        $components->add(new Division(new Comment("Users", false)));
        $users = new Division;
        $users->add(new Link("admincp/user/edit", "Manage Users"));
        $users->add(new Link("admincp/user/delete", "Delete Users"));
        $components->add($users);

        $components->add(new Division(new Comment("Usergroups", false)));
        $usergroups = new Division;
        $usergroups->add(new Link("admincp/usergroup/add", "Add Usergroup"));
        $usergroups->add(new Link("admincp/usergroup/edit", "Edit Usergroup"));
        $usergroups->add(new Link("admincp/usergroup/delete", "Delete Usergroup"));
        $components->add($usergroups);

        $components->add(new Division(new Comment("Items", false)));
        $items = new Division;
        $items->add(new Link("admincp/item/add", "Create an Item"));
        $items->add(new Link("admincp/item/edit", "Manage Items"));
        $items->add(new Link("admincp/item/delete", "Delete Items"));
        $items->add(new Link("admincp/item/functions", "Browse Item Functions"));
        $components->add($items);

        $components->add(new Division(new Comment("Inventory", false)));
        $inventory = new Division;
        $inventory->add(new Link("admincp/inventory/add", "Give Item to User"));
        $inventory->add(new Link("admincp/inventory/edit", "Edit User Inventory"));
        $inventory->add(new Link("admincp/inventory/delete", "Delete Users items"));
        $components->add($inventory);

        $components->add(new Division(new Comment("Shops", false)));
        $shops = new Division;
        $shops->add(new Link("admincp/shop/add", "Add a Shop"));
        $shops->add(new Link("admincp/shop/edit", "Edit Shops"));
        $shops->add(new Link("admincp/shop/delete", "Delete Shops"));
        $components->add($shops);

        $components->add(new Division(new Comment("Trade", false)));
        $trades = new Division;
        $trades->add(new Link("admincp/trade/add", "Create a Trade"));
        $trades->add(new Link("admincp/trade/edit", "Update Trades"));
        $trades->add(new Link("admincp/trade/delete", "Remove Trades"));
        $trades->add(new Link("admincp/trade/moderate", "Moderate Trades"));
        $trades->add(new Link("admincp/trade/settings", "Change Trade Settings"));
        $components->add($trades);

        $components->add(new Division(new Comment("Content", false)));
        $content = new Division;
        $content->add(new Link("admincp/content/add", "Add a Custom Page"));
        $content->add(new Link("admincp/content/edit", "Edit Custom Pages"));
        $content->add(new Link("admincp/content/delete", "Delete Custom Pages"));
        $components->add($content);

        $components->add(new Division(new Comment("Module", false)));
        $module = new Division;
        $module->add(new Link("admincp/module/add", "Create new Module"));
        $module->add(new Link("admincp/module/edit", "Edit Modules"));
        $module->add(new Link("admincp/module/delete", "Delete Modules"));
        $components->add($module);

        $components->add(new Division(new Comment("Widget", false)));
        $widget = new Division;
        $widget->add(new Link("admincp/widget/add", "Create new Widget"));
        $widget->add(new Link("admincp/widget/edit", "Edit Widgets"));
        $widget->add(new Link("admincp/widget/delete", "Delete Widgets"));
        $components->add($widget);

        $components->add(new Division(new Comment("Links", false)));
        $links = new Division;
        $links->add(new Link("admincp/links/add", "Add a link"));
        $links->add(new Link("admincp/links/edit", "Edit a link"));
        $links->add(new Link("admincp/links/delete", "Delete a Link"));
        $components->add($links);

        $components->add(new Division(new Comment("Promocodes", false)));
        $promo = new Division;
        $promo->add(new Link("admincp/promo/add", "Create New Promocode"));
        $promo->add(new Link("admincp/promo/edit", "Edit Promocodes"));
        $promo->add(new Link("admincp/promo/delete", "Delete Promocodes"));
        $components->add($promo);

        $components->add(new Division(new Comment("Themes", false)));
        $theme = new Division;
        $theme->add(new Link("admincp/theme/add", "Add/Install New Theme"));
        $theme->add(new Link("admincp/theme/edit", "Update Themes"));
        $theme->add(new Link("admincp/theme/delete", "Delete Themes"));
        $theme->add(new Link("admincp/theme/css", "Additional CSS"));
        $components->add($theme);

        $components->add(new Division(new Comment("Settings", false)));
        $settings = new Division;
        $settings->add(new Link("admincp/settings/globals", "Basic Settings"));
        $settings->add(new Link("admincp/settings/system", "Subsystem Settings"));
        $settings->add(new Link("admincp/settings/pound", "Pound Settings"));
        $settings->add(new Link("admincp/settings/plugin", "View Plugins"));
        $settings->add(new Link("admincp/settings/forum", "Forum Integration"));
        $components->add($settings);

        $components->add(new Division(new Comment("Advertising", false)));
        $ads = new Division;
        $ads->add(new Link("admincp/ads/add", "Create New Ad"));
        $ads->add(new Link("admincp/ads/edit", "Edit Current Campaigns"));
        $ads->add(new Link("admincp/ads/delete", "Delete Existing Campaigns"));
        $components->add($ads);
        $this->setDivisions($this->addClass($components));
    }

    /**
     * The addClass method, loops through the components array and add classes for each component.
     * It is set internally upon object instantiation, cannot be accessed in client code.
     * @param ArrayList $components
     * @access protected
     * @return ArrayList
     */
    protected function addClass(ArrayList $components)
    {
        $components->get(0)->setClass("accordionButton");
        for ($i = 1; $i < $components->size(); $i += 2) {
            $components->get($i)->setClass("accordionButton");
            $components->get($i + 1)->setClass("accordionContent");
        }
        return $components;
    }
}
