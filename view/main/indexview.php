<?php

namespace View\Main;

use Resource\Core\Registry;
use Resource\Core\View;

class IndexView extends View
{
    public function index()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->systems->site != "enabled") {
            $document = $this->document;
            $document->setTitle("An error has occurred...");
            $document->addLangvar("The admin has turned off the site for maintenance, please come back and visit later.");
        } else {
            $frame = $mysidia->getFrame();
            $frame->getDocument("index");
        }
    }
}
