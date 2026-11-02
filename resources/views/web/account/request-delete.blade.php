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
    <div class="container m-auto py-20 px-20 bg-white h-screen">
        <div class="mb-8 flex justify-center">
            <div class="text-center">
                <img class="w-56 m-0 m-auto block" src="{{ URL::to('/images/logo.png') }}" />
                <h1 class="text-2xl font-bold text-py-blue">Promote Yourself Casting</h1>
                <p class="text-gray-400">(Developed by Virtual Mina LLC)</p>
            </div>
        </div>

        <h1 class="text-xl mb-8 font-bold">Steps to delete your account</h1>

        <div class="mb-10">
            <h3 class="text-lg mb-4 font-bold">Delete a profile under your main account</h3>

            <p class="text-base mb-2 pl-10">1. Go to your account settings in the bottom navigation tab</p>
            <p class="text-base mb-2 pl-10">2. Switch to the profile you want to delete</p>
            <p class="text-base mb-2 pl-10">3. Click on the delete profile button</p>
            <p class="text-base mb-2 pl-10">4. Confirm the deletion</p>
            <p class="text-base mb-2 pl-10">5. The profile will be deleted, including all your answers to our messages,
                your
                audition applications,
                payment details, uploaded media such as photos, images and videos.</p>

            <p class="text-base mb-2 pl-10">6. Repeat the above steps for all the profiles you want to delete</p>

            <p class="text-base mb-2 pl-10">7. Once you have 1 profile left, you will only be able to delete your main
                account
                along with the profile
                you
                have left.</p>
        </div>

        <div class="mb-4">

            <h3 class="text-lg font-bold mb-4">Delete your main account</h3>

            <p class="text-base mb-2 pl-10">1. Go to your account settings in the bottom navigation tab</p>
            <p class="text-base mb-2 pl-10">2. Click on the delete account button</p>
            <p class="text-base mb-2 pl-10">3. Confirm the deletion</p>
            <p class="text-base mb-2 pl-10">4. Your main account will be deleted, including all your profiles, answers
                to our
                messages, your audition
                applications, payment details, uploaded media such as photos, images and videos. You will no longer be
                able
                to login to the app.</p>
        </div>
    </div>
</body>

</html>
