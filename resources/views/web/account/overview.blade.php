@extends('web.layout')

@section('content')
    <div class="bg-white rounded-md px-4 py-4 mb-4">
        <h1 class="text-2xl font-bold mb-4 text-py-blue">Account Overview</h1>
        <p class="text-gray-500">Welcome to your account overview. Here you can manage your account settings and profile
            information.</p>
    </div>

    <div class="bg-white rounded-md px-4 py-4">

        <h2 class="text-xl font-bold mb-4 text-py-blue">Account Information</h2>

        <div class="mb-4">
            <p class='font-bold'>Account email address:</p> <span class="text-gray-500">{{ $user->email }}</span>
        </div>

        <div class="mb-4">
            <p class="font-bold">Joined:</p> <span class="text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
        </div>

        <div class="mb-4">
            <p class="font-bold">Number of profiles:</p> <span class="text-gray-500">{{ $user->profiles_count }}</span>
        </div>

        <div class="mb-4">
            <div class="flex justify-between items-center">
                <p class="font-bold">Subscription:</p> <span class="text-gray-500">{{ $subscriptionValid }}</span>
                <a class="bg-py-orange py-2 px-2 rounded-md text-white block cursor-pointer" href="{{route('account.subscriptions')}}">Manage</a>
            </div>
        </div>
    </div>
@endsection
