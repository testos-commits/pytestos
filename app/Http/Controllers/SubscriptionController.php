<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSubscription;
use App\Models\Subscription;

class SubscriptionController extends Controller
{

    public function index()
    {
        $subscriptionStatus = UserSubscription::where('user_id', auth('web')->user()->id)->valid()->latest()->first();
        $subscriptions = Subscription::all();

        $accountType = $subscriptions->where('is_free',1)->first()->name;

        if($subscriptionStatus) {
            $subscriptions = $subscriptions->map(function ($subscription) use ($subscriptionStatus, &$accountType) {
                if ($subscriptionStatus->subscription_id === $subscription->id) {
                    $subscription->is_valid = true;
                    $accountType = $subscription->name;
                } else {
                    $subscription->is_valid = false;
                }
                
                return $subscription;
            });
        } else {
            $subscriptions = $subscriptions->map(function ($subscription) {
                $subscription->is_valid = false;
                return $subscription;
            });
        }


        return view('web.account.subscriptions', compact('subscriptions', 'accountType'));
    }

    public function terms($subscriptionId)
    {   
        $subscription = Subscription::find($subscriptionId);
        return view('web.account.terms-of-service', compact('subscription'));
    }

    public function subscribe($subscriptionId)
    {
        // Subscribe the user to the selected subscription
        if(UserSubscription::where('user_id', auth('web')->user()->id)->valid()->exists()) {
            return redirect()->back()->with('error', 'You already have an active subscription');
        }

        UserSubscription::create([
            'user_id' => auth('web')->user()->id,
            'subscription_id' => $subscriptionId,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'is_active' => true,
        ]);

        return redirect()->route('account.subscriptions');
    }

    public function cancelSubscription($subscriptionId)
    {
        // Cancel the user's subscription
        UserSubscription::where('user_id', auth('web')->user()->id)
            ->where('subscription_id', $subscriptionId)
            ->update(['is_active' => false]);

        return redirect()->back();
    }
}
