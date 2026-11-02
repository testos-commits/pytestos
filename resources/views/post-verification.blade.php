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

<body class='bg-gray-200'>
    <div class="container m-auto px-6 text-center bg-py-blue h-screen justify-center items-center flex">
        <div class="bg-white py-8 px-4 rounded-lg shadow-lg">
            <div class="justify-center items-center flex mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img w-48 mr-4" />
            </div>
            <h1 class="text-2xl mb-8 font-bold text-py-blue">Your account is now verified!</h1>

            <p> Login, create your profile and start applying. We wish you the best of luck!</p>
        </div>
    </div>
</body>

</html>
