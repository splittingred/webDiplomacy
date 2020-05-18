<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;
use Config;

class FaqController extends BaseController
{
    public $template = 'pages/help/faq.twig';
    public $pageTitle = 'webDiplomacy FAQ';
    public $pageDescription = 'Frequently asked questions about webDiplomacy';

    public function call()
    {
        return [
            'moderator_email' => isset(Config::$modEMail) ? Config::$modEMail : Config::$adminEMail,
        ];
    }
}