<?php
namespace Diplomacy\Controllers\Users;

use Diplomacy\Models\ForumMessage;
use Diplomacy\Services\Forum\MessagesService;

class ThreadsController extends BaseController
{
    /** @var string */
    protected $template = 'pages/users/threads.twig';
    /** @var MessagesService */
    protected $forumMessagesService;

    public function setUp()
    {
        $this->forumMessagesService = new MessagesService();
        parent::setUp();
    }

    public function call()
    {
        return [
            'threads' => $this->forumMessagesService->getThreadsForUser($this->user->id),
        ];
    }
}
