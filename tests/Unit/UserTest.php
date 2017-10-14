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
}
