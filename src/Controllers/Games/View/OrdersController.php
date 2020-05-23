<?php

namespace Diplomacy\Controllers\Games\View;

use Diplomacy\Controllers\Games\View\BaseController;
use Diplomacy\Models\Collection;
use Diplomacy\Models\Order;
use Diplomacy\Services\Games\OrdersService;

class OrdersController extends BaseController
{
    protected $template = 'pages/games/view/orders.twig';

    /** @var OrdersService */
    protected $orders;

    public function setUp()
    {
        $this->orders = new OrdersService();
        parent::setUp();
    }

    public function call()
    {
        $orders = $this->orders->getForGame($this->game->id);

        return [
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
        /** @var Order $order */
        foreach ($orders as $order) {
            $turn = floatval($order->turn);
            if ($order->isInBuildPhase()) $turn += .5;
            $turn = strval($turn);

            if (empty($summary[$turn])) {
                $summary[$turn] = [
                    'countries' => [],
                    'id' => $turn,
                    'name' => $order->getTurnAsDate(),
                    'phase' => $order->getPhaseName(),
                ];
            }

            if (!array_key_exists($order->countryID, $summary[$turn]['countries'])) {
                $summary[$turn]['countries'][$order->countryID] = $order->getCountryName();
            }
        }
        return $summary;
    }

    private function structureOrders(Collection $orders) : array
    {
        $list = [];
        /** @var Order $order */
        foreach ($orders as $order) {
            $turn = floatval($order->turn);
            if ($order->isInBuildPhase()) $turn += .5;
            $turn = strval($turn);

            if (empty($list[$turn])) {
                $list[$turn] = [
                    'id' => $turn,
                    'name' => $order->getTurnAsDate(),
                    'phase' => $order->getPhaseName(),
                    'countries' => [],
                ];
            }

            if (!array_key_exists($order->countryID, $list[$turn]['countries'])) {
                $list[$turn]['countries'][$order->countryID] = [
                    'id' => $order->countryID,
                    'name' => $order->getCountryName(),
                    'orders' => [],
                ];
            }
            $list[$turn]['countries'][$order->countryID]['orders'][] = $order;
        }
        return $list;
    }
}