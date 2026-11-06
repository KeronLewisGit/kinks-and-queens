<?php

namespace Tests\Feature;

use App\Models\Texture;
use Database\Seeders\CatalogueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CatalogueSeeder::class);
    }

    public function test_every_public_page_renders(): void
    {
        foreach (['/', '/textures', '/texture-match', '/clip-ins', '/queen-cam', '/care', '/faq', '/our-story', '/privacy', '/pre-order', '/track', '/sitemap.xml'] as $url) {
            $this->get($url)->assertOk();
        }
        foreach (Texture::all() as $t) {
            $this->get('/textures/'.$t->slug)->assertOk()->assertSee($t->name);
        }
    }

    public function test_price_lists_match_the_published_flyers(): void
    {
        $kc = Texture::where('slug', 'brazilian-kinky-curly')->first();
        $this->assertSame(22, $kc->priceSets()->count());
        $this->assertSame(1910, $kc->fromPrice());
        $this->assertSame(4745, $kc->priceSets()->where('lengths', '28,28,28,28')->value('price'));

        $ac = Texture::where('slug', 'mongolian-afro-coily')->first();
        $this->assertSame(15, $ac->priceSets()->count());
        $this->assertSame(1680, $ac->fromPrice());

        $ts = Texture::where('slug', 'brazilian-textured-straight')->first();
        $this->assertSame(19, $ts->priceSets()->count());
        $this->assertSame(2672, $ts->priceSets()->where('lengths', '22,22')->value('price'));

        $this->get('/textures/brazilian-kinky-curly')->assertSee('4,745')->assertSee('98–100 g');
    }

    public function test_home_shows_textures_prices_and_real_photos(): void
    {
        $this->get('/')
            ->assertSee('Where texture reigns', false)
            ->assertSee('Brazilian Kinky Curly')
            ->assertSee('1,680')
            ->assertSee('assets/img/kinky-curly-portrait.webp')
            ->assertSee('14 business days');
    }

    public function test_hidden_texture_is_not_public(): void
    {
        Texture::where('slug', 'mongolian-afro-coily')->update(['is_active' => false]);
        $this->get('/textures/mongolian-afro-coily')->assertNotFound();
        $this->get('/')->assertDontSee('/textures/mongolian-afro-coily');
    }

    public function test_pages_have_one_h1_and_the_shared_inner_hero(): void
    {
        foreach (['/textures', '/care', '/faq', '/queen-cam', '/clip-ins', '/our-story', '/texture-match', '/pre-order', '/track'] as $url) {
            $html = $this->get($url)->getContent();
            $this->assertSame(1, substr_count($html, '<h1'), "$url should have one h1");
            $this->assertStringContainsString('aria-label="Breadcrumb"', $html, "$url should use the shared page hero");
        }
    }

    public function test_footer_social_links_render_handles_not_template_code(): void
    {
        $this->get('/')
            ->assertSee('>@kinksandqueens</a>', false)
            ->assertSee('href="https://www.tiktok.com/@kinksandqueens"', false)
            ->assertDontSee('Setting::get', false);
    }

    public function test_security_headers_and_noindex_on_private_pages(): void
    {
        $this->get('/')->assertHeader('X-Content-Type-Options', 'nosniff')->assertHeaderMissing('X-Robots-Tag');
        $this->get('/admin/login')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }
}
