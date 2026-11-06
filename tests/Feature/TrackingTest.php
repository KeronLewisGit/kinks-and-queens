<?php

namespace Tests\Feature;

use App\Models\PreOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    private function order(): PreOrder
    {
        $o = PreOrder::create(['kind' => 'hair', 'name' => 'Track Me', 'phone' => '1 (868) 555-0123', 'contact_via' => 'whatsapp', 'lengths' => '18,18,18', 'quoted_price' => 2425]);
        $o->events()->create(['status' => 'new']);

        return $o;
    }

    public function test_lookup_needs_reference_and_last_four_digits(): void
    {
        $o = $this->order();
        $this->post('/track', ['reference' => $o->reference, 'phone' => '9999'])->assertSessionHasErrors('reference');
        $this->post('/track', ['reference' => strtolower(substr($o->reference, 3)), 'phone' => '0123'])->assertRedirect(route('track.show', $o));
        $this->get(route('track.show', $o))->assertOk()->assertSee('Request received')->assertSee('2,425');
    }

    public function test_tracking_page_is_not_open_to_guessing(): void
    {
        $o = $this->order();
        $this->get(route('track.show', $o))->assertRedirect(route('track'));
    }

    public function test_customer_sees_status_notes_and_expected_date(): void
    {
        $o = $this->order();
        $o->moveTo('confirmed');
        $o->moveTo('paid');
        $o->moveTo('ordered', 'Shipped today');
        $this->assertNotNull($o->fresh()->expected_at);

        $this->post('/track', ['reference' => $o->reference, 'phone' => '0123']);
        $this->get(route('track.show', $o))->assertSee('On its way to us')->assertSee('Shipped today')->assertSee('Expected by');
    }
}
