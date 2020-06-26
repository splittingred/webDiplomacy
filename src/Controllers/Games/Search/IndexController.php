<?php

namespace Diplomacy\Controllers\Games\Search;

use Diplomacy\Controllers\BaseController;
use Diplomacy\Models\Collection;
use Diplomacy\Forms\Games\SearchForm;
use Diplomacy\Services\Games\SearchService;

/**
 * Games search page
 *
 * @package Diplomacy\Controllers\Games\Search
 */
class IndexController extends BaseController
{
    use HasGamesTab;

    public string $template = 'pages/games/list/search.twig';
    protected SearchService $searchService;

    public function setUp(): void
    {
        $this->makeForm(SearchForm::class);
        $this->searchService = new SearchService();
        parent::setUp();
    }

    public function call(): array
    {
        $collection = $this->getGames($this->form);
        return [
            'games' => $collection->getEntities(),
            'total_pages' => $this->getTotalPages($collection->getTotal()),
            'pagination' => $this->getPagination($collection->getTotal()),
            'tabs' => $this->getGamesTabs('search'),
            'search_form' => $this->form->render(),
        ];
    }

    /**
     * @param SearchForm $form
     * @return Collection
     */
    public function getGames(SearchForm $form) : Collection
    {
        $values = $form->getValues();
        return $this->searchService->search($values, $this->currentUser);
    }
}
