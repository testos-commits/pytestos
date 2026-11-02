<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Roboto:wght@300;400;700&family=Rokkitt:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body >
    <div x-data="{ sidebarOpen: false }" class="flex h-full relative bg-gray-100">

        <!-- Content -->
        <div class="flex-1 p-4 bg-py-gray relative">
            <button @click="sidebarOpen = !sidebarOpen" class="mb-4 md:hidden right-0 rounded text-white p-2 absolute">
                @svg('heroicon-m-bars-3', ['class' => 'w-6 h-6', 'fill' => '#0094da'])
            </button>

            <div class="mt-12">
                @yield('content')
            </div>
        </div>

        <div :class="sidebarOpen ? 'block' : 'hidden'"  class="md:block w-full  h-full absolute t-0 b-0 bg-black/10" @click="sidebarOpen = !sidebarOpen">
                {{-- don't close sidebar when the below is clicked --}}
            <div class="p-4 text-gray-400 text-sm relative bg-white w-2/4 h-full"  @click.stop=""> 
                <x-auth.application-logo class="w-20 h-16 text-left" />
                <ul>
                    <li class="py-3 "><a href="{{ route('account.manage') }}"
                            class="flex flex-row items-center {{Route::currentRouteName() === 'account.manage' ? 'text-py-blue' : ''}}">@svg('heroicon-o-information-circle', ['class' => "h-5 mr-2 font-bold {{Route::currentRouteName() === 'account.manage' ? 'text-py-blue' : ''}}"]) User Information</a></li>
                    <li class="py-3"><a href="{{ route('account.profiles') }}"
                            class="flex flex-row items-center {{Route::currentRouteName() === 'account.profiles' ? 'text-py-blue' : ''}}">@svg('heroicon-c-user-plus', ['class' => "h-5 mr-2 {{Route::currentRouteName() === 'account.profiles' ? 'text-py-blue' : ''}}"]) Profile Information</a></li>
                    <li class="py-3"><a href="{{ route('account.subscriptions') }}"
                            class="flex flex-row items-center {{Route::currentRouteName() === 'account.subscriptions' ? 'text-py-blue' : ''}}">@svg('heroicon-m-key', ['class' => "h-5 mr-2 {{Route::currentRouteName() === 'account.subscriptions' ? 'text-py-blue' : ''}}"]) Subscription Plan</a></li>
                    <li class="py-3"><a href="{{ route('account.payment-method') }}"
                            class="flex flex-row items-center {{Route::currentRouteName() === 'account.payment-method' ? 'text-py-blue' : ''}}"> @svg('heroicon-o-credit-card', ['class' => "h-5 mr-2 {{Route::currentRouteName() === 'account.payment-method' ? 'text-py-blue' : ''}}"])Payment Method</a></li>
                    <li class="py-3">
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button type="submit" class="flex flex-row items-center">@svg('heroicon-c-power', ['class' => 'h-5 mr-2']) Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
</body>

</html>
