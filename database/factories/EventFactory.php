<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Event::class, function (Faker $faker) {
    return [
        'name' => 'Foxy',
    	'address_1' => '123 street',
    	'address_2' => 'Some place',
    	'address_3' => 'Some town',
    	'city' => 'Cheltenham',
    	'date' => Carbon::parse('2017-11-20 10:00am'),
    ];
});
