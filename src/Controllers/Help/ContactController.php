<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;

class ContactController extends BaseController
{
    public $template = 'pages/help/contact.twig';
    public $pageTitle = 'Contact the Moderators';
    public $pageDescription = 'Learn how to contact the moderators and what information to include.';

    public function call()
    {
        return [];
    }
}