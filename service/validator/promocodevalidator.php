<?php

namespace Service\Validator;
use ArrayObject;
use Model\DomainModel\Promocode;
use Model\DomainModel\PromocodeException;
use Resource\Core\Registry;
use Resource\Core\Validator;
use Resource\Utility\Date;

class PromocodeValidator extends Validator{ 
    
    public function __construct(private readonly Promocode $promocode, ArrayObject $validations){
        parent::__construct($validations);	
	}   
 
    protected function checkUser(){
        $mysidia = Registry::get("mysidia");
        $promoUser = $this->promocode->getUser();
        if($promoUser && $promoUser != $mysidia->user->getID()){
            // The user has entered a promocode that matches our database, but it does not belong to him/her. We're having a hacking attempt!
            $mysidia->user->ban();
            throw new PromocodeException("owner");
        }
    }
    
    protected function checkNumber(){ 
        if(!$this->promocode->isAvailable()) throw new PromocodeException("unavail");        
    }
    
    protected function checkDate(){ 
        $fromDate = $this->promocode->getDateFrom();
        $toDate = $this->promocode->getDateTo();
        $today = new Date;
        if($fromDate && $today < $fromDate) throw new PromocodeException("early");
        if($toDate && $today > $toDate) throw new PromocodeException("expired");
    }
}