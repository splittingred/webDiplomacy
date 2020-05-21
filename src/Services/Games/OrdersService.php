<?php

namespace Diplomacy\Services\Games;

use Diplomacy\Models\Collection;
use Diplomacy\Models\Order;

class OrdersService
{
    protected $database;

    public function __construct(\Database $database)
    {
        $this->database = $database;
    }

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

        $query = $this->database->sql_tabl("
            SELECT 
               moves.*,
               LOWER(moves.unitType) AS unitType,
               LOWER(moves.type) AS type,
               games.variantID AS variantId
		    FROM wD_MovesArchive AS moves 
              INNER JOIN wD_Games AS games ON games.id = moves.gameID
            WHERE gameID = $gameId 
            ORDER BY turn DESC, countryID ASC
        ");

        $totalQuery = "SELECT COUNT(moves.turn) FROM wD_MovesArchive moves WHERE moves.gameID = $gameId";
        list($total) = $this->database->sql_row($totalQuery);

        $orders = [];
        while ($row = $this->database->tabl_hash($query)) {
            $order = Order::fromRow($row);
            $order->setVariant($variant);
            $order->setGameBoard($gameBoard);
            $orders[] = $order;
        }

        return new Collection($orders, $total);
    }
}