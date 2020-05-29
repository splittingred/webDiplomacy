<?php

namespace Diplomacy\Controllers\Games\Search;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Models\Collection;
use Diplomacy\Models\Game;
use Diplomacy\Forms\Games\SearchForm;
use Illuminate\Database\Eloquent\Builder;

class IndexController extends BaseController
{
    use HasGamesTab;
    public $template = 'pages/games/list/search.twig';
    /** @var SearchForm $searchForm */
    public $searchForm;

    public function setUp()
    {
        $this->searchForm = new SearchForm($this->request, $this->renderer);
        parent::setUp();
    }

    public function call()
    {
        $collection = $this->getGames($this->searchForm);
        return [
            'games' => $collection->getEntities(),
            'total_pages' => $this->getTotalPages($collection->getTotal()),
            'pagination' => $this->getPagination($collection->getTotal()),
            'tabs' => $this->getGamesTabs('search'),
            'search_form' => $this->searchForm->render(),
        ];
    }

    public function getGames(SearchForm $form) : Collection
    {
        $query = Game::query();

        // we're going to be doing a _lot_ of form processing, so let's just grab this once to limit array iteration
        $values = $form->getValues();
        $userId = $values['user_id'];
        $isModerator = $this->currentUser->isModerator();

        // handle user games filter
        if ($values['user_games'] == 'include') {
            $query->withUser($this->currentUser->id);

        // handle filtering by user ID
        } elseif (!empty($userId)) {
            $query->withUser($userId);
            if ($this->currentUser->id != $userId && !$isModerator) {
                $query->where(function($q) {
                    $q->where('anon', '=', 'No')->orWhere('phase', '=', 'Finished');
                });
            }

        // handle filtering by tournament ID + round
        } elseif (!empty($values['tournament_id'])) {
            $query->forTournament((int)$values['tournament_id'], (int)$values['round']);
        }

        switch ($values['status']) {
            case 'pre-game': $query->preGame(); break;
            case 'active': $query->active(); break;
            case 'paused': $query->paused(); break;
            case 'running': $query->running(); break;
            case 'finished': $query->finished(); break;
            case 'won': $query->finished()->won(); break;
            case 'drawn': $query->finished()->drawn(); break;
            default: break;
        }

        switch ($values['joinable']) {
            case 'yes': $query->joinableForUser($this->currentUser->id, $this->currentUser->points, $this->currentUser->reliabilityRating); break;
            case 'active': $query->joinableForUser($this->currentUser->id, $this->currentUser->points, $this->currentUser->reliabilityRating)->active(); break;
            case 'new': $query->joinableForUser($this->currentUser->id, $this->currentUser->points, $this->currentUser->reliabilityRating)->new(); break;
            default: break;
        }

        var_dump($query->toSql());
        echo "<br /><br />\n";
        var_dump($query->getBindings());
        echo "<br /><br />\n";
        var_dump($form->getValues());
        die();

        $query->orderBy('id', 'desc');

        $total = $query->count();
        $query->paginate();
        return new Collection($query->get(), $total);

    }
}
