<?php

namespace Controller\Main;

use Resource\Core\FrontController;
use Resource\Core\Registry;
use Resource\Exception\NoPermissionException;

class IndexController extends FrontController
{
    protected function triggerAction()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->systems->site != "enabled") {
            throw new NoPermissionException("The admin has turned off the site for maintenance, please come back and visit later.");
        }
        parent::triggerAction();
    }
}
