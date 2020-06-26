<?php

namespace Diplomacy\Controllers;

use libHome;

class IntroController extends BaseController
{
    /** @var string */
    protected string $template = 'pages/home/intro.twig';
    protected array $footerScripts = ['homeGameHighlighter();'];
    protected array $footerIncludes = ['home.js'];

    public function call(): array
    {
        return [
            'globalInfo' => libHome::globalInfo()
        ];
    }
}