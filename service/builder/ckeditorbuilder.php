<?php

namespace Service\Builder;
use Resource\Core\Registry;
use Resource\GUI\Component;
use Resource\Native\MysObject;

class CKEditorBuilder extends MysObject{
    
    private $package;
    
    public function __construct($package = "standard"){
        $this->package = $package;
        $this->loadScripts();
    }
    
    private function loadScripts(){
        $path = Registry::get("path");
        $frame = Registry::get("frame");
        $header = $frame->getHeader();
        $header->addScript("{$path->getTempRoot()}bundles/ckeditor/ckeditor.js");
        $header->addScript("{$path->getTempRoot()}js/ckeditor.js");  
    }
    
    public function getPackage(){
        return $this->package;
    }
    
    public function buildEditor($id, $value = "", $rows = "", $cols = "", $event = ""){
        $component = new Component\TextArea($id, $value, $rows, $cols, $event);
        return $this->buildEditorComponent($component);
    }
    
    public function buildEditorComponent(Component $component){
        $component->setClass("editor-{$this->package}");
        return $component;
    }
}