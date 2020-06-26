<?php

namespace Diplomacy\Controllers\Games\View;

use Diplomacy\Controllers\Games\View\BaseController;
use Diplomacy\Models\Collection;
use Diplomacy\Services\Games\GraphService;

class GraphController extends BaseController
{
    protected string $template = 'pages/games/view/graph.twig';
    protected GraphService $graphService;

    public function setUp(): void
    {
        $this->graphService = new GraphService();
        parent::setUp();
    }

    public function call(): array
    {
        return [
            'ratios' => $this->graphService->getSupplyCenterOwnershipData($this->game->id, $this->variant->mapID, $this->game->turn),
        ];
    }
}