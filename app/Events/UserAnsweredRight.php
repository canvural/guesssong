<?php

namespace App\Events;

use App\User;
use Illuminate\Foundation\Events\Dispatchable;

class UserAnsweredRight
{
    use Dispatchable;

    /**
     * @var User
     */
    public $user;

    /**
     * @var array
     */
    public $playlist;

    /**
     * Create a new event instance.
     *
     * @param User  $user
     * @param array $playlist
     */
    public function __construct(User $user, array $playlist)
    {
        $this->user = $user;
        $this->playlist = $playlist;
    }
}
