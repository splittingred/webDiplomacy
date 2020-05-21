<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;

class DevelopersController extends BaseController
{
    public $template = 'pages/help/developers.twig';
    public $pageTitle = 'Development Information';
    public $pageDescription = 'How to help improve or install webDiplomacy.';

    public function call()
    {
        return [];
    }
}