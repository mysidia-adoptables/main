<?php

namespace Service\Helper;

use Model\DomainModel\OwnedItem;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\Form;
use Service\Builder\FormBuilder;

/**
 * The ItemTableHelper Class, extends from the TableHelper class.
 * It is a specific helper for tables that involves operations on items.
 * @category Service
 * @package Helper
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.3
 * @todo Not much at this point.
 *
 */
class ItemTableHelper extends TableHelper
{
    /**
     * The getUseForm method, constructs a use form for the inventory table.
     * @param OwnedItem $item
     * @access public
     * @return Form|String
     */
    public function getUseForm(OwnedItem $item)
    {
        if (!$item->isConsumable()) {
            return "N/A";
        }
        $useForm = new FormBuilder("useform", "inventory/uses", "post");
        $useForm->setLineBreak(false);
        $useForm->buildComment("")
            ->buildPasswordField("hidden", "action", "uses")
            ->buildPasswordField("hidden", "item", $item->getItemID())
            ->buildButton("Use", "use", "use");
        return $useForm;
    }

    /**
     * The getSellForm method, constructs a sell form for the inventory table.
     * @param OwnedItem $item
     * @access public
     * @return Form|String
     */
    public function getSellForm(OwnedItem $item)
    {
        if ($item->getCategory() == "Key Items") {
            return "N/A";
        }
        $sellForm = new FormBuilder("sellform", "inventory/sell", "post");
        $sellForm->setLineBreak(false);
        $sellForm->buildComment("")
            ->buildPasswordField("hidden", "action", "sell")
            ->buildPasswordField("hidden", "item", $item->getItemID());

        $quantity = new TextField("quantity");
        $quantity->setSize(3);
        $quantity->setMaxLength(3);
        $quantity->setLineBreak(false);

        $sell = new Button("Sell", "sell", "sell");
        $sell->setLineBreak(false);

        $sellForm->add($quantity);
        $sellForm->add($sell);
        return $sellForm;
    }

    /**
     * The getTossForm method, constructs a toss form for the inventory table.
     * @param OwnedItem $item
     * @access public
     * @return Form|String
     */
    public function getTossForm(OwnedItem $item)
    {
        if ($item->getCategory() == "Key Items") {
            return "N/A";
        }
        $tossForm = new FormBuilder("tossform", "inventory/toss", "post");
        $tossForm->setLineBreak(false);
        $tossForm->buildComment("")
            ->buildPasswordField("hidden", "action", "toss")
            ->buildPasswordField("hidden", "item", $item->getItemID())
            ->buildButton("Toss", "toss", "toss");
        return $tossForm;
    }

    /**
     * Magic method __toString for ItemTableHelper class, it reveals that the object is an item table helper.
     * @access public
     * @return String
     */
    public function __toString(): string
    {
        return "This is an instance of Mysidia ItemTableHelper class.";
    }
}
