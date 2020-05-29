<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\Collection;
use Diplomacy\Models\Game;
use Diplomacy\Models\Member;
use Diplomacy\Models\WatchedGame;
use Illuminate\Database\Eloquent\Builder;

/**
 * Service class for searching games
 *
 * @package Diplomacy\Services\Games
 */
class SearchService
{
    public function search(array $values, \User $currentUser)
    {
        $gamesTable = Game::getTableName();
        $query = Game::query();
        $query->selectRaw($gamesTable . '.*');
        $query->selectRaw('(SELECT count(1) FROM ' . WatchedGame::getTableName().' w WHERE w.gameID = ' . $gamesTable . '.id) AS watchedGames');

        $userId = array_key_exists('user_id', $values) ? $values['user_id'] : 0;
        $isModerator = $currentUser->isModerator();

        // handle user games filter
        if (array_key_exists('user_games', $values) && $values['user_games'] == 'include') {
            $query->withUser($currentUser->id);

            // handle filtering by user ID
        } elseif (!empty($userId)) {
            $query->withUser($userId);
            if ($currentUser->id != $userId && !$isModerator) {
                $query->where(function($q) {
                    $q->where('anon', '=', 'No')->orWhere('phase', '=', 'Finished');
                });
            }

            // handle filtering by tournament ID + round
        } elseif (!empty($values['tournament_id'])) {
            $query->forTournament((int)$values['tournament_id'], (int)$values['round']);
        }

        if (array_key_exists('status', $values)) {
            switch ($values['status']) {
                case 'pre-game': $query->preGame(); break;
                case 'active': $query->active(); break;
                case 'paused': $query->paused(); break;
                case 'running': $query->running(); break;
                case 'finished': $query->finished(); break;
                case 'won': $query->finished()->won(); break;
                case 'drawn': $query->finished()->drawn(); break;
            }
        }

        if (array_key_exists('joinable', $values)) {
            switch (strval($values['joinable'])) {
                case 'yes': $query->joinableForUser($currentUser->id, $currentUser->points, $currentUser->reliabilityRating); break;
                case 'active': $query->joinableForUser($currentUser->id, $currentUser->points, $currentUser->reliabilityRating)->active(); break;
                case 'new': $query->joinableForUser($currentUser->id, $currentUser->points, $currentUser->reliabilityRating)->new(); break;
            }
        }

        if (array_key_exists('privacy', $values)) {
            switch (strval($values['privacy'])) {
                case 'private': $query->private(); break;
                case 'public': $query->public(); break;
            }
        }

        if (array_key_exists('pot_type', $values)) {
            switch (strval($values['pot_type'])) {
                case 'dss': $query->where('potType', '=', 'Winner-takes-all'); break;
                case 'sos': $query->where('potType', '=', 'Sum-of-squares'); break;
                case 'ppsc': $query->where('potType', '=', 'Points-per-supply-center'); break;
                case 'unranked': $query->where('potType', '=', 'Unranked'); break;
            }
        }

        if (array_key_exists('draw_votes', $values)) {
            switch (strval($values['draw_votes'])) {
                case 'hidden': $query->where('drawType', 'draw-votes-hidden'); break;
                case 'public': $query->where('drawType', 'draw-votes-public'); break;
            }
        }

        if (array_key_exists('variant', $values) && $values['variant'] != 'all' && !empty($values['variant'])) {
            if (!empty($variantId)) $query->forVariant($variantId);
        }

        if (array_key_exists('excused_turns', $values) && $values['excused_turns'] >= 0) {
            $query->withExcusedMissedTurnsOf(intval($values['excused_turns']));
        }

        if (array_key_exists('anonymity', $values)) {
            switch ($values['anonymity']) {
                case 'yes': $query->anonymous(); break;
                case 'no': $query->notAnonymous(); break;
            }
        }

        if (array_key_exists('phase_length_min', $values)) {
            $query->where('phaseMinutes', '>=', (int)$values['phase_length_min']);
        }
        if (array_key_exists('phase_length_max', $values)) {
            $query->where('phaseMinutes', '<=', (int)$values['phase_length_max']);
        }

        if (array_key_exists('rr_min', $values)) {
            $query->where('minimumReliabilityRating', '>=', (int)$values['rr_min']);
        }
        if (array_key_exists('rr_max', $values)) {
            $query->where('minimumReliabilityRating', '<=', (int)$values['rr_max']);
        }

        if (array_key_exists('bet_min', $values) && !empty($values['bet_min'])) {
            $query->orWhere(Game::raw(intval($values['bet_min'])), '<=', function($q) {
                /** @var Builder $q */
                $q->from(Member::raw(Member::getTableName() . ' AS mBMin'))
                    ->selectRaw('mBMin.bet')
                    ->whereRaw('mBMin.gameID = ' . Game::getTableName() . '.id')
                    ->whereRaw('mBMin.bet > ?', [0])
                    ->limit(1);
            });
        }

        if (array_key_exists('bet_max', $values) && !empty($values['bet_max'])) {
            $query->orWhere(Game::raw(intval($values['bet_max'])), '>=', function($q) {
                /** @var Builder $q */
                $q->from(Member::raw(Member::getTableName() . ' AS mBMax'))
                    ->selectRaw('mBMax.bet')
                    ->whereRaw('mBMax.gameID = ' . Game::getTableName() . '.id')
                    ->whereRaw('mBMax.bet > ?', [0])
                    ->limit(1);
            });
        }

        if (array_key_exists('messaging_types', $values)) {
            $messageTypes = $values['messaging_types'];
            $allowedTypes = [];
            if (in_array('norm', $messageTypes)) $allowedTypes[] = 'Regular';
            if (in_array('pub', $messageTypes)) $allowedTypes[] = 'PublicPressOnly';
            if (in_array('non', $messageTypes)) $allowedTypes[] = 'NoPress';
            if (in_array('rule', $messageTypes)) $allowedTypes[] = 'RulebookPress';
            $query->whereIn('pressType', $allowedTypes);
        }

        $sortBy = array_key_exists('sort_by', $values) ? $values['sort_by'] : 'id';
        $sortDir = array_key_exists('sort_dir', $values) && in_array($values['sort_dir'], ['asc', 'desc']) ? $values['sort_dir'] : 'desc';

        switch (strtolower($sortBy)) {
            case 'id': $query->orderBy($gamesTable . '.id', $sortDir); break;
            case 'name': $query->orderBy($gamesTable . '.name', $sortDir); break;
            case 'pot': $query->orderBy($gamesTable . '.pot', $sortDir); break;
            case 'minimumBet': break;
            case 'phaseMinutes': $query->orderBy($gamesTable . '.phaseMinutes', $sortDir); break;
            case 'minimumReliabilityRating': $query->orderBy($gamesTable . '.minimumReliabilityRating', $sortDir); break;
            case 'watchedGames': $query->orderBy('watchedGames', $sortDir); break;
            case 'turn': $query->orderBy($gamesTable . '.turn', $sortDir); break;
            case 'processtime': $query->orderByRaw('(CASE WHEN g.processStatus = ? THEN (g.pauseTimeRemaining + ?) ELSE g.processTime END) ?', ['Paused', time(), $sortDir]);
        }

        $total = $query->count();
        $query->paginate();
        return new Collection($query->get(), $total);
    }
}