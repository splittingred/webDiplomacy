<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;
use Config;

class RecentChangesController extends BaseController
{
    public $template = 'pages/help/recent_changes.twig';
    public $pageTitle = 'Recent Changes';
    public $pageDescription = 'Lists the most recent changes to the webdiplomacy software.';

    public function call()
    {
        return [
            'moderator_email' => isset(Config::$modEMail) ? Config::$modEMail : Config::$adminEMail,
        ];
    }
}