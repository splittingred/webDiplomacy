<?php

namespace Diplomacy\Controllers\Tournaments;

use Diplomacy\Controllers\BaseController;

class InfoController extends BaseController
{
    /** @var string */
    protected $template = 'pages/tournaments/info.twig';

    public function call() : array
    {
        return [
            'moderator_email' => \Config::$modEMail ? \Config::$modEMail : \Config::$adminEMail
        ];
    }
}