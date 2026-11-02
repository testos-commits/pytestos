<div class="flex flex-row items-center justify-center">
    <img src="{{ asset('images/logo_no_icon.png') }}" alt="Logo" class="logo-img w-40" />

    <button
        style="--c-300: var(--gray-300); --c-400: var(--gray-400); --c-500: var(--gray-500); --c-600: var(--gray-600);"
        class="fi-icon-btn relative  items-center justify-center rounded-lg outline-none transition duration-75 focus-visible:ring-2 disabled:pointer-events-none disabled:opacity-70 -m-1.5 h-9 w-9 fi-color-gray text-gray-400 hover:text-gray-500 focus-visible:ring-primary-600 dark:text-gray-500 dark:hover:text-gray-400 dark:focus-visible:ring-primary-500 ms-auto lg:flex block"
        title="Collapse sidebar" type="button" x-data="{}" x-on:click="$store.sidebar.close()"
        x-show="$store.sidebar.isOpen">
        <span class="sr-only">
            Collapse sidebar
        </span>
        @svg('heroicon-m-x-mark')
    </button>

    <button
        style="--c-300: var(--gray-300); --c-400: var(--gray-400); --c-500: var(--gray-500); --c-600: var(--gray-600);"
        class="fi-icon-bt relative flex items-center justify-center rounded-lg outline-none transition duration-75 focus-visible:ring-2 disabled:pointer-events-none disabled:opacity-70 -mx-4 h-9 w-9 fi-color-gray text-gray-400 hover:text-gray-500 focus-visible:ring-primary-600 dark:text-gray-500 dark:hover:text-gray-400 dark:focus-visible:ring-primary-500 ms-auto lg:flex hidden"
        title="Collapse sidebar" type="button" x-data="{}" x-on:click="$store.sidebar.open()"
        x-show="!$store.sidebar.isOpen">
        <span class="sr-only">
            Collapse sidebar
        </span>

        @svg('heroicon-m-bars-3')
    </button>

    <div class="absolute -right-12 w-9 lg:hidden">
        <button
            style="--c-300: var(--gray-300); --c-400: var(--gray-400); --c-500: var(--gray-500); --c-600: var(--gray-600);"
            class="fi-icon-bt relative flex items-center justify-center rounded-lg outline-none transition duration-75 focus-visible:ring-2 disabled:pointer-events-none disabled:opacity-70  h-9 w-9 fi-gray-400 text-gray-400 hover:text-gray-500 focus-visible:ring-primary-600 dark:text-gray-500 dark:hover:text-gray-400 dark:focus-visible:ring-primary-500 ms-auto lg:flex"
            title="Collapse sidebar" type="button" x-data="{}" x-on:click="$store.sidebar.open()"
            x-show="!$store.sidebar.isOpen">
            <span class="sr-only">
                Collapse sidebar
            </span>

            <x-heroicon-m-bars-3 class="h-7 w-7" />
        </button>
    </div>

</div>
