<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\WhatsAppModule;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * وحدة الواتساب مطفأة (WHATSAPP_ENABLED=false، الافتراضي في أفق): مساراتها 404 حتى للمدير،
 * والعلم المشترك false فتُخفي الواجهة قوائمها، ومهامها المجدولة لا تعمل. phpunit.xml يشغّلها لبقية الاختبارات.
 */
class WhatsAppModuleDisabledTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['whatsapp.enabled' => false]);
    }

    public function test_module_routes_are_not_found_even_for_admin(): void
    {
        $admin = User::factory()->admin()->create();

        foreach (['/whatsapp/inbox', '/whatsapp/settings', '/api/whatsapp/conversations', '/quote-requests', '/api/whatsapp/webhook'] as $path) {
            $this->actingAs($admin)->get($path)->assertNotFound();
        }
        $this->postJson('/api/whatsapp/webhook', [])->assertNotFound();
    }

    public function test_shared_feature_flag_is_off_and_other_pages_still_work(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/invoices')->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('features.whatsapp', false)->where('invoiceWhatsapp', false));
        $this->actingAs($admin)->get('/offers')->assertOk();
    }

    public function test_roles_screen_hides_whatsapp_permission_groups(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/roles')->assertOk()
            ->assertInertia(fn (Assert $page) => $page->missing('permissions.whatsapp')->missing('permissions.quotes'));
    }

    public function test_scheduled_whatsapp_commands_are_skipped(): void
    {
        $events = collect(app(Schedule::class)->events())->filter(fn ($e) => WhatsAppModule::ownsCommand($e->command));

        $this->assertNotEmpty($events);
        $this->assertTrue($events->every(fn ($e) => ! $e->filtersPass($this->app)));

        config(['whatsapp.enabled' => true]);
        $this->assertTrue($events->contains(fn ($e) => $e->filtersPass($this->app)));
    }

    public function test_enabled_module_keeps_routes(): void
    {
        config(['whatsapp.enabled' => true]);

        $this->actingAs(User::factory()->admin()->create())->get('/whatsapp/inbox')->assertOk();
    }
}
