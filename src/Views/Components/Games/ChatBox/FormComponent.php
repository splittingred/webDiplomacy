<?php
namespace Diplomacy\Views\Components\Games\ChatBox;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Country;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Services\Games\MessagesService;
use Diplomacy\Views\Components\BaseComponent;

class FormComponent extends BaseComponent
{
    protected string $template = 'games/chatbox/form.twig';
    protected Game $game;
    protected ?Member $currentMember;
    protected int $targetCountryId;
    protected MessagesService $messagesService;
    protected bool $isGlobal;
    protected bool $isAll;

    /**
     * @param Game $game
     * @param Member $currentMember
     * @param int $targetCountryId
     */
    public function __construct(Game $game, Member $currentMember, int $targetCountryId = 0)
    {
        $this->game = $game;
        $this->currentMember = $currentMember;
        $this->targetCountryId = $targetCountryId > Country::GLOBAL && $targetCountryId < $this->game->getCountryCount() ? (int)$targetCountryId : Country::GLOBAL;
        $this->isGlobal = $targetCountryId == Country::GLOBAL;
        $this->isAll = $targetCountryId == Country::ALL;
        $this->messagesService = new MessagesService();
    }

    public function attributes(): array
    {
        $attributes = [
            'game' => $this->game,
            'targetCountryId' => $this->targetCountryId,
            'isGlobal' => $this->isGlobal,
            'isAll' => $this->isAll,
        ];

        $currentUserIsSudo = $this->currentMember->user->isModerator()
            || $this->game->isDirector($this->currentMember->user->id)
            || $this->game->isTournamentDirector($this->currentMember->user->id)
            || $this->game->isTournamentCoDirector($this->currentMember->user->id);

        $form = ''; // TODO: the chat box form component
        if (($this->isGlobal && $currentUserIsSudo) || $this->messagesService->canSend($this->game, $this->currentMember, $this->targetCountryId)) {
            \libHTML::$footerIncludes[] = l_j('message.js');
        }
        else if ((string)$this->game->pressType == 'RulebookPress' && (string)$this->game->phase != 'Diplomacy' && !$this->isGlobal)  {
            $this->template = 'games/chatbox/form_rulebook.twig';
        } else {
            $this->template = 'games/chatbox/form_no_press.twig';
        }

        return $attributes;
    }
}