<?php

namespace Tests\Unit;

use App\Services\GameService;
use Tests\TestCase;

class GameServiceTest extends TestCase
{
    /** @test */
    public function it_can_filter_the_tracks()
    {
        $tracks = [
            [
                'track' => [
                    'id' => 1,
                    'name' => 'Example Track',
                    'type' => 'track',
                    'artists' => [
                        'name' => 'Example Artist',
                    ],
                    'preview_url' => 'http://example.com/1',
                ],
            ],
            [
                'track' => [
                    'id' => 2,
                    'name' => 'Example Track 2',
                    'type' => 'track',
                    'artists' => [],
                    'preview_url' => 'http://example.com/2',
                ],
            ],
            [
                'track' => [
                    'id' => 3,
                    'name' => 'Example Track 3',
                    'type' => 'track',
                    'artists' => [
                        'name' => 'Example Artist 3',
                    ],
                    'preview_url' => '',
                ],
            ],
            [
                'track' => [
                    'id' => 4,
                    'name' => 'Example Track 4',
                    'type' => 'album',
                    'artists' => [
                        'name' => 'Example Artist 4',
                    ],
                    'preview_url' => 'http://example.com/4',
                ],
            ],
        ];

        $transformedTracks = (new GameService())->transformTracksForGame($tracks);

        $this->assertCount(1, $transformedTracks);
        $this->assertEquals(1, $transformedTracks->first()['id']);
    }
}
