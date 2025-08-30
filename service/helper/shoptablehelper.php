<?php

namespace Service\Helper;

use Model\DomainModel\Adoptable;
use Model\DomainModel\Adoptshop;
use Model\DomainModel\Item;
use Model\DomainModel\Itemshop;
use Model\DomainModel\Shop;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Component\TextField;
use Service\Builder\FormBuilder;

/**
 * The ShopTableHelper Class, extends from the TableHelper class.
 * It is a specific helper for tables that involves operations on shops.
 * @category Service
 * @package Helper
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not much at this point.
 *
 */

class ShopTableHelper extends TableHelper
{
    /**
     * The getSalestax method, formats and retrieves the salestax of a shop.
     * @param int  $salestax
     * @access public
     * @return String
     */
    public function getSalestax($salestax)
    {
        return "{$salestax}%";
    }

    /**
     * The getShopstatus method, returns the shop status with an enter link or a closed message.
     * @param Shop  $shop
     * @access public
     * @return Link|String
     */
    public function getShopStatus(Shop $shop)
    {
        if ($shop->isOpen()) {
            return new Link("shop/browse/{$shop->getID()}", new Image("templates/icons/next.gif"));
        } else {
            return "Closed";
        }
    }

    /**
     * The getItemPurchaseForm method, constructs a buy form for an itemshop table.
     * @param Itemshop  $shop
     * @param Item  $item
     * @access public
     * @return Form
     */
    public function getItemPurchaseForm(Itemshop $shop, Item $item)
    {
        $buyForm = new FormBuilder("buyform", "../purchase/{$shop->getID()}", "post");
        $buyForm->setLineBreak(false);
        $buyForm->buildComment("<br>")
                ->buildPasswordField("hidden", "action", "purchase")
                ->buildPasswordField("hidden", "itemid", $item->getID())
                ->buildPasswordField("hidden", "itemname", $item->getItemname())
                ->buildPasswordField("hidden", "shoptype", "itemshop");

        $quantity = new TextField("quantity");
        $quantity->setSize(3);
        $quantity->setMaxLength(3);
        $quantity->setLineBreak(true);

        $buy = new Button("Buy", "buy", "buy");
        $buy->setLineBreak(false);

        $buyForm->add($quantity);
        $buyForm->add($buy);
        return $buyForm;
    }

    /**
     * The getAdoptPurchaseForm method, constructs a purchase form for an adoptshop table.
     * @param Adoptshop  $shop
     * @param Adoptable  $adopt
     * @access public
     * @return Form
     */
    public function getAdoptPurchaseForm(Adoptshop $shop, Adoptable $adopt)
    {
        $buyForm = new FormBuilder("buyform", "../purchase/{$shop->getID()}", "post");
        $buyForm->setLineBreak(false);
        $buyForm->buildComment("<br>")
                ->buildPasswordField("hidden", "action", "purchase")
                ->buildPasswordField("hidden", "adoptid", $adopt->getID())
                ->buildPasswordField("hidden", "adopttype", $adopt->getType())
                ->buildPasswordField("hidden", "shoptype", "adoptshop");

        $buy = new Button("Buy", "buy", "buy");
        $buy->setLineBreak(false);
        $buyForm->add($buy);
        return $buyForm;
    }

    /**
     * Magic method __toString for ShopTableHelper class, it reveals that the object is a shop table helper.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia ShopTableHelper class.";
    }
}
