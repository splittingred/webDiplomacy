<?php

namespace Diplomacy\Views\Components\Games\Statuses;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

/**
 * Display a table with the vital members info; who is finalized, who has sent messages etc, each member
 * takes up a short, thin column.
 */
class SmallChecksGridComponent extends BaseComponent
{
    protected string $template = 'games/statuses/smallChecksGridActive.twig';
    protected Game $game;
    protected ?Member $currentMember;
    protected int $countryCount;
    protected int $memberCount;

    public function __construct(Game $game, Member $currentMember)
    {
        $this->game = $game;
        $this->currentMember = $currentMember;
        $this->countryCount = $this->game->getCountryCount();
        $this->memberCount = $this->game->getMemberCount();
    }

    public function attributes(): array
    {
        $attributes = [
            'game' => $this->game,
            'currentMember' => $this->currentMember,
        ];
        if ($this->game->phase->isPreGame()) {
            $attributes = array_merge($attributes, $this->buildForPreGame());
        } else {
            $attributes = array_merge($attributes, $this->buildForActive());
        }
        return $attributes;
    }

    public function buildForPreGame(): array
    {
        $this->template = 'games/statuses/smallChecksGridPreGame.twig';

        $attributes = ['members' => []];
        for ($i = 0; $i < $this->memberCount; $i++)
            $attributes['members'][] = [
                'filled' => true,
            ];
        for ($i = $this->memberCount + 1; $i <= $this->countryCount; $i++)
            $attributes['members'][] = [
                'filled' => false,
            ];

        return [];
    }

    public function buildForActive(): array
    {
        $attributes = ['members' => []];
        $membersPerRow = 7; // TODO: Consider adjusting members per row for variants

        for($countryID = 1; $countryID <= $this->countryCount; $countryID++)
        {
            $member = $this->game->members->byCountryId($countryID);
            $isSelf = $this->currentMember->isUser($member->user);
            $messagesIcon = $this->currentMember->messagesFromLink($member->user);

            $data = [
                'id' => $member->id,
                'cssCls' => $isSelf ? 'memberYourCountry' : '',
                'alternate' => \libHTML::alternate(),
                'width' => $this->memberCount / $membersPerRow,
                'status' => $member->status,
                'country' => $member->country,
                'statusIcon' => $member->canBeSeenBy($this->currentMember) ?  $member->ordersState->icon() : $member->ordersState->iconAnon(),
                'newMessagesIcon' => $messagesIcon,
                'name' => $member->country->shortName(),
            ];
            $attributes['members'][] = $data;
        }
        return $attributes;
    }
}