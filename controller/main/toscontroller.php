<?php

namespace Controller\Main;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\PageNotFoundException;

class TosController extends AppController{

	public function index(){
	    $mysidia = Registry::get("mysidia");
		try{
		    $document = $mysidia->frame->getDocument("tos");
		}
        catch(PageNotFoundException){
		    $this->setFlags("error", "nonexist");		 
        }
        $this->setField("document", $document);
	}
}