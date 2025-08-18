<?php

namespace Model\DomainModel;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;
use Resource\Native\MysString;

class ACPHook extends Model{
    
    protected $id;
    protected $linktext;
    protected $linkurl;
    protected $pluginname;
    protected $pluginstatus;
    
    public function __construct($acphookinfo, $dto = NULL){	  
	    $mysidia = Registry::get("mysidia");
	    if($acphookinfo instanceof MysString) $acphookinfo = $acphookinfo->getValue();
        if(!$dto){
	        $whereClause = is_numeric($acphookinfo) ? "id = :acphookinfo" : "pluginname = :acphookinfo";
	        $dto = $mysidia->db->select("acp_hooks", [], $whereClause, ["acphookinfo" => $acphookinfo])->fetchObject();
            if(!is_object($dto)) throw new InvalidIDException("Plugin {$acphookinfo} does not exist...");
        }
        parent::__construct($dto);     
    }    
    
    public function getLinkText(){
        return $this->linktext;
    }
    
    public function getLinkURL(){
        return $this->linkurl;
    }
    
    public function getPluginName(){
        return $this->pluginname;
    }
    
    public function getPluginStatus(){
        return $this->pluginstatus;
    }
    
    
    public function isEnabled(){
        return ($this->pluginstatus == 1);
    }
    
    public function enable(){
        $this->pluginstatus = 1;
        $this->save("pluginstatus", $this->pluginstatus);
    }
    
    public function disable(){
        $this->pluginstatus = 0;
        $this->save("pluginstatus", $this->pluginstatus);
    }
    
    protected function save($field, $value) {
		$mysidia = Registry::get("mysidia");
		$mysidia->db->update("acp_hooks", [$field => $value], "id = '{$this->id}'");        
    }
}