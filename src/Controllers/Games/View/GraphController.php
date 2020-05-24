<?php

namespace Diplomacy\Controllers\Games\View;

use Diplomacy\Controllers\Games\View\BaseController;
use Diplomacy\Models\Collection;
use Diplomacy\Services\Games\GraphService;

class GraphController extends BaseController
{
    protected $template = 'pages/games/view/graph.twig';

    /** @var GraphService */
    protected $graphService;

    public function setUp()
    {
        $this->graphService = new GraphService();
        parent::setUp();
    }

    public function call()
    {
        return [
            'ratios' => $this->graphService->getSupplyCenterOwnershipData($this->game->id, $this->variant->mapID, $this->game->turn),
        ];
    }
}