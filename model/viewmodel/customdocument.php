<?php

namespace Model\ViewModel;

use ArrayObject;
use Model\DomainModel\Content;
use Model\DomainModel\OwnedItem;
use Model\DomainModel\Promocode;
use Resource\Core\Initializable;
use Resource\Core\Registry;
use Resource\Exception\NoPermissionException;
use Resource\GUI\Component;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Division;
use Resource\Utility\Date;
use Service\Validator\PromocodeValidator;

class CustomDocument extends WidgetViewModel implements Initializable
{

    /**
     * Constructor of Menu Class, it initializes basic custom document properties.
     * @param Content $content
     * @access public
     * @return void
     */
    public function __construct(Content $content)
    {
        $this->model = $content;
        $this->initialize();
    }

    /**
     * The initialize method, which handles advanced properties that cannot be initialized without a menu object.
     * It should only be called upon object instantiation, otherwise an exception will be thrown.
     * @access public
     * @return void
     */
    public function initialize()
    {
        $this->checkAccess();
        $this->setDivision(new Comment($this->getContent()));
    }

    /**
     * The setDivision method, setter method for property $division.
     * It is set internally upon object instantiation, cannot be accessed in client code.
     * @param GUIComponent $document
     * @access protected
     * @return Void
     */
    protected function setDivision(Component $document)
    {
        $this->division = new Division;
        $this->division->setName("document");
        $this->division->add($document);
    }

    /**
     * The getPage method, find the page name of this document.
     * @access public
     * @return String
     */
    public function getPage()
    {
        return $this->model->getPage();
    }

    /**
     * The getTitle method, obtain the title of this document.
     * @access public
     * @return String
     */
    public function getTitle()
    {
        return $this->model->getTitle();
    }

    /**
     * The getDate method, getter method for property $date.
     * @access public
     * @return String
     */
    public function getDate()
    {
        return $this->model->getDate();
    }

    /**
     * The getContent method, obtain the rendered content of this document.
     * @access public
     * @return String
     */
    public function getContent()
    {
        return $this->model->getContent();
    }

    /**
     * The render method for CustomDocument class, it renders the division component and thus all subcomponents.
     * @access public
     * @return String
     */
    public function render()
    {
        return $this->division->render();
    }

    protected function checkAccess()
    {
        if (!$this->model->hasDisplayConditions()) return true;
        $mysidia = Registry::get("mysidia");
        if ($this->model->getCode()) {
            if ($mysidia->input->post("promocode")) {
                // A promocode has been entered, now process the request.
                $code = $this->model->getCode();
                if ($code != $mysidia->input->post("promocode")) throw new NoPermissionException("wrongcode");

                $promo = new Promocode($code);
                $validator = new PromocodeValidator($promo, new ArrayObject(["user", "number", "date"]));
                $validator->validate();
                // Now execute the action of using this promocode.
                if (!$mysidia->input->post("item")) $promo->execute();
            } else {
                // Show a basic form for user to enter promocode.
                $promoform = "<br><form name='form1' method='post' action='{$this->getPage()}'><p>Your Promo Code: 
                              <input name='promocode' type='text' id='promocode'></p>
                              <p><input type='submit' name='submit' value='Enter Code'></p></form>";
                $this->model->updateDocument($mysidia->lang->code_title, $mysidia->lang->code . $promoform);
                return false;
            }
        }
        if ($this->model->getTime()) {
            $time = $this->model->getTime();
            $current = new Date;
            if ($current < $time) throw new NoPermissionException("wrongtime");
        }
        if ($this->model->getGroup()) {
            $group = $mysidia->user->getUsergroup();
            if ($group != $this->model->getGroup()) throw new NoPermissionException("notgroup");
        }
        if ($this->model->getItem()) {
            if ($mysidia->input->post("item")) {
                // An item has been selected, now process the request.
                $itemID = $this->model->getItem();
                if ($mysidia->input->post("item") != $itemID) throw new NoPermissionException("wrongitem");

                $item = new OwnedItem($itemID, $mysidia->user->getID());
                if ($item->getQuantity() < 1) throw new NoPermissionException("noitem");

                // Consume one quantity of this item if it is consumable.
                if ($item->isConsumable()) $item->remove();
            } else {
                // Show a basic form for user to choose an item.
                $itemform = "<form name='form1' method='post' action='{$this->getPage()}'><br>
                             <b>Choose an Item:</b>
                             (The quantity of each item you own is shown inside the parentheses)<br> 
                             <select name='item' id='item'><option value='none' selected>None Selected</option>";
                $stmt = $mysidia->db->join("items", "items.id = inventory.item")
                    ->select("inventory", ["id", "itemname", "quantity"], "owner = '{$mysidia->user->getID()}'");
                while ($items = $stmt->fetchObject()) {
                    $itemform .= "<option value='{$items->id}'>{$items->itemname} ({$items->quantity})</option>";
                }
                $itemform .= "</select></p><input name='promocode' type='hidden' id='promocode' value='{$this->model->getCode()}'>
                              <p><input type='submit' name='submit' value='Use Item'></p></form>";

                $this->model->updateDocument($mysidia->lang->item_title, $mysidia->lang->item . $itemform);
                return false;
            }
        }
    }
}
