$(document).ready(function () {

    $('.owl-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        center: true,
        dots: true,
        navText: [
            `<button type="button"
                class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                data-carousel-prev>
                <span
                    class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 1 1 5l4 4" />
                    </svg>
                    <span class="sr-only">Previous</span>
                </span>
            </button>`,
            `<button type="button"
                class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                data-carousel-next>
                <span
                    class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 9 4-4-4-4" />
                    </svg>
                    <span class="sr-only">Next</span>
                </span>
            </button>`
        ],
        responsive: {
            0: {
                items: 1
            },
        }
    });

    var isActionPopupOpen = false;

    const popup = document.getElementById('js-manage-audition-actions');
    const trigger = document.getElementById('js-manage-audition-actions-trigger');

    trigger.addEventListener('click', event => {
        if (isActionPopupOpen) {
            isActionPopupOpen = false;
            popup.classList.add('hidden');

        } else {
            isActionPopupOpen = true;
            popup.classList.remove('hidden');
        }
    });

    // window.addEventListener('manage-audition-actions-popup-toggle', event => {
    //     if (isActionPopupOpen) {
    //         isActionPopupOpen = false;
    //         popup.classList.add('hidden');
    //         console.log(popup.classList);
    //     } else {
    //         isActionPopupOpen = true;
    //         popup.classList.remove('hidden');
    //         console.log(popup.classList);
    //     }
    // })

    popup.querySelectorAll('li').forEach(item => {
        item.addEventListener('click', event => {
            popup.classList.add('hidden');
            isActionPopupOpen = false
        });
    });
});