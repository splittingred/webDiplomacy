<?php

namespace Factories;

use Diplomacy\Models\Game;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

$faker = Faker::instance()->getGenerator();

/** @var FactoryMuffin $fm */
$definitions = $fm->define(Game::class);
$definitions->setDefinitions([
    'variantID'             => 1,
    'name'                  => $faker->words(3, true),
]);