<?php

namespace View\Main;
use Resource\Core\Registry;
use Resource\Core\View;

class BlankView extends View{
	
	public function index(){
	    $mysidia = Registry::get("mysidia");
		$document = $this->document;
	}
}