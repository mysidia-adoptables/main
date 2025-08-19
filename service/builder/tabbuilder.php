<?php

namespace Service\Builder;
use Exception;
use Resource\Collection\Mappable;
use Resource\Core\Registry;
use Resource\Native\MysArray;
use Resource\Native\MysObject;

/**
 * The TabBuilder Class, extends from the abstract MysObject class.
 * It provides shortcut for building tabs in quick manner.
 * @category Service
 * @package Builder
 * @author Hall of Famer 
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 *
 */  

class TabBuilder extends MysObject{

	/**
	 * The name property, stores an array of names for each tab.
	 * @access private
	 * @var MysArray
     */
    private $name;
    
    /**
	 * The alias property, stores an array of alias for each tab.
	 * @access private
	 * @var MysArray
     */
    private $alias;
    
    /**
	 * The hide property, specifies whether the tab is hidden.
	 * @access private
	 * @var MysArray
     */
    private $hide;
    
    /**
	 * The index property, stores an array of index info for each tab.
	 * @access private
	 * @var MysArray
     */
    private $index;
    
    /**
	 * The last property, stores an array of last tab info for each tab.
	 * @access private
	 * @var MysArray
     */
    private $last;

    
	/**
     * Constructor of TabBuilder Class, it initiates the basic properties for tabs. 
	 * @param int  $num
     * @param Mappable  $tabs
	 * @param int  $default
     * @access public
     * @return void
     */    
    public function __construct(/**
     * The num property, defines the number of tabs available.
     * @access private
     */
    private $num, Mappable $tabs, /**
     * The default property, defines the default tab.
     * @access private
     */
    private $default = 1){
        $path = Registry::get("path");
        $frame = Registry::get("frame");
        $header = $frame->getHeader();
        $header->addStyle("{$path->getTempRoot()}css/tabs.css");
        $header->addScript("{$path->getTempRoot()}js/tabs.js");
        $this->name = new MysArray($this->num);
        $this->alias = new MysArray($this->num);
        $this->index = new MysArray($this->num);
        $this->last = new MysArray($this->num);

	    $i = 0;
        $iterator = $tabs->iterator();
        while($iterator->hasNext()){
            $entry = $iterator->next();
	        $this->name[$i] = (string)$entry->getKey();
            $this->alias[$i] = (string)$entry->getValue();
	        $this->index[$i] = ($i == $this->default - 1) ? " class='current" : "";
	        $this->index[$i] = ($i == $this->num - 1) ? " class='last" : $this->index[$i];	  
  	        $this->last[$i] = ($i == $this->num - 1) ? " last" : "";  
	        $i++;
        }
    }

    /**
     * The createTab method, creates the tab panel and returns the corresponding HTML. 
     * @access public
     * @return String
     */
    public function createTab(){
        if($this->num < 2 || $this->num > 5) throw new Exception("The number of tabs must be restricted between 2 to 5!",272);
        $init = "<div id='page-wrap'><div id='profile'><ul class='nav'>";
        for($i=0; $i < $this->num; $i++){
            $init .= " <li class='nav{$i}{$this->last[$i]}'><a href='#{$this->alias[$i]}'{$this->index[$i]}'>{$this->name[$i]}</a></li>";
        }
        $init .= "</ul><div class='list-wrap'>"; 
		return $init;
    }
  
    /**
     * The startTab method, starts a tab for the given index.
     * @param int  $index 
     * @access public
     * @return String
     */    
    public function startTab($index){
        $this->hide = ($index == $this->default - 1) ? "" : " class='hide";
        $header = "<ul id='{$this->alias[$index]}'{$this->hide}'>";	
		return $header;
    }

    /**
     * The endTab method, ends a tab for the given index.
     * @param int  $index 
     * @access public
     * @return String
     */ 
    public function endTab($index){
        $footer = "</ul>";
	    if($index == $this->num - 1) $footer .= "</div></div>";
	    return $footer;
    }
}