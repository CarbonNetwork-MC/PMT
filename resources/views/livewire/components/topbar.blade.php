<div class="w-full pt-4 px-4 relative flex items-center justify-end gap-x-4" x-data="{ open: false }">

    <!-- Dark Mode Toggle -->
    <div x-on:click="darkMode = !darkMode" class="flex items-center justify-center p-1.5 rounded-md bg-gray-300 dark:bg-gray-700 cursor-pointer">
        <svg x-show="!darkMode" class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M13 3a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0V3ZM6.343 4.929A1 1 0 0 0 4.93 6.343l1.414 1.414a1 1 0 0 0 1.414-1.414L6.343 4.929Zm12.728 1.414a1 1 0 0 0-1.414-1.414l-1.414 1.414a1 1 0 0 0 1.414 1.414l1.414-1.414ZM12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm-9 4a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2H3Zm16 0a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2h-2ZM7.757 17.657a1 1 0 1 0-1.414-1.414l-1.414 1.414a1 1 0 1 0 1.414 1.414l1.414-1.414Zm9.9-1.414a1 1 0 0 0-1.414 1.414l1.414 1.414a1 1 0 0 0 1.414-1.414l-1.414-1.414ZM13 19a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0v-2Z" clip-rule="evenodd"/>
        </svg>
        <svg x-show="darkMode" class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M11.675 2.015a.998.998 0 0 0-.403.011C6.09 2.4 2 6.722 2 12c0 5.523 4.477 10 10 10 4.356 0 8.058-2.784 9.43-6.667a1 1 0 0 0-1.02-1.33c-.08.006-.105.005-.127.005h-.001l-.028-.002A5.227 5.227 0 0 0 20 14a8 8 0 0 1-8-8c0-.952.121-1.752.404-2.558a.996.996 0 0 0 .096-.428V3a1 1 0 0 0-.825-.985Z" clip-rule="evenodd"/>
        </svg>          
    </div>

    <!-- Dropdown -->
    <div>
        <div class="flex items-center justify-end text-black dark:text-white cursor-pointer">
            <div @click="open = !open" class="font-bold flex">
                <img src="{{ $user->profile_photo_url }}" class="w-8 h-8 rounded-full border border-2" alt="{{ $user->name }}" />
                <p class="flex items-center pl-2">{{ $user->name }}</p>
                <svg x-show="open" class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16 14-4-4-4 4"/>
                </svg>
                <svg x-show="!open" class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 10 4 4 4-4"/>
                </svg>
            </div>
        </div>
    
        <div x-show="open" @click.away="open = false" class="absolute right-5 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10">
            <a href="{{ route('profile.show') }}" class="block px-2 py-2 flex items-center text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fi fi-sr-user text-xl"></i>             
                <p class="pl-2">Profile</p>
            </a>
            <a href="" class="block px-2 py-2 flex items-center text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fi fi-ss-settings text-xl"></i>    
                <p class="pl-2">Settings</p>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="block px-2 py-2 text-sm flex items-center gap-x-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 border-t border-gray-300">
                @csrf
                <i class="fi fi-sr-sign-out-alt text-xl"></i>
                <button class="w-full text-left" type="submit" class="pl-2">Logout</button>
            </form>
        </div>
    </div>
</div>
