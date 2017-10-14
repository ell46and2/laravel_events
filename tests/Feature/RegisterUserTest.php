<?php

namespace Tests\Feature;

use App\Mail\Approval;
use App\Mail\NewUserAdminEmail;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_register_as_a_user()
    {
    	Mail::fake();

    	$admin = factory(User::class)->states('admin')->create([
    		'email' => 'james@example.com'
    	]);

        $response = $this->post('/register', [
        	'first_name' => 'Jane',
        	'last_name' => 'Doe',
        	'email' => 'jane@example.com',
        	'telephone' => '01242 222333',
        	'address_1' => '123 street',
        	'address_2' => 'Some place',
        	'address_3' => 'Some town',
        	'city' => 'Cheltenham',
        	'postcode' => 'GL50 1ST',
        	'password' => 'super-secret-password',
        	'password_confirmation' => 'super-secret-password',
        ]);

        tap(User::find(2), function($user) use ($response, $admin) {
        	$response->assertStatus(302);
        	$response->assertRedirect("/register/approval");

        	$this->assertEquals('Jane', $user->first_name);
        	$this->assertEquals('Doe', $user->last_name);
        	$this->assertEquals('jane@example.com', $user->email);
        	$this->assertEquals('01242 222333', $user->telephone);
        	$this->assertEquals('123 street', $user->address_1);
        	$this->assertEquals('Some place', $user->address_2);
        	$this->assertEquals('Some town', $user->address_3);
        	$this->assertEquals('Cheltenham', $user->city);
        	$this->assertEquals('GL50 1ST', $user->postcode);

        	$this->assertEquals(0, $user->is_admin);
        	$this->assertEquals(0, $user->approved);

        	// email sent to user to say their account is pending approval
        	Mail::assertSent(Approval::class, function($mail) use ($user) {
            	return $mail->hasTo('jane@example.com') && $mail->user->id == $user->id;
        	});

        	// email sent to admin to say a new user has registered
        	Mail::assertSent(NewUserAdminEmail::class, function($email) use ($admin) {
            	return $email->hasTo('james@example.com');
        	});
        });
    }
}