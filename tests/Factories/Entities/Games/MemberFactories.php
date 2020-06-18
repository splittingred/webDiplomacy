<?php

namespace Factories\Entities\Games\Members;

use Diplomacy\Models\Entities\Games\Country;
use Diplomacy\Models\Entities\Games\Member;
use Diplomacy\Models\Entities\Games\UnassignedMember;
use Diplomacy\Models\Entities\User;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

$faker = Faker::instance()->getGenerator();

/** @var FactoryMuffin $fm */
$fm->define(Member::class)->setDefinitions([
    'id' => 1,
    'user' => $fm->instance(User::class),
    'gameId' => 1,
    'country' => new Country(1, 'England'),
]);

/** @var FactoryMuffin $fm */
$fm->define(UnassignedMember::class)->setDefinitions([
    'gameId' => 1,
]);