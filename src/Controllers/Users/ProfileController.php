<?php

namespace Diplomacy\Controllers\Users;

use Diplomacy\Forms\Users\Messages\SendPrivateMessageForm;
use Diplomacy\Services\Games\MessagesService as GameMessagesService;
use Diplomacy\Services\Users\RankingsService;

class ProfileController extends BaseController
{
    /** @var string $template */
    protected $template = 'pages/users/profile.twig';
    /** @var GameMessagesService $gameMessagesService */
    protected $gameMessagesService;
    /** @var RankingsService $rankingsService */
    protected $rankingsService;

    public function setUp()
    {
        parent::setUp();
        $this->gameMessagesService = new GameMessagesService();
        $this->rankingsService = new RankingsService();
    }

    public function call()
    {
        $rankings = $this->rankingsService->getForUser($this->user);
        return [
            'user' => $this->user,
            'gameMessagesCount' => $this->gameMessagesService->totalForUser($this->user->id),
            'rankings' => $rankings,
            'badges' => [
                'bronze'    => \libHTML::bronze(),
                'silver'    => \libHTML::silver(),
                'gold'      => \libHTML::gold(),
                'platinum'  => \libHTML::platinum()
            ],
            'adminLinks' => [
                'banUser' => \libHTML::admincp('banUser', ['userID' => $this->user->id], 'Ban user'),
                'createSilence' => \libHTML::admincp('createUserSilence', ['userID' => $this->user->id, 'reason' => ''],'Silence user')
            ],
            'sendPrivateMessageForm' => $this->getSendPrivateMessageForm(),
        ];
    }

    private function getSendPrivateMessageForm()
    {
        return $this->makeForm(SendPrivateMessageForm::class, [
            'user_id' => $this->user->id,
        ]);
    }
}