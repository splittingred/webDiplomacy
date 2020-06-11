<?php

namespace Factories\Entities\Games\Members;

use Diplomacy\Models\Entities\Games\Members\Status;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

$faker = Faker::instance()->getGenerator();

/** @var FactoryMuffin $fm */
$fm->define(Status::class)->setDefinitions([
    'type' => Status::STATUS_PLAYING,
]);