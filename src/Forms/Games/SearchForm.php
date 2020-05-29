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
        'status'            => 'all',
        'user_games'        => 'all',
        'round'             => 'all',
        'joinable'          => 'all',
        'privacy'           => 'all',
        'pot_type'          => 'all',
        'draw_votes'        => 'all',
        'variant'           => 'all',
        'excused_turns'     => -1,
        'anonymity'         => 'all',
        'phase_length_min'  => 5,
        'phase_length_max'  => 14400,
        'rr_min'            => 0,
        'rr_max'            => 100,
        'bet_min'           => '',
        'bet_max'           => '',
        'messaging_types'   => ['norm', 'pub', 'none', 'rule'],
        'tournament_id'     => 1,
        'tournament_rounds' => 5,
        'user_id'           => 0,
        'sort_by'           => 'id',
        'sort_dir'          => 'desc',
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
    protected $messagingTypes = [
        'norm'  => 'Regular',
        'pub'   => 'Public Only',
        'none'  => 'No Messaging',
        'rule'  => 'Rulebook',
    ];
    protected $phaseLengths = [
        '5' => '5 Minutes',
        '7' => '10 Minutes',
        '15' => '15 Minutes',
        '20' => '20 Minutes',
        '30' => '30 Minutes',
        '60' => '1 Hour',
        '120' => '2 Hours',
        '240' => '4 Hours',
        '480' => '8 Hours',
        '600' => '10 Hours',
        '720' => '12 Hours',
        '840' => '14 Hours',
        '960' => '16 Hours',
        '1080' => '18 Hours',
        '1200' => '20 Hours',
        '1320' => '22 Hours',
        '1440' => '1 Day',
        '2880' => '2 Days',
        '4320' => '3 Days',
        '5760' => '4 Days',
        '7200' => '5 Days',
        '8640' => '6 Days',
        '10080' => '7 Days',
        '14400' => '10 Days',
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
            'phase_lengths' => $this->phaseLengths,
            'messaging_types' => $this->messagingTypes,
            'sort_columns' => $this->sortColumns,
        ]);
    }
}