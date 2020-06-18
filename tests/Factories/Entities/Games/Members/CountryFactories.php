<?php

namespace Factories\Entities\Games\Members;

use Diplomacy\Models\Entities\Games\Country;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

$faker = Faker::instance()->getGenerator();

/** @var FactoryMuffin $fm */
$fm->define(Country::class)->setDefinitions([
    'id' => 1,
    'name' => 'England',
]);