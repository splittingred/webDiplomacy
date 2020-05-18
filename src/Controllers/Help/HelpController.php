<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;
use Config;

class HelpController extends BaseController
{
    public $template = 'pages/help/help.twig';
    public $pageTitle = 'Information and Links';
    public $pageDescription = 'Links to pages with more information about webDiplomacy.';

    public function call()
    {
        return [
            'moderator_email' => isset(Config::$modEMail) ? Config::$modEMail : Config::$adminEMail,
        ];
    }
}