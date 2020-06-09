<?php
namespace Diplomacy\Views\Components\Games\ChatBox;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\Entities\Games\Message;
use Diplomacy\Views\Components\BaseComponent;

class MessageComponent extends BaseComponent
{
    protected $template = 'games/chatbox/message.twig';

    /** @var Message $message */
    protected $message;
    /** @var Game $game */
    protected $game;
    /** @var Member $currentMember */
    protected $currentMember;
    /** @var bool $showAuthors */
    protected $showAuthors;
    /** @var bool $alternate */
    protected $alternate;

    /**
     * @param Message $message
     * @param Game $game
     * @param Member $currentMember
     * @param bool $showAuthors
     * @param bool $alternate
     */
    public function __construct(Message $message, Game $game, Member $currentMember, bool $showAuthors = true, bool $alternate = false)
    {
        $this->message = $message;
        $this->game = $game;
        $this->currentMember = $currentMember;
        $this->alternate = $alternate;
        $this->showAuthors = $showAuthors;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        $isAuthenticated = $this->currentMember->isAuthenticated();
        $isToMe = $isAuthenticated && $this->currentMember->isCountry($this->message->toCountry);
        $isFromMe = $isAuthenticated && $this->currentMember->isCountry($this->message->fromCountry);
        $attributes = [
            'gameId' => $this->game->id,
            'timeAsText' => $this->message->timeSentAsText(),
            'alternate' => $this->alternate,
            'showAuthors' => $this->showAuthors,
            'toCountry' => $this->message->toCountry,
            'fromCountry' => $this->message->fromCountry,
            'body' => $this->message->message,
            'isNote' => $isToMe && $isFromMe,
            'isGlobalSystem' => $this->message->fromCountry->isGlobal() && $this->message->toCountry->isGlobal(),
        ];

        if ($this->showAuthors)
        {
            if ($isFromMe) {
                $attributes['from'] = 'You';
            } elseif ($this->message->fromCountry->isGlobal()) {
                $attributes['from'] = 'Gamemaster';
            } else {
                $attributes['from'] = $this->message->fromCountry->name;
            }

            if ($isToMe) {
                $attributes['to'] = 'You';
            } else {
                $attributes['to'] = $this->message->toCountry->name;
            }
        }

        $attributes['turn'] = $this->message->turn->name;
        $attributes['toMe'] = $isToMe;
        $attributes['fromMe'] = $isFromMe;
        return $attributes;
    }
}