<?php

namespace Diplomacy\Controllers\Games\View;

use Diplomacy\Controllers\Games\View\BaseController;
use Diplomacy\Models\Collection;
use Diplomacy\Models\ArchivedMove;
use Diplomacy\Services\Games\ArchivedMovesService;

class OrdersController extends BaseController
{
    protected $template = 'pages/games/view/orders.twig';

    /** @var ArchivedMovesService */
    protected $archivedMoves;

    public function setUp()
    {
        $this->archivedMoves = new ArchivedMovesService();
        parent::setUp();
    }

    public function call()
    {
        $moves = $this->archivedMoves->getForGame($this->game->id);

        return [
            'orders' => $this->structureMoves($moves),
            'summary' => $this->sortMovesAsIndex($moves),
            'phases' => [
                'Diplomacy',
                'Retreats',
                'Unit-placement',
            ],
        ];
    }

    private function sortMovesAsIndex(Collection $archivedMoves) : array
    {
        $summary = [];
        /** @var ArchivedMove $archivedMove */
        foreach ($archivedMoves as $archivedMove) {
            $turn = floatval($archivedMove->turn);
            if ($archivedMove->isInRetreatPhase()) $turn += .25;
            if ($archivedMove->isInBuildPhase()) $turn += .5;
            $turn = strval($turn);

            if (empty($summary[$turn])) {
                $summary[$turn] = [
                    'countries' => [],
                    'id' => $turn,
                    'name' => $archivedMove->getTurnAsDate(),
                    'phase' => $archivedMove->getPhaseName(),
                ];
            }

            if (!array_key_exists($archivedMove->countryID, $summary[$turn]['countries'])) {
                $summary[$turn]['countries'][$archivedMove->countryID] = $archivedMove->getCountryName();
            }
        }
        return $summary;
    }

    private function structureMoves(Collection $archivedMoves) : array
    {
        $list = [];
        /** @var ArchivedMove $archivedMoves */
        foreach ($archivedMoves as $archivedMove) {
            echo $archivedMove->id;
            $turn = floatval($archivedMove->turn);
            if ($archivedMove->isInRetreatPhase()) $turn += .25;
            if ($archivedMove->isInBuildPhase()) $turn += .5;
            $turn = strval($turn);

            if (empty($list[$turn])) {
                $list[$turn] = [
                    'id' => $turn,
                    'name' => $archivedMove->getTurnAsDate(),
                    'phase' => $archivedMove->getPhaseName(),
                    'countries' => [],
                ];
            }

            if (!array_key_exists($archivedMove->countryID, $list[$turn]['countries'])) {
                $list[$turn]['countries'][$archivedMove->countryID] = [
                    'id' => $archivedMove->countryID,
                    'name' => $archivedMove->getCountryName(),
                    'orders' => [],
                ];
            }
            $list[$turn]['countries'][$archivedMove->countryID]['orders'][] = $archivedMove;
        }
        return $list;
    }
}