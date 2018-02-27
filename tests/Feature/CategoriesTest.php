<?php

namespace Tests\Feature;

use Tests\TestCase;

class CategoriesTest extends TestCase
{
    /** @test */
    function it_will_list_all_the_categories()
    {
        $categories = collect(\get_fake_data('categories.json'));

        $response = $this->get(\route('categories.index'));

        $categories->each(function ($category) use ($response) {
            $response->assertSee(\htmlspecialchars($category['name']));
        });
    }
}
