<?php

namespace Database\Seeders;

use App\Models\PreOrder;
use App\Models\PriceSet;
use App\Models\Texture;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'owner@kinksandqueens.test'], ['name' => 'Owner (demo)', 'password' => 'password123', 'role' => 'owner']);
        User::firstOrCreate(['email' => 'staff@kinksandqueens.test'], ['name' => 'Staff (demo)', 'password' => 'password123', 'role' => 'staff']);

        if (PreOrder::count() > 0) {
            return;
        }

        $kc = Texture::where('slug', 'brazilian-kinky-curly')->first();
        $ac = Texture::where('slug', 'mongolian-afro-coily')->first();
        $ts = Texture::where('slug', 'brazilian-textured-straight')->first();

        $samples = [
            [$kc, '16,18,20', 'new', 'Sample Client A', '1 868 555 0101', 0],
            [$ac, '20,20,20', 'confirmed', 'Sample Client B', '1 868 555 0102', 1],
            [$ts, '18,18,18', 'ordered', 'Sample Client C', '1 868 555 0103', 4],
            [$kc, '22,22,22,22', 'ready', 'Sample Client D', '1 868 555 0104', 12],
            [$ac, '14,16,18', 'completed', 'Sample Client E', '1 868 555 0105', 25],
        ];
        $flow = ['new', 'confirmed', 'paid', 'ordered', 'ready', 'completed'];

        foreach ($samples as [$texture, $lengths, $status, $name, $phone, $daysAgo]) {
            $set = PriceSet::where('texture_id', $texture->id)->where('lengths', $lengths)->first();
            $order = PreOrder::create([
                'kind' => 'hair', 'texture_id' => $texture->id, 'format' => 'bundles', 'price_set_id' => $set?->id,
                'lengths' => $lengths, 'bundle_count' => count(explode(',', $lengths)), 'quoted_price' => $set?->price,
                'status' => 'new', 'name' => $name, 'phone' => $phone, 'instagram' => 'sample.client', 'area' => 'San Fernando',
                'contact_via' => 'instagram', 'notes' => 'Demo request created by the local seeder.',
            ]);
            $order->forceFill(['created_at' => now()->subDays($daysAgo)])->save();
            $order->events()->create(['status' => 'new', 'created_at' => now()->subDays($daysAgo)]);
            foreach (array_slice($flow, 1, array_search($status, $flow, true)) as $step) {
                $order->moveTo($step);
            }
        }

        PreOrder::create([
            'kind' => 'clip_in_service', 'format' => 'clip-ins', 'status' => 'new', 'name' => 'Sample Client F', 'phone' => '1 868 555 0106',
            'instagram' => 'sample.clipins', 'contact_via' => 'whatsapp',
            'details' => ['texture' => 'Brazilian Textured Straight', 'bundles' => '3', 'length' => '18″', 'fullness' => 'Double tracked', 'own_hair' => 'Relaxed'],
            'notes' => 'Demo clip-in request.',
        ])->events()->create(['status' => 'new']);
    }
}
