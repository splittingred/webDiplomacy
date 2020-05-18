<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;
use Config;

class PointsController extends BaseController
{
    public $template = 'pages/help/points.twig';
    public $pageTitle = 'A Guide to webDiplomacy\'s Scoring Systems and Points';
    public $pageDescription = 'How our scoring systems work and how you can use your points on webDiplomacy.';

    public function call()
    {
        return [
            'moderator_email' => isset(Config::$modEMail) ? Config::$modEMail : Config::$adminEMail,
        ];
    }
}