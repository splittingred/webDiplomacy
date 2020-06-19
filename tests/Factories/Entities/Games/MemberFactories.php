<?php

namespace Factories\Entities\Games\Members;

use Diplomacy\Models\Entities\Games\Country;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\Entities\Games\Members\OrdersState;
use Diplomacy\Models\Entities\Games\Members\Status;
use Diplomacy\Models\Entities\Games\UnassignedMember;
use Diplomacy\Models\Entities\User;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

/** @var FactoryMuffin $fm */

$faker = Faker::instance()->getGenerator();

// Member with ready orders
$definition = $fm->define(Member::class);
$definition->setDefinitions([
    'id' => 1,
    'user' => $fm->instance(User::class),
    'gameId' => 1,
    'country' => function($object, $saved) use ($fm) {
        return $fm->instance(Country::class);
    },
    'status' => function($object, $saved) use ($fm) {
        return $fm->instance(Status::class);
    },
    'ordersState' => function($object, $saved) use ($fm) {
        return $fm->instance('saved-ready-completed:'.OrdersState::class);
    },
    'unitCount' => 3,
    'supplyCenterCount' => 3,
]);

// Member with unsubmitted orders
$definition = $fm->define('unsubmitted-orders:'.Member::class);
$definition->setDefinitions([
    'id' => 1,
    'user' => $fm->instance(User::class),
    'gameId' => 1,
    'country' => function($object, $saved) use ($fm) {
        return $fm->instance(Country::class);
    },
    'status' => function($object, $saved) use ($fm) {
        return $fm->instance(Status::class);
    },
    'ordersState' => function($object, $saved) use ($fm) {
        return $fm->instance('unsubmitted:'.OrdersState::class);
    },
]);

/** @var FactoryMuffin $fm */
$fm->define(UnassignedMember::class)->setDefinitions([
    'gameId' => 1,
]);