<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;

class RecentChangesController extends BaseController
{
    public string $template = 'pages/help/recent_changes.twig';
    public string $pageTitle = 'Recent Changes';
    public string $pageDescription = 'Lists the most recent changes to the webdiplomacy software.';

    public function call(): array
    {
        return [];
    }
}