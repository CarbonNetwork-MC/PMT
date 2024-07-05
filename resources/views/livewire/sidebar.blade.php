<div id="sidebar-navbar" class="hidden lg:block">
    <div class="z-40 w-full h-screen transition-transform -translate-x-full sm:translate-x-0">
        <div class="h-full px-3 py-4 overflow-y-auto bg-carbon-900 dark:bg-gray-800">

            <div class="flex justify-end">
                <div id="toggleButton" class="mb-12 flex items-center p-2 text-white rounded-lg hover:bg-carbon-500 group cursor-pointer">
                    <svg class="w-6 h-6 text-white transition duration-75" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14"/>
                    </svg>
                </div>
            </div>

            <ul class="space-y-2 font-medium">
                <li>
                    <x-sidebar-nav-link href="{{ route('dashboard.render') }}" :active="request()->routeIs('dashboard.render')">
                        <svg class="w-6 h-6 text-white transition duration-75" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M11.293 3.293a1 1 0 0 1 1.414 0l6 6 2 2a1 1 0 0 1-1.414 1.414L19 12.414V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2v-6.586l-.293.293a1 1 0 0 1-1.414-1.414l2-2 6-6Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="navItem ms-3">Dashboard</span>
                     </x-sidebar-nav-link>
                </li>
                <li>
                    <x-sidebar-nav-link href="{{ route('projects.overview.render') }}" :active="request()->routeIs('projects.overview.render')">
                        <svg class="w-6 h-6 text-white transition duration-75" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M15 4H9v16h6V4Zm2 16h3a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-3v16ZM4 4h3v16H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" clip-rule="evenodd"/>
                        </svg>                          
                        <span class="navItem ms-3">Projects</span>
                     </x-sidebar-nav-link>
                </li>
            </ul>
        </div>
    </div>

    <script>

        // Sidebar toggle
        $(document).ready(function() {
            var $sidebar = $("#sidebar-navbar");
            var $chevrons = $("#chevrons");
            var $dropdownContent = $("#dropdown-content");
            var $navItems = $(".navItem");
            var $open = localStorage.getItem('sidebarState') !== 'closed';

            // Set initial sidebar state
            if (!$open) {
                $sidebar.addClass("w-[4rem]").removeClass("w-[18rem]");
                $chevrons.hide();
                $dropdownContent.hide();
                $navItems.hide();
            } else {
                $sidebar.addClass("w-[18rem]").removeClass("w-[4rem]");
            }

            $("#toggleButton").click(function() {
                if ($open) {
                    $sidebar.addClass("w-[4rem]").removeClass("w-[18rem]");
                    $chevrons.hide();
                    $dropdownContent.hide();
                    $open = false;
                    $navItems.fadeOut(150).delay(100);
                    localStorage.setItem('sidebarState', 'closed');
                    localStorage.setItem('internshipDropdown', false);
                } else {
                    $sidebar.addClass("w-[18rem]").removeClass("w-[4rem]");
                    $chevrons.show();
                    $open = true;
                    $navItems.delay(150).fadeIn(500);
                    localStorage.setItem('sidebarState', 'open');
                }
            });
        });

        // Dropdown toggle
        $(document).ready(function() {

        });

    </script>

</div>
