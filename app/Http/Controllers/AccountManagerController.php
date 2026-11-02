<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserSubscription;

class AccountManagerController extends Controller
{
    public function authorizeRequest()
    {
        if (auth('sanctum')->check()) {
            $user = auth('sanctum')->user();

            // Generate a new token
            $newToken = bin2hex(random_bytes(32));

            $user->manageAccountRequestToken()->updateOrCreate(
                ['user_id' => $user->id],
                ['token' => $newToken]
            );

            return response()->json(['token' => route('account.verify', ['token' => $newToken])], 200);
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    public function verify($token)
    {
        $user = User::whereHas('manageAccountRequestToken', function ($query) use ($token) {
            $query->where('token', $token);
        })->first();


        if ($user) {
            auth('web')->login($user);
            return redirect()->route('account.manage');
        } else {
            return redirect()->route('login');
        }
    }

    public function manageAccount()
    {
        $user = User::withCount('profiles')->with('validSubscription')->find(auth('web')->user()->id);
        $subscriptionValid = 'Not active';
        

        if(count($user->validSubscription)) {
            $subscriptionValid = 'Active';
        }

        return view('web.account.overview', ['user' => $user, 'subscriptionValid' => $subscriptionValid]);
    }

    public function profiles()
    {
        $user = User::withCount('profiles')->find(auth('web')->user()->id);
        return view('web.account.profiles', ['user' => $user]);
    }

    public function requestDelete() {
        return view('web.account.request-delete');
    }
}
