<?php

namespace Diplomacy\Controllers\Games\View;

use Diplomacy\Controllers\BaseController as Base;

abstract class BaseController extends Base
{
    protected $template = 'pages/games/view.twig';

    public function call()
    {
        return [];
    }
}