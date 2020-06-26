<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;

class FaqController extends BaseController
{
    public string $template = 'pages/help/faq.twig';
    public string $pageTitle = 'webDiplomacy FAQ';
    public string $pageDescription = 'Frequently asked questions about webDiplomacy';

    public function call(): array
    {
        return [];
    }
}