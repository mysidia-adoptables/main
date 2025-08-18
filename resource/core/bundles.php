<?php

namespace Resource\Core;
use Resource\Native\MysObject;

class Bundles extends MysObject{
    
    protected $bundles;
    
    public function register($name, $path, $file = NULL){
        $this->bundles[$name] = $path;
        if($file) require $path . $file;           
    }
}