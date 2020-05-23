<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\Collection;
use Diplomacy\Models\ArchivedMove;

class ArchivedMovesService
{
    /**
     * @param int $gameId
     * @return Collection
     */
    public function getForGame(int $gameId) : Collection
    {
        // Needed for now until the compositional model is smarter
        $variant = \libVariant::loadFromGameID($gameId);
        \libVariant::setGlobals($variant);
        $gameBoard = $variant->panelGameBoard($gameId);
        $variant->initialize();

        $query = ArchivedMove::join('wD_Games', 'wD_Games.id', '=', 'wD_MovesArchive.gameID')
            ->where('wD_MovesArchive.gameID', $gameId);

        $total = $query->count();
        $query = $query->select(
            'wD_MovesArchive.*',
            ArchivedMove::raw('LOWER(wD_MovesArchive.unitType) AS unitType'),
            ArchivedMove::raw('LOWER(wD_MovesArchive.type) AS type'),
            ArchivedMove::raw('wD_Games.variantID AS variantId'),
        )
            ->orderBy('turn', 'desc')
            ->orderBy('countryID', 'ASC');

        $archivedMoves = $query->get();
        foreach ($archivedMoves as $archivedMove) {
            $archivedMove->setVariant($variant);
            $archivedMove->setGameBoard($gameBoard);
            $orders[] = $archivedMove;
        }

        return new Collection($archivedMoves, $total);
    }
}