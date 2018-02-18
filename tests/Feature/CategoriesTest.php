<?php

namespace Tests\Feature;

use App\Services\MusicService;
use Tests\Fakes\SpotifyFake;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->app->bind(MusicService::class, SpotifyFake::class);
    }

    /** @test */
    public function it_will_list_all_the_categories()
    {
        $categories = collect(\get_fake_data('categories.json'));

        $response = $this->get(\route('categories.index'));

        $categories->each(function ($category) use ($response) {
            $response->assertSee(\htmlspecialchars($category['name']));
        });
    }
}
