<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-white dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-900 dark:text-white uppercase tracking-widest hover:bg-100 dark:hover:bg-white focus:bg-100 dark:focus:bg-white active:bg-gray-100 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-white transition ease-in-out duration-150 px-5']) }}>
    {{ $slot }}
</button>
