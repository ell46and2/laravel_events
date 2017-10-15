<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserTest extends TestCase
{
	use DatabaseMigrations;
	
    /** @test */
    public function can_get_the_admin_user()
    {
       $adminUser = factory(User::class)->states('admin')->create();

       $this->assertEquals($adminUser->id, User::admin()->id);
    }

    /** @test */
    public function a_user_can_be_approved()
    {
    	$user = factory(User::class)->create();

    	$this->assertEquals(0, $user->approved);

    	$user->approve();

		$this->assertEquals(1, $user->approved);    	
    }

    /** @test */
    public function a_user_can_be_blocked()
    {
        $user = factory(User::class)->states('approved')->create();

        $this->assertEquals(0, $user->blocked);

        $user->block();

        $this->assertEquals(1, $user->blocked);        
    }

    /** @test */
    public function a_user_can_be_unblocked()
    {
        $user = factory(User::class)->states('approved')->create();

        $user->block();

        $this->assertEquals(1, $user->blocked);

        $user->unblock();

        $this->assertEquals(0, $user->blocked);        
    }
}
