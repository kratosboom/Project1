<?php

namespace Tests\Feature\Livescore;

use App\Models\Fixture;
use App\Models\User;
use Database\Seeders\LivescoreDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['livescore.leagues' => [39, 140, 135, 78, 61, 274]]);
        $this->seed(LivescoreDemoSeeder::class);
    }

    private function assertSeoBasics(string $html): void
    {
        $this->assertSame(1, substr_count($html, '<h1'), 'tepat satu <h1>');
        $this->assertStringContainsString('<link rel="canonical"', $html);
        $this->assertStringContainsString('name="description"', $html);
        $this->assertStringNotContainsString('&amp;amp;', $html);

        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
        $this->assertNotEmpty($m[1]);
        foreach ($m[1] as $json) {
            $this->assertNotNull(json_decode(trim($json)), 'JSON-LD valid');
        }
    }

    public function test_home_shows_live_and_today_matches(): void
    {
        $res = $this->get('/')->assertOk()
            ->assertSee('Skor Langsung')
            ->assertSee('Sedang berlangsung')
            ->assertSee('Arsenal')
            ->assertSee('Liga Primer Inggris');

        $this->assertSeoBasics($res->getContent());
    }

    public function test_date_pages(): void
    {
        $tz = 'Asia/Jakarta';
        $this->get('/jadwal/'.now($tz)->subDay()->toDateString())->assertOk()->assertSee('Paris Saint Germain');
        $this->get('/jadwal/'.now($tz)->addDay()->toDateString())->assertOk()->assertSee('Bayer Leverkusen');
        $this->get('/jadwal/'.now($tz)->toDateString())->assertRedirect('/');
        $this->get('/jadwal/2026-13-45')->assertNotFound();
    }

    public function test_league_page_with_standings(): void
    {
        $res = $this->get('/liga/liga-primer-inggris')->assertOk()->assertSee('Klasemen')->assertSee('Manchester City');
        $this->assertSeoBasics($res->getContent());
        $this->get('/liga/tidak-ada')->assertNotFound();
    }

    public function test_match_pages(): void
    {
        $live = Fixture::firstWhere('api_id', 1000001);
        $res = $this->get('/pertandingan/'.$live->slug)->assertOk()->assertSee('LIVE')->assertSee('Momen penting')->assertSee('Susunan pemain');
        $this->assertSeoBasics($res->getContent());
        $this->assertStringContainsString('"@type":"SportsEvent"', $res->getContent());

        $upcoming = Fixture::firstWhere('api_id', 1000002);
        $this->get('/pertandingan/'.$upcoming->slug)->assertOk()->assertSee('Pertandingan belum dimulai');
    }

    public function test_sitemap_and_robots(): void
    {
        $xml = simplexml_load_string($this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8')->getContent());
        $this->assertGreaterThan(12, count($xml->url));

        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap: '.route('sitemap'));
    }

    public function test_admin_dashboard_shows_livescore_card(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertOk()->assertSee('Livescore');
    }
}
