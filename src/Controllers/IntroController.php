<?php

namespace Diplomacy\Controllers;

use libHome;

class IntroController extends BaseController
{
    /** @var string */
    protected $template = 'pages/home/intro.twig';

    public function call()
    {
        return [
            'globalInfo' => libHome::globalInfo()
        ];
    }
}