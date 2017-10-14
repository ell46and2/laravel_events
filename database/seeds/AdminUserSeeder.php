<?php

use App\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
        	'email' => 'james@example.com',
	        'password' => bcrypt('secret'),
	        'first_name' => 'James',
	    	'last_name' => 'Ford',
	    	'telephone' => '01242 222333',
	    	'address_1' => '123 street',
	    	'address_2' => 'Some place',
	    	'address_3' => 'Some town',
	    	'city' => 'Cheltenham',
	    	'postcode' => 'GL50 1ST',
	    	'approved' => true,
	    	'is_admin' => true,	
        ])->save();
    }
}
