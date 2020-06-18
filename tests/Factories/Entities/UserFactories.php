<?php

namespace Factories\Entities\Games\Members;

use Diplomacy\Models\Entities\User;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

$faker = Faker::instance()->getGenerator();

/** @var FactoryMuffin $fm */
$fm->define(User::class)->setDefinitions([
    'id' => 1,
]);