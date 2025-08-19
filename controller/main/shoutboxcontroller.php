<?php

namespace Controller\Main;

use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\NoPermissionException;
use Service\ApplicationService\ShoutboxService;

class ShoutboxController extends AppController
{
    private $shoutboxService;

    public function __construct()
    {
        parent::__construct();
        $mysidia = Registry::get("mysidia");
        if ($mysidia->systems->shoutbox != "enabled") {
            throw new NoPermissionException("disabled");
        }
        $this->shoutboxService = new ShoutboxService();
    }

    public function index()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("comment")) {
            $this->shoutboxService->postMessage($mysidia->input->rawPost("comment"));
        }
        $this->setField("messages", $this->shoutboxService->getMessages());
    }
}
