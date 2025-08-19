<?php

namespace Model\ViewModel;

use Model\DomainModel\Adoptable;
use Resource\Collection\LinkedList;
use Resource\Core\Model;
use Resource\GUI\Container\TCell;
use Resource\GUI\Element\Align;
use Service\Builder\TableBuilder;
use Service\Helper\ShopTableHelper;

class AdoptShopViewModel extends ShopViewModel
{
    public function display()
    {
        $helper = new ShopTableHelper();
        $adoptList = new TableBuilder("shop");
        $adoptList->setAlign(new Align("center", "middle"));
        $adoptList->buildHeaders("Image", "Class", "Type", "Description", "Price", "Buy");
        $adoptList->setHelper($helper);
        $adoptTypes = $this->model->getAdoptTypes();

        foreach ($adoptTypes as $adoptType) {
            $adopt = new Adoptable($adoptType);
            $cells = new LinkedList();
            $cells->add(new TCell($adopt->getEggImage(Model::GUI)));
            $cells->add(new TCell($adopt->getClass()));
            $cells->add(new TCell($adopt->getType()));
            $cells->add(new TCell($adopt->getDescription()));
            $cells->add(new TCell($adopt->getCost()));
            $cells->add(new TCell($helper->getAdoptPurchaseForm($this->model, $adopt)));
            $adoptList->buildRow($cells);
        }
        return $adoptList;
    }
}
