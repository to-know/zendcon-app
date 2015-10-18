<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use Socialite;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Authenticate the user with GitHub.
     *
     * @return Response
     */
    public function authenticate()
    {
        return Socialite::with('github')->redirect();
    }

    /**
     * Handle the authentication callback from GitHub.
     *
     * @return Response
     */
    public function handleGitHubCallback()
    {
        $github = Socialite::with('github')->user();

        $user = User::where('github_id', $github->id)->first();

        if ($user) {
            Auth::login($user);
        } else {
            Auth::login($user = User::create([
                'email' => $github->email,
                'name' => $github->name,
                'github_id' => $github->id,
            ]));
        }

        return redirect('/');
    }

    /**
     * Log the user out of the application.
     *
     * @return Response
     */
    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }
}
