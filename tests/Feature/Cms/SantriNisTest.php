<?php

namespace Tests\Feature\Cms;

use App\Models\Santri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SantriNisTest extends TestCase
{
    use RefreshDatabase;

    public function test_automatic_nis_generation_on_creation()
    {
        $santri1 = Santri::factory()->create([
            'entry_year' => '2026',
        ]);

        $this->assertEquals('20260001', $santri1->nis);

        $santri2 = Santri::factory()->create([
            'entry_year' => '2026',
        ]);

        $this->assertEquals('20260002', $santri2->nis);

        // Different entry year starts a new sequence
        $santri3 = Santri::factory()->create([
            'entry_year' => '2027',
        ]);

        $this->assertEquals('20270001', $santri3->nis);
    }

    public function test_nis_is_displayed_in_index_show_and_edit_pages()
    {
        $this->signIn(User::ROLE_ADMINISTRATOR);

        $santri = Santri::factory()->create([
            'entry_year' => '2026',
        ]);

        // Verify index page displays NIS
        $response = $this->get(route('santri.index'));
        $response->assertStatus(200);
        $response->assertSee('20260001');

        // Verify show page displays NIS
        $response = $this->get(route('santri.show', $santri->id));
        $response->assertStatus(200);
        $response->assertSee('20260001');

        // Verify edit page displays NIS
        $response = $this->get(route('santri.edit', $santri->id));
        $response->assertStatus(200);
        $response->assertSee('20260001');
    }

    public function test_nis_is_returned_in_api_profile_response()
    {
        $santri = Santri::factory()->create([
            'entry_year' => '2026',
        ]);

        $user = User::factory()->create([
            'role' => 'Santri',
            'santri_id' => $santri->id,
        ]);

        $token = auth('api')->login($user);

        // Verify profile response has NIS
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/v1/profile');

        $response->assertStatus(200);
        $response->assertJsonPath('data.nis', '20260001');
    }
}
