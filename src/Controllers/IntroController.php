<?php

namespace Diplomacy\Controllers;

use libHome;

class IntroController extends BaseController
{
    /** @var string */
    protected $template = 'pages/home/intro.twig';
    protected $footerScripts = ['homeGameHighlighter();'];
    protected $footerIncludes = ['home.js'];

    public function call()
    {
        return [
            'globalInfo' => libHome::globalInfo()
        ];
    }
}