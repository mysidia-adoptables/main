<?php

namespace Resource\Core;
use Resource\Collection\HashSet;
use Resource\Native\MysString;

class Plugins extends Core implements Initializable{
    
    private $pluginSet;
    
    public function __construct(){
        $this->pluginSet = new HashSet;
        $this->initialize();
    }
    
    public function initialize() {
        $mysidia = Registry::get("mysidia");
        $stmt = $mysidia->db->select("acp_hooks", ["pluginname"], "pluginstatus = 1");
        while($pluginName = $stmt->fetchColumn()){
            $this->pluginSet->add(new MysString($pluginName));
        }
    }
    
    public function countPlugins(){
        return $this->pluginSet->size();
    }
    
    public function getPlugins(){
        $plugins = [];
        $iterator = $this->pluginSet->iterator();
        while($iterator->hasNext()){
            $plugins[] = (string)$iterator->next();
        }
        return $plugins;
    }
    
    public function isEnabled($pluginName){
        if($this->pluginSet->isEmpty()) return FALSE;
        return $this->pluginSet->contains(new MysString($pluginName));
    }
    
    public function __toString() {
        return "Mysidia Plugins Enabled: " . implode(", ", $this->getPlugins());
    }
}