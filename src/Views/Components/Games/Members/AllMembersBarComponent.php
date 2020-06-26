<?php

namespace Diplomacy\Views\Components\Games\Members;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Views\Components\BaseComponent;

/**
 * The occupation bar; a bar representing each of the countries current progress as measured by the number of SCs.
 * If called pre-game it goes from red to green as 1 to 7 players join the game.
 *
 * @package Diplomacy\Views\Components\Games\Members
 */
class AllMembersBarComponent extends BaseComponent
{
    protected string $template = 'games/members/occupation_bar.twig';
    protected Game $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function getTemplate(): string
    {
        if ($this->game->phase->isPreGame()) {
            return 'games/members/occupation_bar/pre_game.twig';
        } else {
            return 'games/members/occupation_bar/active.twig';
        }
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        \libHTML::$first = true;
        if ($this->game->phase->isStarted())
        {
            $percentages = $this->game->supplyCenterPercentages();

            $members = [];
            foreach ($percentages as $countryID => $width) {
                if ($width <= 0) continue;

                $members[] = [
                    'country_id' => $countryID,
                    'width' => $width,
                    'first' => \libHTML::first(),
                ];
            }

            return [
                'members' => $members,
            ];
        }
        else
        {
            $countryCount = $this->game->getCountryCount();
            $playerCount = $this->game->getMemberCount();
            $joinedPercent = ceil(($playerCount * 100.0 / $countryCount));

            return [
                'joined_percent' => $joinedPercent,
                'remaining_percent' => 100 - $joinedPercent,
            ];
        }
    }
}