<?php

namespace Factories;

use Diplomacy\Models\User;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

$faker = Faker::instance()->getGenerator();

/** @var FactoryMuffin $fm */
$definitions = $fm->define(User::class);
$definitions->setDefinitions([
    'id'                    => 1,
    'username'              => $faker->userName,
    'email'                 => $faker->email,
    'points'                => 0,
    'comment'               => '',
    'homepage'              => $faker->url,
    'hideEmail'             => false,
    'timeJoined'            => time() - 3600,
    'locale'                => 'English',
    'timeLastSessionEnded'  => time() - 60,
    'lastMessageIDViewed'   => 0,
    'password'              => hex2bin(User::hashPassword('test1234')), // this for some reason doesn't work
    'emergencyPauseDate'    => 1,
]);