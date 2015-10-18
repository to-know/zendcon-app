<?php

namespace App\Events;

use App\Movie;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MovieReceivedVote extends Event implements ShouldBroadcast
{
    use SerializesModels;

    /**
     * The movie instance.
     *
     * @var \App\Movie
     */
    public $movie;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return ['movie' => $this->movie->load('user')];
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['movies'];
    }
}
