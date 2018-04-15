<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'verified' => true,
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Payment::class, function (Faker\Generator $faker) {

    return [
        'user_id' => $faker->unique()->randomDigitNotNull,
        'picture' => 'tmp/' . $faker->uuid . 'jpeg',
    ];
});

$factory->define(App\Category::class, function (Faker\Generator $faker) {

    $categories = array('Fragile', 'Dangerous', 'Parts', 'Stackable', 'Non-Stackable');

    return [
        'name' => $faker->randomElement($categories),
        'volume' => $faker->numberBetween($min = 100, $max = 9000),
        'status' => 'true',
    ];
});

$factory->define(App\Courier::class, function (Faker\Generator $faker) {

    $courier = array('Skynet Express﻿', 'Pos Laju', 'City-Link', 'FedEx', 'GD Express');

    return [
        'name' => $faker->randomElement($courier),
        'status' => 'true',
    ];
});

$factory->define(App\Lot::class, function (Faker\Generator $faker) {

    $category = factory('App\Category')->create();

    return [
        'user_id' => $faker->unique()->randomDigitNotNull,
        'category_id' => $category->id,
        'name' => 'TEST LOT ' . $faker->unique()->randomDigit,
        'volume' => $category->volume,
        'left_volume' => $category->volume,
        'rental_duration' => $faker->numberBetween($min = 90, $max = 200),
        'expired_at' => null,
        'status' => 'true',
    ];
});

$factory->define(App\Product::class, function (Faker\Generator $faker) {

    return [
        'user_id' => $faker->randomDigitNotNull,
        'name' => $faker->name,
        'height' => $faker->numberBetween($min = 1, $max = 50),
        'length' => $faker->numberBetween($min = 1, $max = 50),
        'width' => $faker->numberBetween($min = 1, $max = 50),
        'sku' => 'SKU' . $faker->randomNumber($nbDigits = 4),
        'picture' => 'tmp/' . $faker->uuid . 'jpeg',
        'status' => 'true',
    ];
});