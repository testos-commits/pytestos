import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        "./node_modules/flowbite/**/*.js"

    ],
    plugins: [
        require('flowbite/plugin')
    ],
    theme: {
        extend: {
            fontFamily: {
              'DM': ['DM Sans'],
            },
          },
        colors: {
            'white' : '#FFFFFF',
           'py-blue': '#0094DA',
           'py-blue-200': '#DEE8F7',
           'py-gray': '#F9FAFC',
           'py-green': '#3CD6A7',
           'py-purple': '#5D5FEF',
           'py-orange': '#FF7911'
        }
    }
}
