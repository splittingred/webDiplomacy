<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\Collection;
use Diplomacy\Models\Order;

class OrdersService
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

        $query = Order::join('wD_Games', 'wD_Games.id', '=', 'wD_MovesArchive.gameID')
            ->where('gameID', $gameId);

        $total = $query->count();
        $query = $query->select(
            'wD_MovesArchive.*',
            Order::raw('LOWER(wD_MovesArchive.unitType) AS unitType'),
            Order::raw('LOWER(wD_MovesArchive.type) AS type'),
            Order::raw('wD_Games.variantID AS variantId'),
        )
            ->orderBy('turn', 'desc')
            ->orderBy('countryID', 'ASC');

        $orders = $query->get();
        foreach ($orders as $order) {
            $order->setVariant($variant);
            $order->setGameBoard($gameBoard);
            $orders[] = $order;
        }

        return new Collection($orders, $total);
    }
}