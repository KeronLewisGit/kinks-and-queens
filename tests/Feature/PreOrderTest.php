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
use Illuminate\Support\Str;
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
        $this->from('/pre-order')->post('/pre-order', $this->contact(['texture_id' => $set->texture_id, 'format' => 'bundles', 'price_set_id' => $set->id]))
            ->assertRedirect('/pre-order')->assertSessionHasErrors('form')->assertSessionHasInput('name', 'Test Queen');
        $this->assertSame(0, PreOrder::count());

        $this->get('/clip-ins')->assertSee('not taking new clip-in requests');
        $this->from('/clip-ins')->post('/clip-ins', $this->contact(['texture' => 'Body wave', 'bundles' => '3', 'fullness' => 'double']))
            ->assertSessionHasErrors('form');
        $this->assertSame(0, PreOrder::count());
    }

    public function test_a_repeated_submission_shows_the_first_request_instead_of_a_copy(): void
    {
        $set = PriceSet::first();
        $payload = $this->contact(['texture_id' => $set->texture_id, 'format' => 'bundles', 'price_set_id' => $set->id, 'submission' => (string) Str::uuid()]);

        $first = $this->post('/pre-order', $payload);
        $order = PreOrder::sole();
        $first->assertRedirect(route('preorder.sent', $order));

        $this->post('/pre-order', $payload)->assertRedirect(route('preorder.sent', $order));
        $this->assertSame(1, PreOrder::count());

        // A fresh form (new token) is a new request.
        $this->post('/pre-order', array_merge($payload, ['submission' => (string) Str::uuid()]))->assertRedirect();
        $this->assertSame(2, PreOrder::count());
    }

    public function test_a_failed_submission_can_be_corrected_and_sent_with_the_same_token(): void
    {
        $set = PriceSet::first();
        $payload = $this->contact(['texture_id' => $set->texture_id, 'format' => 'bundles', 'price_set_id' => 999999, 'submission' => (string) Str::uuid()]);
        $this->post('/pre-order', $payload)->assertSessionHasErrors('price_set_id');
        $this->post('/pre-order', array_merge($payload, ['price_set_id' => $set->id]))->assertRedirect();
        $this->assertSame(1, PreOrder::count());
    }

    public function test_ponytails_are_saved_without_a_fullness(): void
    {
        $t = Texture::where('slug', 'brazilian-kinky-curly')->first();
        $this->post('/pre-order', $this->contact(['texture_id' => $t->id, 'format' => 'ponytail', 'length' => 20, 'fullness' => 'double']))->assertRedirect();
        $this->assertArrayNotHasKey('fullness', PreOrder::sole()->details);
    }

    public function test_phone_needs_seven_digits_not_just_punctuation(): void
    {
        $set = PriceSet::first();
        $this->post('/pre-order', $this->contact(['phone' => '-------', 'texture_id' => $set->texture_id, 'format' => 'bundles', 'price_set_id' => $set->id]))
            ->assertSessionHasErrors('phone');
    }

    public function test_too_many_requests_keep_what_was_typed(): void
    {
        $set = PriceSet::first();
        $payload = $this->contact(['texture_id' => $set->texture_id, 'format' => 'bundles', 'price_set_id' => $set->id]);
        for ($i = 0; $i < 6; $i++) {
            $this->post('/pre-order', $payload)->assertRedirect();
        }
        $this->from('/pre-order')->post('/pre-order', $payload)
            ->assertRedirect('/pre-order')->assertSessionHasErrors('form')->assertSessionHasInput('phone', '868 555 0199');
        $this->assertSame(6, PreOrder::count());
    }

    public function test_an_expired_form_goes_back_with_its_input(): void
    {
        $this->app['env'] = 'production';
        $this->withMiddleware()->from('/clip-ins')->post('/clip-ins', $this->contact(['texture' => 'Body wave']))
            ->assertRedirect('/clip-ins')->assertSessionHasErrors('form')->assertSessionHasInput('texture', 'Body wave');
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
