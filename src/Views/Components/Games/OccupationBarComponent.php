<?php

namespace Diplomacy\Views\Components\Games;

use Diplomacy\Models\Entities\Game;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Views\Components\BaseComponent;

/**
 * Render a short view of country status and SC totals for each country in a friendly format
 *
 * @package Diplomacy\Views\Components\Games\Members
 */
class OccupationBarComponent extends BaseComponent
{
    protected string $template = 'games/members/occupation_bar/active.twig';
    protected Game $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function attributes(): array
    {
        \libHTML::$first = true;
        if ($this->game->phase->isStarted())
        {
            $percentages = $this->getPercentages();

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
            $this->template = 'games/members/occupation_bar/pre_game.twig';
            $memberCount = $this->game->getMemberCount();
            $countryCount = $this->game->getCountryCount();
            $joinedPercent = ceil(($memberCount * 100.0) / $countryCount);
            return [
                'joined_percent' => $joinedPercent,
                'remaining_percent' => 100.0 - $joinedPercent,
            ];
        }
    }

    protected function getPercentages(): array
    {
        $percentages = [];

        $totalSupplyCenters = $this->game->variant->supplyCenterCount;
        $countryCount = $this->game->getCountryCount();

        if ($totalSupplyCenters == 0) // We must be pre-game
        {
            for ($countryID = 1; $countryID <= $countryCount; $countryID++) {
                $percentages[$countryID] = round((3 / (3 * 6 + 4)) * 100);
            }
        }
        else
        {
            for ($countryID = 1; $countryID <= $countryCount; $countryID++) {
                $member = $this->game->members->byCountryId($countryID);
                $percentages[$countryID] = round(($member->supplyCenterCount / $totalSupplyCenters) * 100);
            }
        }

        $sum = 0;
        foreach($percentages as $countryID => $percent)
            $sum += $percentages[$countryID];

        // Add the rounding error onto a countryID with a few SCs, where it won't be noticed
        foreach ($percentages as $countryID => $percent) {
            if ($percent > (1/8*100))
            {
                $percentages[$countryID] += 100 - $sum;
                break;
            }
        }

        return $percentages;
    }
}