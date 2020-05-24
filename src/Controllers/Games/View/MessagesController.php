<?php

namespace Diplomacy\Controllers\Games\View;

use Diplomacy\Controllers\Games\View\BaseController;
use Diplomacy\Models\GameMessage;
use Diplomacy\Services\Games\MessagesService;
use Diplomacy\Services\Request;

class MessagesController extends BaseController
{
    protected $template = 'pages/games/view/messages.twig';

    /** @var MessagesService */
    protected $gameMessagesService;

    public function setUp()
    {
        $this->gameMessagesService = new MessagesService();
        parent::setUp();
    }

    public function call()
    {
        $filter = (int)$this->request->get('filter', -1, Request::TYPE_GET);
        if (empty($this->member)) $filter = 0;

        $messages = $this->getMessages($filter);

        return [
            'filter' => $filter,
            'messages' => $messages,
            'pagination' => $this->getPagination($messages->getTotal()),
        ];
    }

    protected function getMessages(int $filter)
    {
        $memberCountryId = $this->member ? $this->member->countryID : 0;
        $messages = $this->gameMessagesService->search($this->game->id, $filter, $memberCountryId, $this->perPage);
        /** @var GameMessage $message */
        foreach ($messages as $message) {
            $message->setVariant($this->variant);
        }
        return $messages;
    }
}