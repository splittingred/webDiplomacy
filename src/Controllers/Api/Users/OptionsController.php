<?php

namespace Diplomacy\Controllers\Api\Users;

use Diplomacy\Controllers\BaseController;

class OptionsController extends BaseController
{

    public function call()
    {
        header('Content-type: application/javascript');
        if (!is_null($this->currentUser->options))
        {
            echo $this->currentUser->options->asJS();
        }
        else
        {
            echo \UserOptions::defaultJS();
        }
        close();
    }
}