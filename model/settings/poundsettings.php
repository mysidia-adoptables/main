<?php

namespace Model\Settings;
use Resource\Core\Database;
use Resource\Core\Settings;

class PoundSettings extends Settings{ 

	/**
	 * The system property, defines if the pound system is enabled.
	 * @access public
	 * @var String
     */
    public $system;    
    
	/**
	 * The adopt property, defines if the pound system is enabled.
	 * @access public
	 * @var String
     */    
    public $adopt;
    
	/**
	 * The specieslimit property, specifies the adoptable species that cannot be pounded.
	 * @access public
	 * @var Array
     */       
    public $specieslimit;
    
	/**
	 * The cost property, specifies the adoptable species that cannot be pounded.
	 * @access public
	 * @var Array
     */      
    public $cost;
    
	/**
	 * The costtype property, defines the type of the cost(incremental or percentage).
	 * @access public
	 * @var String
     */     
    public $costtype;

	/**
	 * The levelbonus property, specifies the level bonus value for pound cost.
	 * @access public
	 * @var int
     */         
    public $levelbonus;
    
	/**
	 * The leveltype property, defines the type of the level bonus(incremental or multiplier).
	 * @access public
	 * @var String
     */      
    public $leveltype;
    
	/**
	 * The number property, specifies how many times a user may pound or readopt a pounded adoptable.
	 * @access public
	 * @var Array
     */        
    public $number;
    
	/**
	 * The date property, specifies if the above number restriction is per date or per user.
	 * @access public
	 * @var String
     */       
    public $date;
    
	/**
	 * The duration property, specifies the number of days that an adoptable may be readopted once pounded.
	 * @access public
	 * @var int
     */       
    public $duration;
    
	/**
	 * The owner property, defines if previous owners may re-adopt their own adoptables.
	 * @access public
	 * @var String
     */       
    public $owner;

	/**
	 * The recurrence property, defines how many times an adoptable may be pounded total.
	 * @access public
	 * @var int
     */        
    public $recurrence;
    
	/**
	 * The rename property, specifies if new owners can rename the pounded adoptables.
	 * @access public
	 * @var String
     */      
    public $rename;
    
    
    /**
     * Constructor of PoundSettings Class, it initializes basic setting parameters.
	 * @param Database  $db
     * @access public
     * @return void
     */
    public function __construct(Database $db){
	    parent::__construct($db);
        $this->specieslimit = explode(",", $this->specieslimit);	
       	$this->cost = explode(",", $this->cost);
        $this->poundcost = $this->cost[0];
        $this->adoptcost = $this->cost[1];
        $this->number = explode(",", $this->number);
    }

	/**
     * The fetch method, returns all fields of LevelSettings object by fetching information from database.
     * @param Database|SplFileInfo  $db
     * @access public
     * @return void
     */    
    public function fetch($db) {
        $stmt = $db->select("pounds_settings");
	    while($row = $stmt->fetchObject()){
	        $property = $row->name;
	        $this->$property = $row->value;
	    }	        
    }
    
    /**
     * The set method, set a field of PoundSettings object with a specific value.
	 * @param String  $property    
	 * @param String|Number  $value    
     * @access public
     * @return void
     */
    public function set($property, $value){
        $this->$property = $value;
    }    
}