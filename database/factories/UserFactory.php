<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    return [
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'first_name' => 'James',
    	'last_name' => 'Ford',
    	'telephone' => '01242 222333',
    	'address_1' => '123 street',
    	'address_2' => 'Some place',
    	'address_3' => 'Some town',
    	'city' => 'Cheltenham',
    	'postcode' => 'GL50 1ST',
    ];
});

$factory->state(App\User::class, 'admin', function($faker) {
	return [
		'is_admin' => true,
		'approved' => true
	];
});

$factory->state(App\User::class, 'approved', function($faker) {
	return [
		'approved' => true
	];
});
