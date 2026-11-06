<?php

namespace Tests\Feature;

use App\Mail\NewRequest;
use App\Models\PreOrder;
use App\Models\PriceSet;
use App\Models\Setting;
use App\Models\Texture;
use Database\Seeders\CatalogueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PreOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CatalogueSeeder::class);
    }

    private function contact(array $extra = []): array
    {
        return array_merge([
            'name' => 'Test Queen', 'phone' => '868 555 0199', 'instagram' => '@testqueen', 'contact_via' => 'instagram', 'consent' => '1',
        ], $extra);
    }

    public function test_bundle_pre_order_uses_the_server_price(): void
    {
        $t = Texture::where('slug', 'brazilian-kinky-curly')->first();
        $set = PriceSet::where('texture_id', $t->id)->where('lengths', '16,18,20')->first();

        $res = $this->post('/pre-order', $this->contact([
            'texture_id' => $t->id, 'format' => 'bundles', 'price_set_id' => $set->id, 'quoted_price' => 1,
        ]));

        $order = PreOrder::first();
        $res->assertRedirect(route('preorder.sent', $order));
        $this->assertSame(2400, $order->quoted_price);
        $this->assertSame('16,18,20', $order->lengths);
        $this->assertSame('testqueen', $order->instagram);
        $this->assertSame('new', $order->status);
        $this->assertCount(1, $order->events);
        $this->assertMatchesRegularExpression('/^KQ-[A-HJ-NP-Z2-9]{5}$/', $order->reference);

        $this->get(route('preorder.sent', $order))->assertOk()->assertSee($order->reference)->assertSee('2,400');
    }

    public function test_a_set_from_another_texture_is_rejected(): void
    {
        $kc = Texture::where('slug', 'brazilian-kinky-curly')->first();
        $other = PriceSet::where('texture_id', '!=', $kc->id)->first();

        $this->post('/pre-order', $this->contact(['texture_id' => $kc->id, 'format' => 'bundles', 'price_set_id' => $other->id]))
            ->assertSessionHasErrors('price_set_id');
        $this->assertSame(0, PreOrder::count());
    }

    public function test_format_must_be_offered_by_the_texture(): void
    {
        $ac = Texture::where('slug', 'mongolian-afro-coily')->first();
        $this->post('/pre-order', $this->contact(['texture_id' => $ac->id, 'format' => 'clip-ins', 'length' => 18]))
            ->assertSessionHasErrors('format');
    }

    public function test_ponytail_needs_a_length_and_is_quoted_later(): void
    {
        $ac = Texture::where('slug', 'mongolian-afro-coily')->first();
        $this->post('/pre-order', $this->contact(['texture_id' => $ac->id, 'format' => 'ponytail']))->assertSessionHasErrors('length');

        $this->post('/pre-order', $this->contact(['texture_id' => $ac->id, 'format' => 'ponytail', 'length' => 22]))->assertRedirect();
        $order = PreOrder::first();
        $this->assertNull($order->quoted_price);
        $this->assertSame('22″', $order->details['length']);
    }

    public function test_contact_validation_and_consent(): void
    {
        $t = Texture::first();
        $this->post('/pre-order', ['texture_id' => $t->id, 'format' => 'bundles'])
            ->assertSessionHasErrors(['name', 'phone', 'consent', 'contact_via']);
        $this->post('/pre-order', $this->contact(['texture_id' => $t->id, 'format' => 'bundles', 'phone' => 'call me']))
            ->assertSessionHasErrors('phone');
    }

    public function test_honeypot_silently_drops_spam(): void
    {
        $set = PriceSet::first();
        $this->post('/pre-order', $this->contact(['texture_id' => $set->texture_id, 'format' => 'bundles', 'price_set_id' => $set->id, 'website' => 'spam.example']))
            ->assertRedirect('/');
        $this->assertSame(0, PreOrder::count());
    }

    public function test_sent_page_is_private_to_the_session(): void
    {
        $order = PreOrder::create(['kind' => 'hair', 'name' => 'A', 'phone' => '8685550100', 'contact_via' => 'phone']);
        $this->get(route('preorder.sent', $order))->assertRedirect(route('track'));
    }

    public function test_owner_is_emailed_when_an_address_is_set(): void
    {
        Mail::fake();
        Setting::put('notify_email', 'owner@example.com');
        $set = PriceSet::first();
        $this->post('/pre-order', $this->contact(['texture_id' => $set->texture_id, 'format' => 'bundles', 'price_set_id' => $set->id]));
        Mail::assertSent(NewRequest::class, fn ($m) => $m->hasTo('owner@example.com'));
    }

    public function test_orders_can_be_paused(): void
    {
        Setting::put('accepting_orders', '0');
        $this->get('/pre-order')->assertSee('not taking new pre-orders');
        $set = PriceSet::first();
        $this->post('/pre-order', $this->contact(['texture_id' => $set->texture_id, 'format' => 'bundles', 'price_set_id' => $set->id]))->assertStatus(503);
    }

    public function test_whatsapp_handoff_appears_only_when_a_number_is_set(): void
    {
        $set = PriceSet::first();
        $this->post('/pre-order', $this->contact(['texture_id' => $set->texture_id, 'format' => 'bundles', 'price_set_id' => $set->id]));
        $order = PreOrder::first();
        $this->get(route('preorder.sent', $order))->assertDontSee('wa.me')->assertSee('ig.me/m/kinksandqueens');

        Setting::put('whatsapp_number', '868 555 0000');
        $this->get(route('preorder.sent', $order))->assertSee('https://wa.me/18685550000', false);
    }

    public function test_prefill_from_price_list_link(): void
    {
        $set = PriceSet::where('lengths', '20,20,20')->first();
        $this->get('/pre-order?texture='.$set->texture->slug.'&set='.$set->id)->assertOk()->assertSee('value="'.$set->id.'" selected', false);
    }

    public function test_clip_in_service_request(): void
    {
        $this->post('/clip-ins', $this->contact(['texture' => 'Brazilian Textured Straight', 'bundles' => '3', 'length' => '18″', 'fullness' => 'triple', 'own_hair' => 'Relaxed']))
            ->assertRedirect();
        $order = PreOrder::first();
        $this->assertTrue($order->isClipInService());
        $this->assertSame('Triple tracked', $order->details['fullness']);
    }
}
