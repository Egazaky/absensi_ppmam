<?php

namespace Tests\Feature\Cms;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleConflictTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_schedule_new_combination_succeeds()
    {
        $user = $this->signIn('Administrator');

        $response = $this->post(route('jadwal.store'), [
            'title' => 'Kajian Rutin 1',
            'teacher' => 'Ustadz Ahmad',
            'description' => 'Pembahasan Kitab Riyadhus Shalihin',
            'session' => 'isya',
            'date' => '2026-06-01',
            'time' => '19:30',
        ]);

        $response->assertRedirect(route('jadwal.index'));
        $response->assertSessionHas('success', 'Jadwal berhasil dibuat.');

        $schedule = Schedule::where('title', 'Kajian Rutin 1')->first();
        $this->assertNotNull($schedule);
        $this->assertEquals('isya', $schedule->session);
        $this->assertEquals('2026-06-01', $schedule->date->format('Y-m-d'));
        $this->assertStringContainsString('19:30', $schedule->time);
    }

    public function test_store_schedule_duplicate_combination_fails()
    {
        $user = $this->signIn('Administrator');

        // Create initial schedule
        Schedule::create([
            'title' => 'Kajian Rutin 1',
            'teacher' => 'Ustadz Ahmad',
            'description' => 'Pembahasan Kitab Riyadhus Shalihin',
            'session' => 'isya',
            'date' => '2026-06-01',
            'time' => '19:30',
            'created_by' => $user->id,
        ]);

        // Attempt duplicate schedule
        $response = $this->post(route('jadwal.store'), [
            'title' => 'Kajian Rutin 2',
            'teacher' => 'Ustadz Budi',
            'description' => 'Pembahasan Kitab Al-Hikam',
            'session' => 'isya',
            'date' => '2026-06-01',
            'time' => '19:30',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['date']);
        
        $errors = session('errors');
        $this->assertEquals(
            'Jadwal pada tanggal, waktu, dan sesi tersebut sudah tersedia atau bentrok.',
            $errors->first('date')
        );
    }

    public function test_edit_unregistered_schedule_id_returns_404()
    {
        $this->signIn('Administrator');

        // Random non-existent UUID
        $fakeUuid = 'fa201ed9-6016-4d90-b3aa-f4858c5260f9';

        $response = $this->get(route('jadwal.edit', $fakeUuid));
        $response->assertStatus(404);

        $responseUpdate = $this->put(route('jadwal.update', $fakeUuid), [
            'title' => 'Kajian Rutin 2',
            'teacher' => 'Ustadz Budi',
            'session' => 'isya',
            'date' => '2026-06-01',
            'time' => '19:30',
        ]);
        $responseUpdate->assertStatus(404);
    }

    public function test_update_same_schedule_without_changes_succeeds()
    {
        $user = $this->signIn('Administrator');

        $schedule = Schedule::create([
            'title' => 'Kajian Rutin 1',
            'teacher' => 'Ustadz Ahmad',
            'description' => 'Pembahasan Kitab Riyadhus Shalihin',
            'session' => 'isya',
            'date' => '2026-06-01',
            'time' => '19:30',
            'created_by' => $user->id,
        ]);

        // Put request without changing date, time, and session
        $response = $this->put(route('jadwal.update', $schedule->id), [
            'title' => 'Kajian Rutin Terupdate',
            'teacher' => 'Ustadz Ahmad Terupdate',
            'description' => 'Pembahasan Kitab Riyadhus Shalihin',
            'session' => 'isya',
            'date' => '2026-06-01',
            'time' => '19:30',
        ]);

        $response->assertRedirect(route('jadwal.index'));
        $response->assertSessionHas('success', 'Jadwal berhasil diperbarui.');

        $schedule->refresh();
        $this->assertEquals('Kajian Rutin Terupdate', $schedule->title);
        $this->assertEquals('2026-06-01', $schedule->date->format('Y-m-d'));
        $this->assertStringContainsString('19:30', $schedule->time);
    }

    public function test_update_schedule_conflicting_with_another_schedule_fails()
    {
        $user = $this->signIn('Administrator');

        // Create schedule 1
        $schedule1 = Schedule::create([
            'title' => 'Kajian Rutin 1',
            'teacher' => 'Ustadz Ahmad',
            'session' => 'isya',
            'date' => '2026-06-01',
            'time' => '19:30',
            'created_by' => $user->id,
        ]);

        // Create schedule 2
        $schedule2 = Schedule::create([
            'title' => 'Kajian Rutin 2',
            'teacher' => 'Ustadz Budi',
            'session' => 'subuh',
            'date' => '2026-06-02',
            'time' => '05:00',
            'created_by' => $user->id,
        ]);

        // Try to update schedule 2 to match schedule 1
        $response = $this->put(route('jadwal.update', $schedule2->id), [
            'title' => 'Kajian Rutin 2',
            'teacher' => 'Ustadz Budi',
            'session' => 'isya',
            'date' => '2026-06-01',
            'time' => '19:30',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['date']);
        
        $errors = session('errors');
        $this->assertEquals(
            'Jadwal pada tanggal, waktu, dan sesi tersebut sudah tersedia atau bentrok.',
            $errors->first('date')
        );
    }
}
