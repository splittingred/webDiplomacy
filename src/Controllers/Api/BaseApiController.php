<?php

namespace Diplomacy\Controllers\Api;

use Diplomacy\Controllers\BaseController;

abstract class BaseApiController extends BaseController
{
    public function render(): string
    {
        $this->setDefaultPlaceholders();
        $this->beforeRender();
        $output = $this->call();
        $this->afterRender();

        header('Content-type: application/javascript');
        return json_encode($output);
    }
}