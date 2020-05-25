<?php

namespace Diplomacy\Services\Forum;

use Diplomacy\Models\Collection;
use Diplomacy\Models\ForumMessage;

class MessagesService
{
    /**
     * @param int $userId
     * @return Collection
     */
    public function getThreadsForUser(int $userId)
    {
        $q = ForumMessage::root()->whereFromUser($userId);
        $total = $q->count();

        $q->orderBy('timeSent', 'desc');
        $messages = $q->get();

        return new Collection($messages, $total);
    }
}