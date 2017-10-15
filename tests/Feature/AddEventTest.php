<?php

namespace Tests\Feature;

use App\Event;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class AddEventTest extends TestCase
{
    use DatabaseMigrations;

    private function validParams($overrides = [])
    {
    	return array_merge([
        	'name' => 'Foxy',
        	'address_1' => '123 street',
        	'address_2' => 'Some place',
        	'address_3' => 'Some town',
        	'city' => 'Cheltenham',
        	'date' => '2017-11-20',
        	'time' => '10:00am'
        ], $overrides);
    }

    /** @test */
    public function admin_users_can_view_the_add_event_form()
    {
    	$adminUser = factory(User::class)->states('admin')->create();

    	$response = $this->actingAs($adminUser)->get('/admin/events');

    	$response->assertStatus(200);
    }

    /** @test */
    public function general_users_cannot_view_the_add_event_form()
    {
    	$generalUser = factory(User::class)->create();

    	$response = $this->actingAs($generalUser)->get('/admin/events');

    	$response->assertStatus(403); //forbidden
    }

    /** @test */
    public function guests_cannot_view_the_add_event_form()
    {
    	$response = $this->get('/admin/events');

    	$response->assertStatus(403); //forbidden
    }



    /** @test */
    public function adding_a_valid_event()
    {
        $user = factory(User::class)->states('admin')->create();

        $response = $this->actingAs($user)->post('/admin/events', [
        	'name' => 'Foxy',
        	'address_1' => '123 street',
        	'address_2' => 'Some place',
        	'address_3' => 'Some town',
        	'city' => 'Cheltenham',
        	'date' => '2017-11-20',
        	'time' => '10:00am'
        ]);

        tap(Event::first(), function($event) use ($response, $user) {
        	$response->assertStatus(302);
        	$response->assertRedirect("/admin/events");

        	$this->assertTrue($event->user->is($user));

        	$this->assertEquals('Foxy', $event->name);
        	$this->assertEquals('123 street', $event->address_1);
        	$this->assertEquals('Some place', $event->address_2);
        	$this->assertEquals('Some town', $event->address_3);
        	$this->assertEquals('Cheltenham', $event->city);
        	$this->assertEquals(Carbon::parse('2017-11-20 10:00am'), $event->date);
        });
    }

    /** @test */
    public function guests_cannot_add_an_event()
    {
        $response = $this->post('/admin/events', $this->validParams());

        $response->assertStatus(403); //forbidden
    }

    /** @test */
    public function general_users_cannot_add_an_event()
    {
    	$user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/admin/events', $this->validParams());

        $response->assertStatus(403); //forbidden
    }

    /** @test */
    public function name_is_required()
    {
        $user = factory(User::class)->states('admin')->create();

        $response = $this->actingAs($user)->from('/admin/events/new')->post('/admin/events', $this->validParams([
        		'name' => ''
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/admin/events/new');
        $response->assertSessionHasErrors('name');
        $this->assertEquals(0, Event::count());
    }

    /** @test */
    public function address_1_is_required()
    {
        $user = factory(User::class)->states('admin')->create();

        $response = $this->actingAs($user)->from('/admin/events/new')->post('/admin/events', $this->validParams([
        		'address_1' => ''
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/admin/events/new');
        $response->assertSessionHasErrors('address_1');
        $this->assertEquals(0, Event::count());
    }

    /** @test */
    public function address_2_is_optional()
    {
        $user = factory(User::class)->states('admin')->create();

        $response = $this->actingAs($user)->from('/admin/events/new')->post('/admin/events', $this->validParams([
        		'address_2' => ''
        ]));

        tap(Event::first(), function($event) use ($response, $user) {
        	$response->assertStatus(302);
        	$response->assertRedirect("/admin/events");

        	$this->assertTrue($event->user->is($user));

        	$this->assertNull($event->address_2);
        });
    }

    /** @test */
    public function address_3_is_optional()
    {
        $user = factory(User::class)->states('admin')->create();

        $response = $this->actingAs($user)->from('/admin/events/new')->post('/admin/events', $this->validParams([
        		'address_3' => ''
        ]));

        tap(Event::first(), function($event) use ($response, $user) {
        	$response->assertStatus(302);
        	$response->assertRedirect("/admin/events");

        	$this->assertTrue($event->user->is($user));

        	$this->assertNull($event->address_3);
        });
    }

    /** @test */
    public function date_is_required()
    {
        $user = factory(User::class)->states('admin')->create();

        $response = $this->actingAs($user)->from('/admin/events/new')->post('/admin/events', $this->validParams([
        		'date' => ''
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/admin/events/new');
        $response->assertSessionHasErrors('date');
        $this->assertEquals(0, Event::count());
    }

    /** @test */
    public function date_must_be_a_valid_date()
    {
        $user = factory(User::class)->states('admin')->create();

        $response = $this->actingAs($user)->from('/admin/events/new')->post('/admin/events', $this->validParams([
        	'date' => 'not a date'
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/admin/events/new');
        $response->assertSessionHasErrors('date');
        $this->assertEquals(0, Event::count());
    }

    /** @test */
    public function date_cannot_be_in_the_past()
    {
        $user = factory(User::class)->states('admin')->create();

        $response = $this->actingAs($user)->from('/admin/events/new')->post('/admin/events', $this->validParams([
            'date' => Carbon::yesterday()->toDateString(),
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/admin/events/new');
        $response->assertSessionHasErrors('date');
        $this->assertEquals(0, Event::count());
    }

    /** @test */
    public function time_is_required()
    {
        $user = factory(User::class)->states('admin')->create();

        $response = $this->actingAs($user)->from('/admin/events/new')->post('/admin/events', $this->validParams([
        		'time' => ''
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/admin/events/new');
        $response->assertSessionHasErrors('time');
        $this->assertEquals(0, Event::count());
    }

    /** @test */
    public function time_must_be_a_valid_time()
    {
        $user = factory(User::class)->states('admin')->create();

        $response = $this->actingAs($user)->from('/admin/events/new')->post('/admin/events', $this->validParams([
        	'time' => 'not a time'
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/admin/events/new');
        $response->assertSessionHasErrors('time');
        $this->assertEquals(0, Event::count());
    }
}