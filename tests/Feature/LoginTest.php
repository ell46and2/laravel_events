<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function logging_in_with_valid_credentials()
    {
        $user = factory(User::class)->states('approved')->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password')
        ]);
    

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'super-secret-password'
        ]);

        // check that the user is redirected to the correct url
        $response->assertRedirect('/');

        // check that a user is logged in
        $this->assertTrue(Auth::check());

        // check that the logged in user is the correct user
        $this->assertTrue(Auth::user()->is($user));
    }

    /** @test */
    public function logging_in_with_invalid_credentials()
    {
        $user = factory(User::class)->states('approved')->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password')
        ]);
    

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'not-the-right-password'
        ]);

        // check that the user is redirected to the correct url
        $response->assertRedirect('/login');

        // check that the session has an error (laravel sets error for email if password and/or email is incorrect).
        $response->assertSessionHasErrors('email');

        // check that the old input email is prepopulated in the form.
        $this->assertTrue(session()->hasOldInput('email'));

        // check that the old input password is NOT prepopulated in the form.
        $this->assertFalse(session()->hasOldInput('password'));

        // check that no user is logged in
        $this->assertFalse(Auth::check()); 
    }

    /** @test */
    public function logging_in_with_an_account_that_does_not_exist()
    {
        $response = $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'not-the-right-password'
        ]);

        // check that the user is redirected to the correct url
        $response->assertRedirect('/login');

        // check that the session has an error (laravel sets error for email if password and/or email is incorrect).
        $response->assertSessionHasErrors('email');

        // check that no user is logged in
        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function logging_in_when_not_approved()
    {
        $user = factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password')
        ]);
    

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'super-secret-password'
        ]);

        // check that the user is redirected to the correct url
        $response->assertRedirect('/login');

        // check that the session has an error (laravel sets error for email if password and/or email is incorrect).
        $response->assertSessionHasErrors('email');

        // check that no user is logged in
        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function logging_in_when_user_is_blocked()
    {
        $user = factory(User::class)->states('approved')->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password'),
            'blocked' => true
        ]);
    

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'super-secret-password'
        ]);

        // check that the user is redirected to the correct url
        $response->assertRedirect('/login');

        // check that the session has an error (laravel sets error for email if password and/or email is incorrect).
        $response->assertSessionHasErrors('email');

        // check that no user is logged in
        $this->assertFalse(Auth::check());
    }    

    /** @test */
    public function logging_out_the_current_user()
    {
        Auth::login(factory(User::class)->states('approved')->create());

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertFalse(Auth::check());    
    }
}
