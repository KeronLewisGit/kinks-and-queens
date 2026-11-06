<?php

namespace App\Http\Controllers;

use App\Models\PreOrder;
use App\Models\PriceSet;
use App\Models\Setting;
use App\Models\Texture;
use App\Support\RequestNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PreOrderController extends Controller
{
    public const LENGTHS = [12, 14, 16, 18, 20, 22, 24, 26, 28];

    public function create(Request $request): View
    {
        $textures = Texture::active()->with('activePriceSets')->get();
        $texture = $textures->firstWhere('slug', $request->query('texture'));
        $format = in_array($request->query('format'), array_keys(PreOrder::FORMATS), true) ? $request->query('format') : null;
        $set = $texture?->activePriceSets->firstWhere('id', (int) $request->query('set'));

        return view('preorder.create', [
            'textures' => $textures,
            'catalogue' => TextureController::catalogue($textures),
            'prefill' => [
                'texture' => old('texture_id', $texture?->id),
                'format' => old('format', $format ?? ($texture ? 'bundles' : null)),
                'set' => old('price_set_id', $set?->id),
                'length' => old('length'),
            ],
            'lengths' => self::LENGTHS,
            'accepting' => Setting::get('accepting_orders') === '1',
        ]);
    }

    public function store(Request $request, RequestNotifier $notifier): RedirectResponse
    {
        if ($request->filled('website')) {
            // Honeypot: pretend it worked.
            return redirect()->route('home');
        }
        abort_if(Setting::get('accepting_orders') !== '1', 503, 'We are not taking new pre-orders right now.');

        $data = $request->validate([
            'texture_id' => ['required', Rule::exists('textures', 'id')->where('is_active', true)],
            'format' => ['required', Rule::in(array_keys(PreOrder::FORMATS))],
            'price_set_id' => ['nullable', 'integer'],
            'length' => ['nullable', 'integer', Rule::in(self::LENGTHS)],
            'fullness' => ['nullable', Rule::in(['double', 'triple'])],
            ...self::contactRules(),
        ], self::messages());

        $texture = Texture::findOrFail($data['texture_id']);
        if (! $texture->offers($data['format'])) {
            throw ValidationException::withMessages(['format' => "{$texture->name} is not available as ".strtolower(PreOrder::FORMATS[$data['format']]).'.']);
        }

        $order = new PreOrder([
            'kind' => 'hair',
            'texture_id' => $texture->id,
            'format' => $data['format'],
            ...self::contactFields($data),
        ]);

        if ($data['format'] === 'bundles') {
            // The price always comes from our own list, never from the browser.
            $set = PriceSet::where('texture_id', $texture->id)->where('is_active', true)->find($data['price_set_id'] ?? 0);
            if (! $set) {
                throw ValidationException::withMessages(['price_set_id' => 'Choose a bundle set from the price list.']);
            }
            $order->fill(['price_set_id' => $set->id, 'lengths' => $set->lengths, 'bundle_count' => $set->bundle_count, 'quoted_price' => $set->price]);
        } else {
            if (empty($data['length'])) {
                throw ValidationException::withMessages(['length' => 'Choose the length you would like.']);
            }
            $order->lengths = (string) $data['length'];
            $order->details = array_filter([
                'length' => $data['length'].'″',
                'fullness' => isset($data['fullness']) ? ucfirst($data['fullness']).' tracked' : null,
            ]);
        }

        $order->save();
        $order->events()->create(['status' => 'new']);
        $notifier->send($order);

        $request->session()->put('kq.orders.'.$order->reference, true);

        return redirect()->route('preorder.sent', $order);
    }

    public function sent(Request $request, PreOrder $preOrder): View|RedirectResponse
    {
        if (! $request->session()->get('kq.orders.'.$preOrder->reference)) {
            return redirect()->route('track')->with('notice', 'Enter your reference and phone number to see that request.');
        }

        return view('preorder.sent', [
            'order' => $preOrder->load('texture'),
            'whatsapp' => Setting::whatsappLink($preOrder->handoffMessage()),
            'instagram' => Setting::instagramDm(),
        ]);
    }

    /** Contact fields shared by the pre-order and clip-in forms. */
    public static function contactRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9 +().-]{7,}$/'],
            'instagram' => ['nullable', 'string', 'max:60', 'regex:/^@?[A-Za-z0-9._]{1,30}$/'],
            'email' => ['nullable', 'email:rfc', 'max:120'],
            'area' => ['nullable', 'string', 'max:80'],
            'contact_via' => ['required', Rule::in(['instagram', 'whatsapp', 'phone'])],
            'notes' => ['nullable', 'string', 'max:1000'],
            'consent' => ['accepted'],
        ];
    }

    public static function contactFields(array $data): array
    {
        return [
            'name' => trim($data['name']),
            'phone' => trim($data['phone']),
            'instagram' => isset($data['instagram']) ? ltrim(trim($data['instagram']), '@') : null,
            'email' => $data['email'] ?? null,
            'area' => $data['area'] ?? null,
            'contact_via' => $data['contact_via'],
            'notes' => $data['notes'] ?? null,
        ];
    }

    public static function messages(): array
    {
        return [
            'texture_id.required' => 'Choose a texture.',
            'format.required' => 'Choose bundles, a ponytail or clip-ins.',
            'phone.regex' => 'Enter a phone or WhatsApp number we can reach you on.',
            'instagram.regex' => 'Enter your Instagram username, for example @yourname.',
            'consent.accepted' => 'Please confirm we may contact you about this request.',
        ];
    }
}
