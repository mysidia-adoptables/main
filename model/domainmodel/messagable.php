<?php

namespace Model\DomainModel;

interface Messagable{  
    
    public function gettitle();
    public function getcontent();
    public function getnotifier();
    public function remove();
    
}