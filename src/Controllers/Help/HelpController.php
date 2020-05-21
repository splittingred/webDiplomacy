<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;

class HelpController extends BaseController
{
    public $template = 'pages/help/help.twig';
    public $pageTitle = 'Information and Links';
    public $pageDescription = 'Links to pages with more information about webDiplomacy.';

    public function call()
    {
        return [];
    }
}