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
                <li class="side-item-container">
                    <x-sidebar-nav-link href="{{ route('dashboard.render') }}" :active="request()->routeIs('dashboard.render')">
                        <i class="fi fi-ss-house-chimney-blank"></i>
                        <span class="navItem ms-3">Dashboard</span>
                     </x-sidebar-nav-link>
                </li>
                <li class="side-item-container">
                    <x-sidebar-nav-link href="{{ route('projects.projects.render') }}" :active="request()->routeIs('projects.projects.render')">
                        <i class="fi fi-sr-workflow-setting-alt"></i>                         
                        <span class="navItem ms-3">Projects</span>
                     </x-sidebar-nav-link>
                </li>
                @if ($selectedProject)
                    <div class="my-24">
                        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
                    </div>
                    <li class="side-item-container">
                        <x-sidebar-nav-link href="{{ route('projects.overview.render', ['uuid' => session()->get('selected_project')]) }}" :active="request()->routeIs('projects.overview.render')">
                            <i class="fi fi-sr-overview"></i>
                            <span class="navItem ms-3">Overview</span>
                         </x-sidebar-nav-link>
                    </li>
                    @if ($activeSprints != null)
                        @if ($activeSprints->count() > 1)
                            <li class="side-item-container">
                                <x-sidebar-dropdown dropdownName="boardsDropdown">
                                    <x-slot name="trigger">
                                        <i class="fi fi-sr-game-board-alt"></i>
                                        <span class="navItem ms-3">Boards</span>
                                    </x-slot>

                                    <x-slot name="content">
                                        @foreach ($activeSprints as $sprint)
                                            <x-sidebar-dropdown-link href="{{ route('projects.board.render', ['uuid' => $sprint->uuid]) }}" :active="request()->routeIs('projects.board.render') && request('uuid') == $sprint->id">
                                                <i class="fi fi-rs-clipboard-list-check"></i>
                                                <span class="navItem ms-3">{{ $sprint->name }}</span>
                                            </x-sidebar-dropdown-link>
                                        @endforeach
                                    </x-slot>
                                </x-sidebar-dropdown>
                            </li>
                        @else
                            <li class="side-item-container">
                                <x-sidebar-nav-link href="{{ route('projects.board.render', ['uuid' => $activeSprints[0]->uuid]) }}" :active="request()->routeIs('projects.board.render')">
                                    <i class="fi fi-sr-game-board-alt"></i>
                                    <span class="navItem ms-3">Board</span>
                                </x-sidebar-nav-link>
                            </li>
                        @endif
                    @else
                        <li class="side-item-container">
                            <x-sidebar-nav-link href="{{ route('projects.board.render', ['uuid' => session()->get('selected_project')]) }}" :active="request()->routeIs('projects.board.render')">
                                <i class="fi fi-sr-game-board-alt"></i>
                                <span class="navItem ms-3">Board</span>
                            </x-sidebar-nav-link>
                        </li>
                    @endif
                    <li class="side-item-container">
                        <x-sidebar-nav-link href="{{ route('projects.sprints.render', ['uuid' => session()->get('selected_project')]) }}" :active="request()->routeIs('projects.sprints.render')">
                            <i class="fi fi-sr-running"></i>
                            <span class="navItem ms-3">Sprints</span>
                         </x-sidebar-nav-link>
                    </li>
                    <li class="side-item-container">
                        <x-sidebar-nav-link href="{{ route('projects.backlog.render', ['uuid' => session()->get('selected_project'), 'backlogId' => $selectedBacklog]) }}" :active="request()->routeIs('projects.backlog.render')">
                            <i class="fi fi-ss-cubes-stacked"></i>
                            <span class="navItem ms-3">Backlog</span>
                         </x-sidebar-nav-link>
                    </li>
                    <li class="side-item-container">
                        <x-sidebar-nav-link href="{{ route('projects.settings.overall.render', ['uuid' => session()->get('selected_project')]) }}" :active="request()->routeIs('projects.settings.*')">
                            <i class="fi fi-sr-settings-sliders"></i>
                            <span class="navItem ms-3">Settings</span>
                         </x-sidebar-nav-link>
                    </li>
                @endif
                @if ($user->role->id !== 1)
                    <div class="my-24">
                        <hr class="h-px my-8 bg-gray-200 border-0 dark:bg-gray-700">
                    </div>
                    @if ($user->role && json_decode($user->role->permissions)->view_other_projects)
                        <li class="side-item-container">
                            <x-sidebar-nav-link href="" :active="request()->routeIs('')">
                                <i class="fi fi-sr-archive"></i>
                                <span class="navItem ms-3">Manage Projects</span>
                            </x-sidebar-nav-link>
                        </li>
                    @endif
                    @if ($user->role && json_decode($user->role->permissions)->manage_users)
                        <li class="side-item-container">
                            <x-sidebar-nav-link href="" :active="request()->routeIs('')">
                                <i class="fi fi-sr-admin-alt"></i>
                                <span class="navItem ms-3">Manage Users</span>
                            </x-sidebar-nav-link>
                        </li>
                    @endif
                    @if ($user->role && json_decode($user->role->permissions)->manage_staff)
                        <li class="side-item-container">
                            <x-sidebar-nav-link href="" :active="request()->routeIs('')">
                                <i class="fi fi-sr-user-crown"></i>
                                <span class="navItem ms-3">Manage Staff</span>
                            </x-sidebar-nav-link>
                        </li>
                    @endif
                @endif
            </ul>
        </div>
    </div>

    <script>

        // Sidebar toggle
        $(document).ready(function() {
            var $sidebar = $("#sidebar-navbar");
            var $sidebarItems = $(".side-item-container");
            var $chevrons = $("#chevrons");
            var $dropdownContent = $("#dropdown-content");
            var $navItems = $(".navItem");
            var $open = localStorage.getItem('sidebarState') !== 'closed';

            // Set initial sidebar state
            if (!$open) {
                $sidebar.addClass("w-[4rem]").removeClass("w-[18rem]");
                $sidebarItems.addClass("flex justify-center");
                $chevrons.hide();
                $dropdownContent.hide();
                $navItems.hide();
            } else {
                $sidebar.addClass("w-[18rem]").removeClass("w-[4rem]");
                $sidebarItems.removeClass("flex justify-center");
            }

            $("#toggleButton").click(function() {
                if ($open) {
                    $sidebar.addClass("w-[4rem]").removeClass("w-[18rem]");
                    $sidebarItems.addClass("flex justify-center");
                    $chevrons.hide();
                    $dropdownContent.hide();
                    $open = false;
                    $navItems.hide();
                    localStorage.setItem('sidebarState', 'closed');
                    localStorage.setItem('internshipDropdown', false);
                } else {
                    $sidebar.addClass("w-[18rem]").removeClass("w-[4rem]");
                    $sidebarItems.removeClass("flex justify-center");
                    $chevrons.show();
                    $open = true;
                    $navItems.show();
                    localStorage.setItem('sidebarState', 'open');
                }
            });
        });

        // Dropdown toggle
        $(document).ready(function() {

        });

    </script>

</div>
