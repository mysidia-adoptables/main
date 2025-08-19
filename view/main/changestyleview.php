<?php

namespace View\Main;

use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Link;
use Resource\GUI\Document\Paragraph;

class ChangeStyleView extends View
{

    public function index()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $document->setTitle($this->lang->title);
        if ($mysidia->input->get("theme")) {
            $document->addLangvar($this->lang->success);
            return;
        }
        $document->addLangvar($this->lang->select);
        $paragraph = new Paragraph;
        $themes = $this->getField("themes");
        $iterator = $themes->iterator();
        while ($iterator->hasNext()) {
            $theme = $iterator->next();
            $paragraph->add(new Link("changestyle/index/{$theme->getThemeFolder()}", $theme->getThemename(), true));
        }
        $document->add($paragraph);
    }
}
