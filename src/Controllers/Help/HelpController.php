<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;

class HelpController extends BaseController
{
    public string $template = 'pages/help/help.twig';
    public string $pageTitle = 'Information and Links';
    public string $pageDescription = 'Links to pages with more information about webDiplomacy.';

    public function call(): array
    {
        return [];
    }
}