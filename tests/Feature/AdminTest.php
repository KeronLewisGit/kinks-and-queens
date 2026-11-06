<?php

namespace Tests\Feature;

use App\Models\GalleryPhoto;
use App\Models\PreOrder;
use App\Models\PriceSet;
use App\Models\Setting;
use App\Models\Texture;
use App\Models\User;
use Database\Seeders\CatalogueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CatalogueSeeder::class);
        $this->owner = User::create(['name' => 'Owner', 'email' => 'o@kq.test', 'password' => 'secret-password', 'role' => 'owner']);
        $this->staff = User::create(['name' => 'Staff', 'email' => 's@kq.test', 'password' => 'secret-password', 'role' => 'staff']);
    }

    public function test_admin_requires_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->post('/admin/login', ['email' => 'o@kq.test', 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/admin/login', ['email' => 'o@kq.test', 'password' => 'secret-password'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($this->owner);
    }

    public function test_dashboard_leads_with_requests_to_confirm(): void
    {
        PreOrder::create(['kind' => 'hair', 'name' => 'Waiting Queen', 'phone' => '8685550100', 'contact_via' => 'phone']);
        $this->actingAs($this->staff)->get('/admin')->assertOk()->assertSee('To confirm')->assertSee('Waiting Queen');
    }

    public function test_every_admin_page_renders_for_the_owner(): void
    {
        $o = PreOrder::create(['kind' => 'hair', 'name' => 'A', 'phone' => '8685550100', 'contact_via' => 'phone']);
        $t = Texture::first();
        foreach (['/admin', '/admin/requests', '/admin/requests/'.$o->reference, '/admin/prices', '/admin/prices/'.$t->slug, '/admin/photos', '/admin/settings', '/admin/team'] as $url) {
            $this->actingAs($this->owner)->get($url)->assertOk();
        }
    }

    public function test_staff_cannot_reach_owner_pages(): void
    {
        $this->actingAs($this->staff)->get('/admin/settings')->assertForbidden();
        $this->actingAs($this->staff)->get('/admin/team')->assertForbidden();
        $this->actingAs($this->staff)->delete('/admin/photos/'.GalleryPhoto::first()->id)->assertForbidden();
    }

    public function test_moving_a_request_records_history(): void
    {
        $o = PreOrder::create(['kind' => 'hair', 'name' => 'A', 'phone' => '8685550100', 'contact_via' => 'phone']);
        $this->actingAs($this->staff)->post("/admin/requests/{$o->reference}/status", ['status' => 'confirmed', 'note' => 'Confirmed by DM'])->assertRedirect();
        $o->refresh();
        $this->assertSame('confirmed', $o->status);
        $this->assertSame('Confirmed by DM', $o->events->last()->note);
        $this->assertSame($this->staff->id, $o->events->last()->user_id);
    }

    public function test_price_changes_reach_the_website(): void
    {
        $t = Texture::where('slug', 'brazilian-kinky-curly')->first();
        $set = PriceSet::where('texture_id', $t->id)->where('lengths', '12,12,12')->first();
        $payload = [
            'best_for' => $t->best_for, 'tagline' => $t->tagline, 'description' => $t->description, 'is_active' => '1',
            'formats' => ['bundles', 'ponytail'],
            'sets' => [$set->id => ['price' => 1999, 'is_active' => '1']],
        ];
        $this->actingAs($this->staff)->put('/admin/prices/'.$t->slug, $payload)->assertRedirect();
        $this->assertSame(1999, $set->fresh()->price);
        $this->get('/textures/'.$t->slug)->assertSee('1,999');

        $this->actingAs($this->staff)->post('/admin/prices/'.$t->slug.'/sets', ['lengths' => '30, 30, 30', 'price' => 4200])->assertRedirect();
        $this->assertDatabaseHas('price_sets', ['texture_id' => $t->id, 'lengths' => '30,30,30', 'bundle_count' => 3]);
        $this->actingAs($this->staff)->post('/admin/prices/'.$t->slug.'/sets', ['lengths' => '30,30,30', 'price' => 1])->assertSessionHasErrors('lengths');
    }

    public function test_photo_upload_and_hide(): void
    {
        Storage::fake('public');
        $this->actingAs($this->staff)->post('/admin/photos', [
            'photo' => UploadedFile::fake()->image('look.jpg', 800, 1000), 'alt' => 'Ponytail side view', 'label' => 'Queen Cam',
        ])->assertRedirect();
        $photo = GalleryPhoto::where('alt', 'Ponytail side view')->first();
        Storage::disk('public')->assertExists($photo->path);
        $this->assertSame($photo->id, GalleryPhoto::orderBy('sort')->first()->id, 'new uploads lead the gallery');

        $this->actingAs($this->staff)->patch('/admin/photos/'.$photo->id, ['alt' => 'Ponytail', 'sort' => 0])->assertRedirect();
        $this->assertFalse($photo->fresh()->is_visible);
        $this->get('/queen-cam')->assertDontSee($photo->path);
    }

    public function test_owner_settings_change_the_site(): void
    {
        $this->actingAs($this->owner)->put('/admin/settings', [
            'instagram_handle' => 'kinksandqueens', 'whatsapp_number' => '1 868 555 0000', 'announcement' => 'New lengths in', 'accepting_orders' => '1',
        ])->assertRedirect();
        $this->assertSame('1 868 555 0000', Setting::get('whatsapp_number'));
        $this->get('/')->assertSee('New lengths in')->assertSee('wa.me/18685550000', false);
    }

    public function test_team_keeps_one_owner_and_create_admin_command(): void
    {
        $this->actingAs($this->owner)->delete('/admin/team/'.$this->owner->id)->assertSessionHasErrors('team');
        $this->artisan('app:create-admin', ['--email' => 'boss@kq.test', '--password' => 'long-enough-1'])->assertSuccessful();
        $this->assertTrue(User::where('email', 'boss@kq.test')->first()->isOwner());
        $this->artisan('app:create-admin', ['--email' => 'x@kq.test', '--password' => 'short'])->assertFailed();
    }
}
