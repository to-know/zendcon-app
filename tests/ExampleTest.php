<?php

use App\User;
use App\Movie;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    public function test_movie_can_be_created()
    {
        $this->be($user = factory(User::class)->create());

        $this->expectsEvents('App\Events\MovieCreated');

        $this->json('POST', '/movies', [
            'movie' => 'Star Wars',
        ]);

        $this->assertResponseOk();
        $this->seeInDatabase('movies', ['name' => 'Star Wars', 'user_id' => $user->id]);
    }


    public function test_vote_increases_vote_count_by_one()
    {
        $this->be(factory(User::class)->create());

        $movie = factory(Movie::class)->create(
            ['name' => 'Star Wars', 'votes' => 1]
        );

        $this->expectsEvents('App\Events\MovieReceivedVote');

        $this->json('PUT', '/movies/'.$movie->id.'/vote');

        $this->assertResponseOk();
        $this->seeInDatabase('movies', ['name' => 'Star Wars', 'votes' => 2]);
    }


    public function test_destroy_removes_movies()
    {
        $this->be($user = factory(User::class)->create());

        $user->movies()->save($movie = factory(Movie::class)->make([
            'name' => 'Star Wars', 'votes' => 1
        ]));

        $this->expectsEvents('App\Events\MovieDeleted');

        $this->seeInDatabase('movies', ['name' => 'Star Wars']);

        $this->json('DELETE', '/movies/'.$movie->id);

        $this->assertResponseOk();
        $this->notSeeInDatabase('movies', ['name' => 'Star Wars']);
    }


    public function test_movies_cant_be_destroyed_by_non_owner()
    {
        $this->be($user = factory(User::class)->create());

        $otherUser = factory(User::class)->create();

        $otherUser->movies()->save($movie = factory(Movie::class)->make([
            'name' => 'Star Wars', 'votes' => 1
        ]));

        $this->json('DELETE', '/movies/'.$movie->id);

        $this->assertResponseStatus(403);
    }
}
