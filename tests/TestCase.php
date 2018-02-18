<?php

namespace Tests;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Cache;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
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
    
    public function withPlaylistCache(array $playlist): self
    {
        Cache::shouldReceive('get')
            ->once()
            ->with('playlist_'.\str_slug($playlist['name']))
            ->andReturn($playlist);
        
        return $this;
    }
    
    public function withPlaylistTracksCache(array $playlist, array $tracks): self
    {
        Cache::shouldReceive('get')
            ->once()
            ->with($playlist['id'].'_tracks')
            ->andReturn($tracks);
        
        return $this;
    }
    
    public function withPlaylistCacheExistence(array $playlist): self
    {
        Cache::shouldReceive('has')
            ->once()
            ->with('playlist_'.\str_slug($playlist['name']))
            ->andReturnTrue();
        
        return $this;
    }
    
    public function withPlaylistTracksCacheRemember($tracks): self
    {
        Cache::shouldReceive('remember')
            ->once()
            ->withAnyArgs()
            ->andReturn($tracks);
        
        return $this;
    }
}
