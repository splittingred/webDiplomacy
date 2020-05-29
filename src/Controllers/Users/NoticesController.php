<?php

namespace Diplomacy\Controllers\Users;

use Diplomacy\Controllers\BaseController;
use libHome;

class NoticesController extends BaseController
{
    protected $template = 'pages/users/notices.twig';
    protected $footerScripts = [
        'homeGameHighlighter();'
    ];
    protected $footerIncludes = [
        'home.js'
    ];

    public function call()
    {
        $this->currentUser->clearNotification('PrivateMessage');
        \notice::$noticesPage = true;

        return [
            'pms' => libHome::NoticePMs(),
            'game_notices' => libHome::NoticeGame(),
        ];
    }
}