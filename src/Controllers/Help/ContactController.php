<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;

class ContactController extends BaseController
{
    public string $template = 'pages/help/contact.twig';
    public string $pageTitle = 'Contact the Moderators';
    public string $pageDescription = 'Learn how to contact the moderators and what information to include.';

    public function call(): array
    {
        return [];
    }
}