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
    public function __construct(Message $message, Game $game, Member $currentMember, bool $showAuthors, bool $alternate)
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
        $attributes = [
            'timeAsText' => $this->message->timeSentAsText(),
            'alternate' => $this->alternate,
        ];

        if ($this->showAuthors)
        {
            if ($this->currentMember->isCountry($this->message->fromCountry)) {
                $attributes['from'] = 'you';
            } elseif ($this->message->fromCountry->isGlobal()) {
                $attributes['from'] = 'Gamemaster';
            } else {
                $attributes['from'] = $this->message->fromCountry->name;
            }

            if ($this->currentMember->isCountry($this->message->toCountry)) {
                $attributes['to'] = 'You';
            } else {
                $attributes['to'] = $this->message->toCountry->name;
            }
        }

        $attributes['turn'] = $this->message->turn->name;
        $attributes['fromMe'] = $this->currentMember->isAuthenticated() && $this->currentMember->isCountry($this->message->fromCountry);
        return $attributes;
    }
}