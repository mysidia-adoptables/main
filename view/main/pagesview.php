<?php

namespace View\Main;
use Resource\Core\View;

class PagesView extends View{

    public function view(){
	    if(isset($this->flags)) $this->redirect(3, "../../index");
    }
}