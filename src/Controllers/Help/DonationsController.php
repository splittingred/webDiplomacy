<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;
use Config;

class DonationsController extends BaseController
{
    public $template = 'pages/help/donations.twig';
    public $pageTitle = 'Donations';
    public $pageDescription = 'Learn how to donate and what your donations are used for.';

    public function call()
    {
        return [
            'moderator_email' => isset(Config::$modEMail) ? Config::$modEMail : Config::$adminEMail,
        ];
    }
}