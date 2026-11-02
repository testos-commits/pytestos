@extends('web.layout')

@section('content')
    <div class="relative">

        <div class="bg-white rounded-md px-4 py-6 fixed w-full bottom-0 left-0">
            <div class="flex flex-row justify-between items-center">
                <div>
                    <h1 class="text-base font-bold text-py-blue">{{ $subscription->name }}</h1>
                    <p class=" text-sm text-py-orange">{{ $subscription->price_text }}</p>
                </div>

                <form action="{{ route('subscription.subscribe', ['subscriptionId' => $subscription->id]) }}" method="post">
                    @csrf

                    <button type="submit" class="!bg-py-orange text-white text-sm px-4 py-1 rounded-md ">Subscribe</button>
                </form>
            </div>
        </div>


        <div>
            <h2 class="text-xl font-bold mb-4">Terms of Service</h2>
            <p>
                <strong>1. Acceptance of Terms</strong>
                </br></br>
                By purchasing a subscription for the premium account in our mobile app, you agree to abide by these
                Terms of
                Service.
                </br>
                </br>
                <strong>2. Subscription Details </strong>
                </br>
                </br>
                Annual Fee: The premium subscription is billed at USD 19 per year.</br>
                Account Features: Subscribers will have access to the following features:</br></br>
                Customized notifications for auditions that match your gender and age.</br>
                Ability to add up to 20 pictures beyond the main 5 pictures.</br>
                Management of up to 4 profiles under one account.</br>
                One complimentary one-on-one consultancy call with an agent to optimize your profile.</br></br>

                <strong>3. Payment Terms</strong>
                </br>
                </br>
                Subscriptions are billed annually and automatically renew at the end of each billing period unless
                canceled
                prior to
                the renewal date.</br>
                You authorize us to charge the payment method provided at the time of subscription.</br>
                </br>

                <strong>4. Cancellation</strong></br></br>
                You may cancel your subscription at any time; however, cancellations will not take effect until the end
                of
                the
                current billing cycle.
                </br></br>
            </p>

        </div>

        <div style="padding-bottom:100px;">
            <h2 class="text-xl font-bold mb-4">Refund Policy</h2>

            <p>
                <strong>1. Non-Refundable Subscriptions</strong>
                </br>
                </br>
                All subscription fees are non-refundable. This means once the subscription is purchased, there are no
                refunds
                for any reason, including partial usage of the services.
                </br>
                </br>

                <strong> 2. Auto-Renewal</strong>
                </br>
                </br>
                Your subscription will automatically renew at the end of each billing cycle unless canceled in
                accordance
                with
                our cancellation policy.
                </br></br>
                By using our services, you confirm that you have read, understood, and accepted these terms. If you do
                not
                agree
                with any part of the terms or the refund policy, please refrain from making a purchase.
            </p>
        </div>
    </div>
@endsection
