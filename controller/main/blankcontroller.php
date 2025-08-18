<?php

namespace Controller\Main;
use Resource\Core\AppController;
use Resource\Core\Registry;

class BlankController extends AppController{

    public function __construct(){
        parent::__construct();	
    }
	
	public function index(){
        $mysidia = Registry::get("mysidia");
	}
}