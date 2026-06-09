<?php

namespace Tests\Feature\Cms;

use App\Models\Attendance;
use App\Models\Santri;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_access_the_home_page()
    {
        $response = $this->get(route('home'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_dashboard()
    {
        $this->signIn();
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertViewIs('home');
        $response->assertViewHasAll([
            'santri',
            'users',
            'schedules',
            'todayAttendances',
        ]);
    }

    public function test_homepage_shows_correct_data_information()
    {
        // every time a new factory user is created
        // one new santri will be created
        $user = User::factory()->create();
        Schedule::create([
            'title' => 'Kajian Subuh',
            'teacher' => 'Ustadz',
            'description' => 'Kajian rutin',
            'session' => 'subuh',
            'date' => date('Y-m-d'),
            'time' => '05:00',
            'created_by' => $user->id,
        ]);
        Attendance::create([
            'date' => date('Y-m-d'),
            'santri_id' => $user->santri_id,
            'session' => 'Subuh',
            'status' => true,
        ]);

        $this->signIn('Administrator', $user);

        $response = $this->get(route('home'));
        $response->assertStatus(200)
            ->assertViewHas('santri', 1)
            ->assertViewHas('users', 1)
            ->assertViewHas('schedules', 1)
            ->assertViewHas('todayAttendances', 1);
    }
}
