<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;

class PointsController extends BaseController
{
    public string $template = 'pages/help/points.twig';
    public string $pageTitle = 'A Guide to webDiplomacy\'s Scoring Systems and Points';
    public string $pageDescription = 'How our scoring systems work and how you can use your points on webDiplomacy.';

    public function call(): array
    {
        return [];
    }
}