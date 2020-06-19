<?php

namespace Factories\Entities\Games\Members;

use Diplomacy\Models\Entities\Games\Country;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

$faker = Faker::instance()->getGenerator();

/** @var FactoryMuffin $fm */
$definition = $fm->define(Country::class);
$definition->setDefinitions([
    'id' => 1,
    'name' => 'England',
]);
$definition->setMaker(function($class) {
    return new $class(1, 'England');
});