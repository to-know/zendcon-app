<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\Movie;
use App\Http\Requests;
use App\Events\MovieCreated;
use App\Events\MovieDeleted;
use App\Events\MovieReceivedVote;
use App\Http\Controllers\Controller;

class MovieController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Return a list of all of the movies.
     *
     * @return Response
     */
    public function index()
    {
        return Movie::with('user')->get();
    }

    /**
     * Create a new movie record.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'movie' => 'required|max:255',
        ]);

        $movie = $request->user()->movies()->create([
            'name' => $request->movie,
        ])->load('user');

        event(new MovieCreated($movie));

        return $movie;
    }

    /**
     * Add a vote for the given movie.
     *
     * @param  \App\Movie  $movie
     * @return Response
     */
    public function vote(Movie $movie)
    {
        $movie->increment('votes');

        event(new MovieReceivedVote($movie));
    }

    /**
     * Destroy the given movie.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Movie  $movie
     * @return Response
     */
    public function destroy(Request $request, Movie $movie)
    {
        if ($movie->user->id !== $request->user()->id) {
            abort(403);
        }

        $movie->delete();

        event(new MovieDeleted($movie->id));
    }
}
