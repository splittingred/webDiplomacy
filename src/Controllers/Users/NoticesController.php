<?php

namespace Diplomacy\Controllers\Users;

use Diplomacy\Controllers\BaseController;
use libHome;

class NoticesController extends BaseController
{
    protected string $template = 'pages/users/notices.twig';
    protected array $footerScripts = [
        'homeGameHighlighter();'
    ];
    protected array $footerIncludes = [
        'home.js'
    ];

    public function call(): array
    {
        $this->currentUser->clearNotification('PrivateMessage');
        \notice::$noticesPage = true;

        return [
            'pms' => libHome::NoticePMs(),
            'game_notices' => libHome::NoticeGame(),
        ];
    }
}