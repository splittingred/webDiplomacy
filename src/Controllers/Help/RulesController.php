<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;

class RulesController extends BaseController
{
    public $template = 'pages/help/rules.twig';
    public $pageTitle = 'webDiplomacy Rulebook';
    public $pageDescription = 'The webDiplomacy rules that let moderators and users keep this server fun to play on.';

    public function call()
    {
        return [];
    }
}