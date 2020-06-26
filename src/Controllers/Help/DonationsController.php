<?php

namespace Diplomacy\Controllers\Help;

use Diplomacy\Controllers\BaseController;

class DonationsController extends BaseController
{
    public string $template = 'pages/help/donations.twig';
    public string $pageTitle = 'Donations';
    public string $pageDescription = 'Learn how to donate and what your donations are used for.';

    public function call(): array
    {
        return [];
    }
}