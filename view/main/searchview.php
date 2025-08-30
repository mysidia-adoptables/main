<?php

namespace View\Main;

use Resource\Collection\LinkedList;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Link;
use Resource\GUI\Component\Option;
use Resource\GUI\Container\DropdownList;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\SearchTableHelper;

class SearchView extends View
{
    public function index()
    {
        $document = $this->document;
        $document->setTitle($this->lang->title);
        $document->addLangvar($this->lang->default);
        $document->add(new Link("search/user", "Search for Users", true));
        $document->add(new Link("search/adopt", "Search for Adoptables", true));
        $document->add(new Link("search/item", "Search for Items", true));
        $document->add(new Link("search/page", "Search for Pages(Not available now)", true));
    }

    public function user()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $document->setTitle($this->lang->user);

        if ($mysidia->input->post("submit")) {
            $userList = $this->getField("userList");
            $iterator = $userList->iterator();
            $helper = new SearchTableHelper();
            $searchTable = new TableBuilder("searchresult");
            $searchTable->setAlign(new Align("center"));
            $searchTable->buildHeaders("ID", "Name", "Email", "Usergroup", "Joindate", "Befriend", "Trade");
            $searchTable->setHelper($helper);

            while ($iterator->hasNext()) {
                $user = $iterator->next();
                $cells = new LinkedList();
                $cells->add(new TCell($user->getID()));
                $cells->add(new TCell($helper->getUserProfile($user->getID(), $user->getUsername())));
                $cells->add(new TCell($user->getEmail()));
                $cells->add(new TCell($helper->getUsergroup($user->getUsergroup())));
                $cells->add(new TCell($user->getMemberSince("Y-m-d")));
                $cells->add(new TCell($helper->getFriendRequest($user->getID())));
                $cells->add(new TCell($helper->getTradeOffer($user->getID())));
                $searchTable->buildRow($cells);
            }
            $document->add($searchTable);
            return;
        }

        $searchForm = new FormBuilder("searchform", "user", "post");
        $searchForm->buildComment("Username: ", false)
                   ->buildTextField("name")
                   ->buildComment("Email: ", false)
                   ->buildTextField("email");

        $groupMap = $this->getField("groupMap");
        $usergroups = new DropdownList("group");
        $usergroups->add(new Option("None Selected", "none"));
        $usergroups->fill($groupMap);
        $searchForm->add(new Comment("Usergroup: ", false));
        $searchForm->add($usergroups);

        $searchForm->buildComment("Birthday:", false)
                   ->buildTextField("birthday")
                   ->buildComment("JoinDate:", false)
                   ->buildTextField("joindate")
                   ->buildButton("Search", "submit", "submit");
        $document->add($searchForm);
    }

    public function adopt()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $document->setTitle($this->lang->adopt);
        if ($mysidia->input->post("submit")) {
            $adoptList = $this->getField("adoptList");
            $iterator = $adoptList->iterator();
            $helper = new SearchTableHelper();
            $searchTable = new TableBuilder("searchresult");
            $searchTable->setAlign(new Align("center"));
            $searchTable->buildHeaders("ID", "Name", "Type", "Owner", "Level", "Gender", "Trade");
            $searchTable->setHelper($helper);

            while ($iterator->hasNext()) {
                $adopt = $iterator->next();
                $owner = $adopt->getOwner(Model::MODEL);
                $cells = new LinkedList();
                $cells->add(new TCell($adopt->getAdoptID()));
                $cells->add(new TCell($helper->getAdoptName($adopt->getAdoptID(), $adopt->getName())));
                $cells->add(new TCell($adopt->getType()));
                $cells->add(new TCell($helper->getUserProfile($owner->getID(), $owner->getUsername())));
                $cells->add(new TCell($adopt->getCurrentLevel()));
                $cells->add(new TCell($helper->getGenderImage($adopt->getGender())));
                $cells->add(new TCell($helper->getTradeStatus($adopt->getAdoptID(), $adopt->getTradeStatus())));
                $searchTable->buildRow($cells);
            }
            $document->add($searchTable);
            return;
        }

        $searchForm = new FormBuilder("searchform", "adopt", "post");
        $searchForm->buildComment("Name: ", false)
                   ->buildTextField("name")
                   ->buildComment("Type: ", false)
                   ->buildTextField("type")
                   ->buildComment("Owner:", false)
                   ->buildDropdownList("owner", "UsernameList")
                   ->buildComment("Gender:", false)
                   ->buildTextField("gender")
                   ->buildComment("MinLevel:", false)
                   ->buildTextField("minlevel")
                   ->buildButton("Search", "submit", "submit");
        $document->add($searchForm);
    }

    public function item()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $document->setTitle($this->lang->item);

        if ($mysidia->input->post("submit")) {
            $ItemList = $this->getField("itemList");
            $iterator = $ItemList->iterator();
            $helper = new SearchTableHelper();
            $searchTable = new TableBuilder("searchresult");
            $searchTable->setAlign(new Align("center"));
            $searchTable->buildHeaders("ID", "Name", "Category", "Description", "Function", "Price", "Shop");
            $searchTable->setHelper($helper);

            while ($iterator->hasNext()) {
                $item = $iterator->next();
                $cells = new LinkedList();
                $cells->add(new TCell($item->getID()));
                $cells->add(new TCell($item->getItemname()));
                $cells->add(new TCell($item->getCategory()));
                $cells->add(new TCell($item->getDescription()));
                $cells->add(new TCell($item->getFunction()));
                $cells->add(new TCell($item->getPrice()));
                $cells->add(new TCell($helper->getShopLink($item->getShop())));
                $searchTable->buildRow($cells);
            }
            $document->add($searchTable);
            return;
        }

        $searchForm = new FormBuilder("searchform", "item", "post");
        $searchForm->buildComment("Itemname: ", false)
                   ->buildTextField("name")
                   ->buildComment("Category: ", false)
                   ->buildTextField("category");

        $funcList = $this->getField("funcList");
        $functions = new DropdownList("function");
        $functions->add(new Option("None Selected", "none"));
        $functions->fill($funcList);
        $searchForm->add(new Comment("Function: ", false));
        $searchForm->add($functions);

        $searchForm->buildComment("Shop:", false)
                   ->buildDropdownList("shop", "ItemShopList")
                   ->buildComment("MaxPrice:", false)
                   ->buildTextField("maxprice")
                   ->buildButton("Search", "submit", "submit");
        $document->add($searchForm);
    }
}
