<?php

namespace Diplomacy\Forms\Games;

use Diplomacy\Forms\BaseForm;
use Diplomacy\Services\Request;
use Illuminate\Database\Eloquent\Builder;

class SearchForm extends BaseForm
{
    protected $template = 'forms/games/search_form.twig';
    protected $requestType = Request::TYPE_GET;
    protected $fields = [
        'status'            => [
            'default' => 'all'
        ],
        'user_games'        => [
            'default' => 'all'
        ],
        'round'             => [
            'default' => 'all'
        ],
        'joinable'          => [
            'default' => 'all'
        ],
        'privacy'           => [
            'default' => 'all'
        ],
        'pot_type'          => [
            'type' => 'Games\PotTypeSelect',
            'showAll' => true,
            'default' => '-1',
        ],
        'draw_votes'        => [
            'type' => 'Games\DrawTypeSelect',
            'default' => 'all',
            'showAll' => true,
            'showAllValue' => 'all',
        ],
        'variant'           => [
            'type' => 'Games\VariantSelect',
            'default' => 'all',
            'showAll' => true,
            'showAllValue' => 'all',
        ],
        'excused_turns'     => [
            'default' => -1
        ],
        'anonymity'         => [
            'default' => 'all',
            'type'    => 'select',
            'label'   => 'Anonymity',
            'options' => [
                ['value' => 'all', 'text' => 'All'],
                ['value' => 'yes', 'text' => 'Anonymous'],
                ['value' => 'no', 'text' => 'Non-Anonymous'],
            ],
        ],
        'phase_length_min'  => [
            'type' => 'Games\PhaseLengthSelect',
            'label' => 'Phase Length From',
            'default' => 5
        ],
        'phase_length_max'  => [
            'type' => 'Games\PhaseLengthSelect',
            'label' => 'Phase Length To',
            'default' => 14400
        ],
        'rr_min'            => [
            'default' => 0
        ],
        'rr_max'            => [
            'default' => 100
        ],
        'bet_min'           => [
            'type'      => 'number',
            'label'     => 'Bet Size From',
            'min'       => 0,
            'default'   => '',
        ],
        'bet_max'           => [
            'type'      => 'number',
            'label'     => 'Bet Size To',
            'min'       => 0,
            'default'   => '',
        ],
        'messaging_types'   => [
            'type' => 'checkboxes',
            'label' => 'Messaging Types',
            'default' => ['norm', 'pub', 'none', 'rule'],
            'options' => [
                ['value' => 'norm', 'text' => 'Regular'],
                ['value' => 'pub',  'text' => 'Public Only'],
                ['value' => 'none', 'text' => 'No Messaging'],
                ['value' => 'rule', 'text' => 'Rulebook'],
            ],
        ],
        'tournament_id'     => ['default' => 1],
        'tournament_rounds' => ['default' => 5],
        'user_id'           => ['default' => 0],
        'sort_by'           => ['default' => 'id'],
        'sort_dir'          => ['default' => 'desc'],
    ];

    protected $sortColumns = [
        'id'                        => 'Game ID',
        'name'                      => 'Game Name',
        'pot'                       => 'Pot Size',
        'minimumBet'                => 'Bet',
        'phaseMinutes'              => 'Phase Length',
        'minimumReliabilityRating'  => 'Reliability Rating',
        'watchedGames'              => 'Spectator Count',
        'turn'                      => 'Game Turn',
        'processTime'               => 'Time to Next Phase',
    ];
    protected $anonymityLevels = [
        'all' => 'All',
        'yes' => 'Anonymous',
        'no'  => 'Non-Anonymous',
    ];
    protected $drawVoteOptions = [
        'all'    => 'All',
        'hidden' => 'Hidden Votes',
        'public' => 'Public Votes',
    ];
    protected $potTypes = [
        'all' => 'All',
        'dss' => 'Draw Size Scoring',
        'sos' => 'Sum of Squares',
        'ppsc' => 'Points Per Supply Center',
        'unranked' => 'Unranked',
    ];
    protected $privacyOptions = [
        'all' => 'All',
        'private' => 'Private',
        'public' => 'Public',
    ];
    protected $joinableOptions = [
        'all' => 'All Games',
        'yes' => 'All Joinable Games',
        'active' => 'Active Joinable Games Only',
        'new' => 'New Joinable Games Only',
    ];
    protected $gameStatuses = [
        'all'       => 'All',
        'pre-game'  => 'Pre-Game',
        'active'    => 'All Active',
        'paused'    => 'Paused',
        'running'   => 'Running (excludes paused games)',
        'finished'  => 'All Finished',
        'won'       => 'Won',
        'Drawn'     => 'Drawn',
    ];

    public function beforeRender()
    {
        $this->setPlaceholders([
            'game_statuses' => $this->gameStatuses,
            'joinable_options' => $this->joinableOptions,
            'privacy_options' => $this->privacyOptions,
            'pot_types' => $this->potTypes,
            'draw_vote_options' => $this->drawVoteOptions,
            'variants' => \Config::$variants,
            'anonymity_levels' => $this->anonymityLevels,
            'sort_columns' => $this->sortColumns,
        ]);
    }
}