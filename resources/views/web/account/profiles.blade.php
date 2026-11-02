@extends('web.layout')

@section('content')
    <div class="bg-white rounded-md px-4 py-4 mb-4">
        <h1 class="text-2xl font-bold mb-4 text-py-blue">Profiles Overview</h1>
        <p class="text-gray-500">These are the profiles under your main account.</p>
    </div>

    @foreach ($user->profiles as $profile)
        <div class="bg-white rounded-md px-4 py-4 flex flex-row justify-start items-center mb-2">
            <img src="{{ $profile->getAvatar() }}" class="h-16 w-16 rounded-full object-cover" />
            <div class="ml-4">
                <h2 class="text-base font-medium text-py-blue">{{ $profile->first_name }} {{ $profile->last_name }}</h2>
                <h2 class="text-sm font-regular text-gray-500">{{ $profile->age }} years old, joined {{ $profile->created_at->diffForHumans() }}</h2>
            </div>
        </div>
    @endforeach
@endsection
