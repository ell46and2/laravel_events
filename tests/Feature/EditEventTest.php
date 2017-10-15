<?php

namespace Tests\Feature;

use App\Event;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class EditEventTest extends TestCase
{
	use DatabaseMigrations;

    private function validParams($overrides = [])
    {
        return array_merge([
            'name' => 'New name',
            'address_1' => 'New address 1',
            'address_2' => 'New address 2',
            'address_3' => 'New address 3',
            'city' => 'New city',
            'date' => '2018-12-25',
            'time' => '4:30pm',
        ], $overrides);
    }

    private function oldAttributes($overrides = [])
    {
        return array_merge([
            'name' => 'Old name',
            'address_1' => 'Old address 1',
            'address_2' => 'Old address 2',
            'address_3' => 'Old address 3',
            'city' => 'Old city',
            'date' => Carbon::parse('2017-11-20 10:00am'),
        ], $overrides);
    }

    /** @test */
    public function an_admin_can_view_the_edit_form()
    {
        $admin = factory(User::class)->states('admin')->create();
        $event = factory(Event::class)->create([
        	'user_id' => $admin->id
        ]);

        $response = $this->actingAs($admin)->get("admin/events/{$event->id}/edit");

        $response->assertStatus(200);
        $this->assertTrue($response->data('event')->is($event));
    }

    /** @test */
    public function an_general_user_cannot_view_the_edit_form()
    {
        $user = factory(User::class)->states('approved')->create();
        $admin = factory(User::class)->states('admin')->create();
        $event = factory(Event::class)->create([
            'user_id' => $admin->id
        ]);

        $response = $this->actingAs($user)->get("admin/events/{$event->id}/edit");

        $response->assertStatus(403);
    }

    /** @test */
    public function guests_cannot_view_the_edit_form()
    {
        $admin = factory(User::class)->states('admin')->create();
        $event = factory(Event::class)->create([
            'user_id' => $admin->id
        ]);

        $response = $this->get("admin/events/{$event->id}/edit");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_sees_a_404_when_attempting_to_view_the_edit_form_for_an_event_that_does_not_exist()
    {
        $admin = factory(User::class)->states('admin')->create();

        $response = $this->actingAs($admin)->get("admin/events/999/edit");

        $response->assertStatus(404);
    }

    /** @test */
    public function admin_can_edit_an_event()
    {
        $admin = factory(User::class)->states('admin')->create();
        $event = factory(Event::class)->create([
            'user_id' => $admin->id,
            'name' => 'Old name',
            'address_1' => 'Old address 1',
            'address_2' => 'Old address 2',
            'address_3' => 'Old address 3',
            'city' => 'Old city',
            'date' => Carbon::parse('2017-11-20 10:00am'),
        ]);

        $response = $this->actingAs($admin)->patch("/admin/events/{$event->id}", [
            'name' => 'New name',
            'address_1' => 'New address 1',
            'address_2' => 'New address 2',
            'address_3' => 'New address 3',
            'city' => 'New city',
            'date' => '2018-12-25',
            'time' => '4:30pm',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/events');

        tap($event->fresh(), function($event) {
            $this->assertEquals('New name', $event->name);
            $this->assertEquals('New address 1', $event->address_1);
            $this->assertEquals('New address 2', $event->address_2);
            $this->assertEquals('New address 3', $event->address_3);
            $this->assertEquals('New city', $event->city);
            $this->assertEquals(Carbon::parse('2018-12-25 4:30pm'), $event->date);
        });

    }

    /** @test */
    public function general_users_cannot_edit_events()
    {
        $admin = factory(User::class)->states('admin')->create();
        $user = factory(User::class)->states('approved')->create();
        $event = factory(Event::class)->create($this->oldAttributes([
            'user_id' => $admin->id
        ]));

        $response = $this->actingAs($user)->patch("/admin/events/{$event->id}", $this->validParams());

        $response->assertStatus(403);

        $this->assertArraySubset($this->oldAttributes([
            'user_id' => $admin->id
        ]), $event->fresh()->getAttributes());
    }

    /** @test */
    public function guests_cannot_edit_events()
    {
        $admin = factory(User::class)->states('admin')->create();
        $event = factory(Event::class)->create($this->oldAttributes([
            'user_id' => $admin->id
        ]));

        $response = $this->patch("/admin/events/{$event->id}", $this->validParams());

        $response->assertStatus(403);

        $this->assertArraySubset($this->oldAttributes([
            'user_id' => $admin->id
        ]), $event->fresh()->getAttributes());
    }

    /** @test */
    public function name_is_required()
    {
        $admin = factory(User::class)->states('admin')->create();
        $event = factory(Event::class)->create($this->oldAttributes([
            'user_id' => $admin->id
        ]));

        $response = $this->actingAs($admin)->from("/admin/events/{$event->id}/edit")->patch("/admin/events/{$event->id}", $this->validParams([
            'name' => ''
        ]));

        $response->assertRedirect("/admin/events/{$event->id}/edit");
        $response->assertSessionHasErrors('name');

        $this->assertArraySubset($this->oldAttributes([
            'user_id' => $admin->id
        ]), $event->fresh()->getAttributes());
    }

    /** @test */
    public function address_1_is_required()
    {
        $admin = factory(User::class)->states('admin')->create();
        $event = factory(Event::class)->create($this->oldAttributes([
            'user_id' => $admin->id
        ]));

        $response = $this->actingAs($admin)->from("/admin/events/{$event->id}/edit")->patch("/admin/events/{$event->id}", $this->validParams([
            'address_1' => ''
        ]));

        $response->assertRedirect("/admin/events/{$event->id}/edit");
        $response->assertSessionHasErrors('address_1');

        $this->assertArraySubset($this->oldAttributes([
            'user_id' => $admin->id
        ]), $event->fresh()->getAttributes());
    }

    /** @test */
    public function address_2_is_optional()
    {
        $admin = factory(User::class)->states('admin')->create();
        $event = factory(Event::class)->create($this->oldAttributes([
            'user_id' => $admin->id
        ]));

        $response = $this->actingAs($admin)->from("/admin/events/{$event->id}/edit")->patch("/admin/events/{$event->id}", $this->validParams([
            'address_2' => ''
        ]));

        $response->assertRedirect("/admin/events/");
        tap($event->fresh(), function($event) {
            $this->assertNull($event->address_2);
        });
    }

    /** @test */
    public function address_3_is_optional()
    {
        $admin = factory(User::class)->states('admin')->create();
        $event = factory(Event::class)->create($this->oldAttributes([
            'user_id' => $admin->id
        ]));

        $response = $this->actingAs($admin)->from("/admin/events/{$event->id}/edit")->patch("/admin/events/{$event->id}", $this->validParams([
            'address_3' => ''
        ]));

        $response->assertRedirect("/admin/events/");
        tap($event->fresh(), function($event) {
            $this->assertNull($event->address_3);
        });
    }

    /** @test */
    public function city_is_required()
    {
        $admin = factory(User::class)->states('admin')->create();
        $event = factory(Event::class)->create($this->oldAttributes([
            'user_id' => $admin->id
        ]));

        $response = $this->actingAs($admin)->from("/admin/events/{$event->id}/edit")->patch("/admin/events/{$event->id}", $this->validParams([
            'city' => ''
        ]));

        $response->assertRedirect("/admin/events/{$event->id}/edit");
        $response->assertSessionHasErrors('city');

        $this->assertArraySubset($this->oldAttributes([
            'user_id' => $admin->id
        ]), $event->fresh()->getAttributes());
    }

    /** @test */
    public function date_is_required()
    {
        $admin = factory(User::class)->states('admin')->create();
        $event = factory(Event::class)->create($this->oldAttributes([
            'user_id' => $admin->id
        ]));

        $response = $this->actingAs($admin)->from("/admin/events/{$event->id}/edit")->patch("/admin/events/{$event->id}", $this->validParams([
            'date' => ''
        ]));

        $response->assertRedirect("/admin/events/{$event->id}/edit");
        $response->assertSessionHasErrors('date');

        $this->assertArraySubset($this->oldAttributes([
            'user_id' => $admin->id
        ]), $event->fresh()->getAttributes());
    }

    /** @test */
    public function date_must_be_a_valid_date()
    {
        $admin = factory(User::class)->states('admin')->create();
        $event = factory(Event::class)->create($this->oldAttributes([
            'user_id' => $admin->id,
            'date' => Carbon::parse('2018-01-01 8:00am'),
        ]));

        $response = $this->actingAs($admin)->from("/admin/events/{$event->id}/edit")->patch("/admin/events/{$event->id}", $this->validParams([
            'date' => 'not a date'
        ]));

        $response->assertRedirect("/admin/events/{$event->id}/edit");
        $response->assertSessionHasErrors('date');

        tap($event->fresh(), function($event) {
            $this->assertEquals(Carbon::parse('2018-01-01 8:00am'), $event->date);
        });
    }

    /** @test */
    public function date_cannot_be_in_the_past()
    {
        $admin = factory(User::class)->states('admin')->create();
        $event = factory(Event::class)->create($this->oldAttributes([
            'user_id' => $admin->id,
            'date' => Carbon::parse('2018-01-01 8:00am'),
        ]));

        $response = $this->actingAs($admin)->from("/admin/events/{$event->id}/edit")->patch("/admin/events/{$event->id}", $this->validParams([
            'date' => Carbon::yesterday()->toDateString(),
        ]));

        $response->assertRedirect("/admin/events/{$event->id}/edit");
        $response->assertSessionHasErrors('date');

        tap($event->fresh(), function($event) {
            $this->assertEquals(Carbon::parse('2018-01-01 8:00am'), $event->date);
        });
    }

    /** @test */
    public function time_is_required()
    {
        $admin = factory(User::class)->states('admin')->create();
        $event = factory(Event::class)->create($this->oldAttributes([
            'user_id' => $admin->id
        ]));

        $response = $this->actingAs($admin)->from("/admin/events/{$event->id}/edit")->patch("/admin/events/{$event->id}", $this->validParams([
            'time' => ''
        ]));

        $response->assertRedirect("/admin/events/{$event->id}/edit");
        $response->assertSessionHasErrors('time');

        $this->assertArraySubset($this->oldAttributes([
            'user_id' => $admin->id
        ]), $event->fresh()->getAttributes());
    }

    /** @test */
    public function time_must_be_a_valid_date()
    {
        $admin = factory(User::class)->states('admin')->create();
        $event = factory(Event::class)->create($this->oldAttributes([
            'user_id' => $admin->id,
            'date' => Carbon::parse('2018-01-01 8:00am'),
        ]));

        $response = $this->actingAs($admin)->from("/admin/events/{$event->id}/edit")->patch("/admin/events/{$event->id}", $this->validParams([
            'time' => 'not a time'
        ]));

        $response->assertRedirect("/admin/events/{$event->id}/edit");
        $response->assertSessionHasErrors('time');

        tap($event->fresh(), function($event) {
            $this->assertEquals(Carbon::parse('2018-01-01 8:00am'), $event->date);
        });
    }
}