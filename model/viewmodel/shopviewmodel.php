<?php

namespace Model\ViewModel;
use Resource\Core\Model;
use Resource\Core\ViewModel;

abstract class ShopViewModel extends ViewModel{
    
    public function getShoptype(){
        return $this->model->getShoptype();
    }
    
    public function getShopImage(){
        return $this->model->getImageURL(Model::GUI);
    }
    
    public function isEmpty(){
        return ($this->model->getTotal() == 0);
    }
    
    abstract public function display();
}