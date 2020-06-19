<?php

namespace Factories\Entities\Games\Members;

use Diplomacy\Models\Entities\Games\Members\OrdersState;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

/** @var FactoryMuffin $fm */
$faker = Faker::instance()->getGenerator();
$init = function($class) {
    return new $class([]);
};

// Saved state
$fm->define(OrdersState::class)->setMaker($init)->setDefinitions([
    'states' => [OrdersState::STATE_SAVED],
]);

// None (no orders to file)
$fm->define('none:'.OrdersState::class)->setMaker($init)->setDefinitions([
    'states' => [OrdersState::STATE_NONE],
]);

// Ready and saved but not completed
$fm->define('ready:'.OrdersState::class)->setMaker($init)->setDefinitions([
    'states' => [OrdersState::STATE_SAVED, OrdersState::STATE_READY],
]);

// Saved and completed but not ready
$fm->define('saved-completed-not-ready:'.OrdersState::class)->setMaker($init)->setDefinitions([
    'states' => [OrdersState::STATE_SAVED, OrdersState::STATE_COMPLETED],
]);

// Ready, saved, and completed
$fm->define('saved-ready-completed:'.OrdersState::class)->setMaker($init)->setDefinitions([
    'states' => [OrdersState::STATE_SAVED, OrdersState::STATE_READY, OrdersState::STATE_COMPLETED],
]);

// unsubmitted
$fm->define('unsubmitted:'.OrdersState::class)->setMaker($init)->setDefinitions([
    'states' => [],
]);