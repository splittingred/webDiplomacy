<?php

namespace Diplomacy\Controllers\Games\View;

use Diplomacy\Controllers\Games\View\BaseController;
use Diplomacy\Models\Collection;
use Diplomacy\Services\Games\OrdersService;

class OrdersController extends BaseController
{
    protected $template = 'pages/games/view/orders.twig';

    /** @var OrdersService */
    protected $orders;

    public function setUp()
    {
        $this->orders = new OrdersService($this->database);
    }

    public function call()
    {
        $gameId = $this->request->get('id');
        require_once 'objects/game.php';
        require_once 'board/chatbox.php';
        require_once 'gamepanel/gameboard.php';
        $Variant = \libVariant::loadFromGameID($gameId);
        \libVariant::setGlobals($Variant);
        $game = $Variant->panelGameBoard($gameId);

        $orders = $this->orders->getForGame($gameId);

        return [
            'game' => $game,
            'orders' => $this->structureOrders($orders),
            'summary' => $this->sortOrdersAsIndex($orders),
            'phases' => [
                'Diplomacy',
                'Retreats',
                'Unit-placement',
            ],
        ];
    }

    private function sortOrdersAsIndex(Collection $orders) : array
    {
        $summary = [];
        foreach ($orders as $order) {
            if (empty($summary[$order->turn])) {
                $summary[$order->turn] = [
                    'countries' => [],
                    'id' => $order->turn,
                    'name' => $order->turnAsDate(),
                ];
            }

            if (!array_key_exists($order->countryID, $summary[$order->turn]['countries'])) {
                $summary[$order->turn]['countries'][$order->countryID] = $order->countryName();
            }
        }
        return $summary;
    }

    private function structureOrders(Collection $orders) : array
    {
        $list = [];
        foreach ($orders as $order) {
            if (empty($list[$order->turn])) {
                $list[$order->turn] = [
                    'id' => $order->turn,
                    'name' => $order->turnAsDate(),
                    'phase' => $order->phaseName(),
                    'countries' => [],
                ];
            }

            if (!array_key_exists($order->countryID, $list[$order->turn]['countries'])) {
                $list[$order->turn]['countries'][$order->countryID] = [
                    'id' => $order->countryID,
                    'name' => $order->countryName(),
                    'orders' => [],
                ];
            }
            $list[$order->turn]['countries'][$order->countryID]['orders'][] = $order;
        }
        return $list;
    }
}