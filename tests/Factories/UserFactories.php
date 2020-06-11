<?php
use Faker\Generator as Faker;
use Support\TestCase;
use Diplomacy\Models\User;

TestCase::getFactory()->define(User::class, function (Faker $faker) {
    return [
        'id' => 1,
        'username' => 'test'
    ];
});
