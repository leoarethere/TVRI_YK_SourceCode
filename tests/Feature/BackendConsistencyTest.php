<?php

namespace Tests\Feature;

use App\Models\Broadcast;
use App\Models\BroadcastCategory;
use App\Models\InfoKunjunganFaq;
use App\Models\InfoMagangFaq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackendConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_actions_use_standard_flash_message_keys(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('dashboard.kategori-postingan.store'), [
            'name' => 'Kategori Baru',
            'slug' => 'kategori-baru',
            'color' => 'blue',
        ])->assertSessionHas('success')
            ->assertSessionMissing('category_success');

        $category = BroadcastCategory::factory()->create();
        Broadcast::factory()->create([
            'user_id' => $admin->id,
            'broadcast_category_id' => $category->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('dashboard.penyiaran-kategori.destroy', $category))
            ->assertSessionHas('error')
            ->assertSessionMissing('category_error');
    }

    public function test_faq_resource_routes_use_implicit_model_binding(): void
    {
        $admin = User::factory()->admin()->create();
        $magangFaq = InfoMagangFaq::create([
            'question' => 'Pertanyaan magang?',
            'answer' => 'Jawaban magang.',
            'order' => 1,
            'is_active' => true,
        ]);
        $kunjunganFaq = InfoKunjunganFaq::create([
            'question' => 'Pertanyaan kunjungan?',
            'answer' => 'Jawaban kunjungan.',
            'order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard.info-magang-faq.edit', $magangFaq))
            ->assertOk()
            ->assertViewHas('faq', fn (InfoMagangFaq $faq): bool => $faq->is($magangFaq));

        $this->actingAs($admin)
            ->get(route('dashboard.info-kunjungan-faq.edit', $kunjunganFaq))
            ->assertOk()
            ->assertViewHas('faq', fn (InfoKunjunganFaq $faq): bool => $faq->is($kunjunganFaq));
    }
}
