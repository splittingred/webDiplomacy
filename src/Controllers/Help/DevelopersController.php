<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;

class DevelopersController extends BaseController
{
    public string $template = 'pages/help/developers.twig';
    public string $pageTitle = 'Development Information';
    public string $pageDescription = 'How to help improve or install webDiplomacy.';

    public function call(): array
    {
        return [];
    }
}