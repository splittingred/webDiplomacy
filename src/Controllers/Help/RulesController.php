<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;

class RulesController extends BaseController
{
    public string $template = 'pages/help/rules.twig';
    public string $pageTitle = 'webDiplomacy Rulebook';
    public string $pageDescription = 'The webDiplomacy rules that let moderators and users keep this server fun to play on.';

    public function call(): array
    {
        return [];
    }
}