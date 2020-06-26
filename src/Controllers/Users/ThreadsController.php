<?php
namespace Diplomacy\Controllers\Users;

use Diplomacy\Models\ForumMessage;
use Diplomacy\Services\Forum\MessagesService;

class ThreadsController extends BaseController
{
    protected string $template = 'pages/users/threads.twig';
    protected MessagesService $forumMessagesService;

    public function setUp(): void
    {
        $this->forumMessagesService = new MessagesService();
        parent::setUp();
    }

    public function call(): array
    {
        return [
            'threads' => $this->forumMessagesService->getThreadsForUser($this->user->id),
        ];
    }
}
