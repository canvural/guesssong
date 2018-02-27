<?php

namespace Tests;

use App\Services\MusicService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Fakes\SpotifyFake;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp()
    {
        parent::setUp();

        $this->app->bind(MusicService::class, SpotifyFake::class);
    }

    protected function setCarbonTest(): Carbon
    {
        $now = Carbon::create(2018, 1, 1, 19, 0, 0);
        Carbon::setTestNow($now);

        return $now;
    }

    public function progressTime(int $minutes, int $seconds = 0): self
    {
        $newNow = \now()->copy()->addMinutes($minutes)->addSeconds($seconds);

        Carbon::setTestNow($newNow);

        return $this;
    }
}
