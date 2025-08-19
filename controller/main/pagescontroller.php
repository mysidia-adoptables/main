<?php

namespace Controller\Main;

use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;

class PagesController extends AppController
{
    public function index(): never
    {
        throw new InvalidIDException("global_id");
    }

    public function view($pageinfo)
    {
        $mysidia = Registry::get("mysidia");
        try {
            $document = $mysidia->frame->getDocument($pageinfo);
        } catch (NoPermissionException $npe) {
            $this->setFlags("error", $npe->getmessage());
        } catch (PageNotFoundException $pne) {
            $this->setFlags("error", $pne->getmessage());
        }
        $this->setField("document", $document);
    }
}
