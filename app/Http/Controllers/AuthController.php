<?php namespace Korobi\Http\Controllers;


use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

class AuthController extends BaseController {

    public function __construct(SocialiteFactory $socialite) {
        $this->socialite = $socialite;
    }

    public function getUserDetails() {
        $user = $this->socialite->driver('github')->user();
        dd($user);
    }

    public function redirectToGitHub() {
        return $this->socialite->driver('github')->scopes([])->redirect();
    }

}
