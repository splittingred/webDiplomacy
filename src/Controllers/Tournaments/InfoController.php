<?php

namespace Diplomacy\Controllers\Tournaments;

use Diplomacy\Controllers\BaseController;

class InfoController extends BaseController
{
    /** @var string */
    protected string $template = 'pages/tournaments/info.twig';
    protected string $pageTitle = 'webDiplomacy Tournaments';
    protected string $pageDescription = 'Information on Tournaments and Feature Game rules and setup.';

    public function call(): array
    {
        return [];
    }
}