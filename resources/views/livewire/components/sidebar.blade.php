<div x-data="sidebarState()" x-init="init()" @keydown.window.escape="isOpenMobile && closeMobile()" class="relative">
    <!-- Mobile overlay -->
    <div x-show="isOpenMobile" x-transition.opacity @click="closeMobile()"
        class="fixed inset-0 z-40 bg-black/40 lg:hidden" aria-hidden="true"></div>

    <!-- Sidebar panel -->
    <aside :class="[
            (isOpenMobile || isDesktop) ? 'translate-x-0' : '-translate-x-full',
            isCollapsed ? 'lg:w-20' : 'lg:w-64'
        ]" 
        x-transition:enter="transition ease-out duration-200" 
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0" 
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0" 
        x-transition:leave-end="-translate-x-full"
        class="fixed lg:sticky top-0 z-20 lg:top-20 h-dvh shrink-0 bg-zinc-900/90 backdrop-blur border-r border-zinc-800 p-3 lg:p-4 will-change-transform transition-all duration-200 flex flex-col overflow-hidden"
        aria-label="Sidebar"
    >

        <!-- Header / Brand + Collapse Toggle (desktop) -->
        <div class="flex items-center mb-2 border-b border-zinc-400 pb-3" :class="isCollapsed ? 'flex-col justify-center' : 'justify-between'">
            <a href="{{ route('dashboard.render') }}" x-show="!isCollapsed" class="flex items-center gap-2">
                <span class="font-semibold text-lg text-zinc-100">PMT</span>
            </a>
            <button type="button"
                class="items-center justify-center rounded-xl p-2 text-zinc-300 hover:text-white cursor-pointer"
                @click="toggleCollapse()" :aria-expanded="(!isCollapsed).toString()"
                :title="isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                <i class="flex items-center fi"
                    :class="isCollapsed ? 'fi-tr-angle-double-small-right' : 'fi-tr-angle-double-small-left'"></i>
            </button>
        </div>

        <!-- Nav -->
        @if (!request()->routeIs('admin.*'))
            <nav class="space-y-2 flex-1 min-h-0">
                {{-- Dashboard --}}
                <x-sidebar.nav-item
                    :href="route('dashboard.render')"
                    :active="request()->routeIs('dashboard.render')"
                    icon="fi fi-rr-home"
                    :label="__('sidebar.dashboard')"
                />

                {{-- Projects --}}
                <x-sidebar.nav-item
                    :href="route('projects.render')"
                    :active="request()->routeIs('projects.render') || request()->routeIs('projects.new.render')"
                    icon="fi fi-sr-department-structure"
                    :label="__('sidebar.projects.title')"
                />

                @if ($selectedProject)
                    <x-sidebar.nav-divider />

                    {{-- Project Dashboard --}}
                    <x-sidebar.nav-item
                        :href="route('projects.dashboard.render', ['uuid' => $selectedProject->uuid])"
                        :active="request()->routeIs('projects.dashboard.*')"
                        icon="fi fi-rs-dashboard"
                        :label="__('sidebar.projects.dashboard')"
                    />

                    {{-- Project Board --}}
                    @if ($selectedProject->sprints->where('status', 'active')->count() <= 1)
                        @if ($selectedProject->sprints->where('status', 'active')->count() === 1)
                            @php
                                $activeSprint = $selectedProject->sprints->where('status', 'active')->first();
                            @endphp
                            <x-sidebar.nav-item
                                :href="route('projects.board.render', ['uuid' => $selectedProject->uuid, 'sprintUuid' => $activeSprint->uuid])"
                                :active="request()->routeIs('projects.board.*') && request()->route('sprintUuid') === $activeSprint->uuid"
                                icon="fi fi-sr-game-board-alt"
                                :label="__('sidebar.projects.board')"
                            />
                        @else
                            <x-sidebar.nav-item
                                :href="route('projects.sprints.render', ['uuid' => $selectedProject->uuid])"
                                :active="request()->routeIs('projects.board.*')"
                                icon="fi fi-sr-game-board-alt"
                                :label="__('sidebar.projects.board')"
                            />
                        @endif
                    @else
                        <x-sidebar.nav-group :groupKey="'boards'" wire:key="boards" label="{{ __('sidebar.projects.board') }}" icon="sr-game-board-alt">
                            @foreach ($selectedProject->sprints->where('status', 'active') as $sprint)
                                <x-sidebar.nav-group-item
                                    href="{{ route('projects.board.render', ['uuid' => $selectedProject->uuid, 'sprintUuid' => $sprint->uuid]) }}"
                                    :active="request()->routeIs('projects.board.*') && request()->route('sprintUuid') === $sprint->uuid"
                                    wire:key="board-{{ $sprint->uuid }}"
                                    icon="rs-clipboard-list-check"
                                >
                                    {{ $sprint->name }}
                                </x-sidebar.nav-group-item>
                            @endforeach
                        </x-sidebar.nav-group>
                    @endif

                    {{-- Project Sprints --}}
                    <x-sidebar.nav-item
                        :href="route('projects.sprints.render', ['uuid' => $selectedProject->uuid])"
                        :active="request()->routeIs('projects.sprints.*')"
                        icon="fi fi-br-running"
                        :label="__('sidebar.projects.sprints')"
                    />
                    
                    {{-- Project Backlog --}}
                    <x-sidebar.nav-item
                        :href="route('projects.backlog.render', ['uuid' => $selectedProject->uuid])"
                        :active="request()->routeIs('projects.backlog.*')"
                        icon="fi fi-br-cubes-stacked"
                        :label="__('sidebar.projects.backlog')"
                    />

                    {{-- Sprint Archive --}}
                    <x-sidebar.nav-item
                    :href="route('projects.archive.render', ['uuid' => $selectedProject->uuid])"
                    :active="request()->routeIs('projects.archive.*')"
                    icon="fi fi-br-archive"
                    :label="__('sidebar.projects.archive')"
                    />

                    {{-- Project Settings --}}
                    <x-sidebar.nav-item
                        :href="route('projects.settings.general.render', ['uuid' => $selectedProject->uuid])"
                        :active="request()->routeIs('projects.settings.*')"
                        icon="fi fi-br-settings-sliders"
                        :label="__('sidebar.projects.settings')"
                    />
                @endif
            </nav>
        @endif

        <!-- Admin Nav -->
        @if (request()->routeIs('admin.*') && ($user->hasRole('Superadmin') || $user->hasRole('Admin')))
            <nav class="space-y-2 flex-1 min-h-0 overflow-y-auto hide-scrollbar">
                {{-- Admin Dashboard --}}
                <x-sidebar.nav-item
                    :href="route('admin.dashboard.render')"
                    :active="request()->routeIs('admin.dashboard.*')"
                    icon="fi fi-rr-home"
                    :label="__('sidebar.dashboard')"
                />

                {{-- Users --}}
                @if (\App\Helpers\CheckIfPermissionExists::check('manage-users') && $user->can('manage-users'))
                    <x-sidebar.nav-item
                        :href="route('admin.users.render')"
                        :active="request()->routeIs('admin.users.*')"
                        icon="fi fi-rr-users-alt"
                        :label="__('sidebar.admin.users')"
                    />
                @endif

                {{-- Roles and Permissions --}}
                @if (\App\Helpers\CheckIfPermissionExists::check('manage-permissions') && $user->can('manage-permissions'))
                    <x-sidebar.nav-item
                        :href="route('admin.roles-and-permissions.render')"
                        :active="request()->routeIs('admin.roles-and-permissions.*')"
                        icon="fi fi-rr-shield-check"
                        :label="__('sidebar.admin.roles_permissions')"
                    />
                @endif
            </nav>
        @endif

        <!-- Footer -->
        <div class="mt-auto">
            @if ($user->hasRole('Superadmin') || $user->hasRole('Admin'))
                {{-- Admin Section --}}
                <x-sidebar.nav-divider />

                <div class="mb-2">
                    @if (request()->routeIs('admin.*'))
                        <x-sidebar.nav-item
                            :href="route('dashboard.render')"
                            :active="false"
                            icon="fi fi-rr-arrow-small-left"
                            :label="__('sidebar.back_to_dashboard')"
                        />
                    @else
                        <x-sidebar.nav-item
                            :href="route('admin.dashboard.render')"
                            :active="request()->routeIs('admin.dashboard.*')"
                            icon="fi fi-rr-admin-alt"
                            :label="__('sidebar.management')"
                        />
                    @endif
                </div>
            @endif

            <div class="border-t border-zinc-200 dark:border-zinc-800 pt-3">
                <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                    <!-- Profile button -->
                    <button type="button" @click="open = !open" :title="isCollapsed ? '{{ Auth::user()->name ?? 'Account' }}' : null"
                        class="w-full rounded-xl px-2 py-2 hover:bg-zinc-800 flex items-center gap-3 cursor-pointer"
                    >

                        <!-- Avatar -->
                        <img src="{{ $userProfilePicture
                            ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name ?? 'U') . '&background=16a34a&color=ffffff' }}"
                            alt="{{ Auth::user()->name ?? 'User' }}" class="h-8 w-8 rounded-xl object-cover" />

                        <!-- Name / email (hidden when collapsed) -->
                        <div x-show="!isCollapsed" class="min-w-0 text-left">
                            <p class="text-sm font-medium text-zinc-100 truncate">
                                {{ Auth::user()->name ?? 'Your Name' }}
                            </p>
                            <p class="text-xs text-zinc-300 truncate">
                                {{ Auth::user()->email ?? 'you@example.com' }}
                            </p>
                        </div>

                        <!-- Chevron (hidden when collapsed) -->
                        <svg x-show="!isCollapsed" :class="{ 'rotate-0': open, 'rotate-180': !open }" class="ml-auto h-4 w-4 text-zinc-300 transition-transform" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true"
                        >
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Dropdown (opens UP) -->
                    <div x-show="open" x-transition.origin-bottom @click.outside="open = false" class="absolute left-0 right-0 z-50 bottom-12 mb-2"
                        :class="isCollapsed ? 'left-1/2 -translate-x-1/2 w-56' : 'left-0 right-0'">

                        <div class="rounded-xl border border-zinc-800 bg-zinc-900 shadow-xl overflow-hidden">
                            <!-- Dark mode switch -->
                            <label class="flex items-center gap-2 px-3 py-2 text-sm cursor-pointer select-none hover:bg-zinc-800"
                                role="switch" :aria-checked="dark.toString()"
                            >
                                <i class="fi text-white" :class="dark ? 'fi-rr-moon' : 'fi-rr-sun'"></i>
                                <span class="text-zinc-200">Dark mode</span>

                                <input type="checkbox" x-model="dark" class="sr-only peer" />

                                <span class="ml-auto relative bg-zinc-700 inline-flex h-5 w-9 items-center rounded-full transition-colors duration-200">
                                    <span class="h-4 w-4 bg-white rounded-full shadow transform transition-transform duration-200"
                                        :class="dark ? 'translate-x-5' : 'translate-x-1'"></span>
                                </span>
                            </label>

                            <div class="border-t border-zinc-200 dark:border-zinc-800"></div>

                            <a href="{{ route('profile.overview.render') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-zinc-200 hover:bg-zinc-800">
                                <i class="fi fi-rr-user"></i>
                                <span>Profile</span>
                            </a>

                            <div class="border-t border-zinc-200 dark:border-zinc-800"></div>

                            <a href="" class="flex items-center gap-2 px-3 py-2 text-sm text-zinc-200 hover:bg-zinc-800">
                                <i class="fi fi-rr-settings"></i>
                                <span>Settings</span>
                            </a>

                            <div class="border-t border-zinc-200 dark:border-zinc-800"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-500 hover:text-red-600 hover:bg-zinc-800 cursor-pointer">
                                    <i class="fi fi-rr-exit"></i>
                                    <span>Log out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile top bar -->
    <div
        class="lg:hidden sticky top-0 z-20 bg-white/80 dark:bg-zinc-900/80 backdrop-blur border-b border-zinc-200 dark:border-zinc-800">
        <div class="flex items-center gap-2 p-3">
            <button @click="openMobile()"
                class="inline-flex items-center justify-center rounded-xl border border-zinc-200 dark:border-zinc-800 p-2 text-zinc-700 dark:text-zinc-200">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                    <path d="M3 6h18v2H3V6zm0 5h18v2H3v-2zm0 5h18v2H3v-2z" />
                </svg>
            </button>
            <span class="font-semibold text-zinc-800 dark:text-zinc-100">Menu</span>
        </div>
    </div>
</div>

<script>
    function sidebarState() {
        const STORAGE_KEY = 'sidebar:collapsed';
        const GROUPS_KEY = 'sidebar:openGroups';
        return {
            isDesktop: window.matchMedia('(min-width: 1024px)').matches,
            isOpenMobile: false,
            isCollapsed: false,
            openGroups: new Set(),
            init() {
                    const saved = localStorage.getItem(STORAGE_KEY);
                    this.isCollapsed = saved === '1';

                    const g = JSON.parse(localStorage.getItem(GROUPS_KEY) || '[]');
                    this.openGroups = new Set(g);

                    const mq = window.matchMedia('(min-width: 1024px)');
                    const sync = () => { this.isDesktop = mq.matches; if (!this.isDesktop) this.isCollapsed = false; };
                    mq.addEventListener('change', sync);
                    sync();
                },
                persist() {
                    localStorage.setItem(STORAGE_KEY, this.isCollapsed ? '1' : '0');
                    localStorage.setItem(GROUPS_KEY, JSON.stringify(Array.from(this.openGroups)));
                },
                toggleCollapse() {
                    this.isCollapsed = !this.isCollapsed;
                    localStorage.setItem(STORAGE_KEY, this.isCollapsed ? '1' : '0');
                },
                expandFromIcon() {
                    this.isCollapsed = false;
                    this.persist();
                },
                openMobile() { this.isOpenMobile = true; },
                closeMobile() { this.isOpenMobile = false; },
                toggleGroup(key) {
                    if (this.openGroups.has(key)) this.openGroups.delete(key); else this.openGroups.add(key);
                    localStorage.setItem(GROUPS_KEY, JSON.stringify(Array.from(this.openGroups)));
                },
                isGroupOpen(key) { return this.openGroups.has(key); },
            navLinkClass(active) {
                return [
                    'group items-center gap-3 rounded-xl px-2 py-2 text-sm ',
                    active && !this.isCollapsed ? 'bg-green-500/20' : 'dark:text-zinc-200 hover:bg-zinc-800',
                    this.isCollapsed ? '' : 'flex'
                ].join(' ');
            },
            navSubLinkClass(active, hasIcon) {
                return [
                    'block rounded-lg px-2 py-1.5 text-sm text-zinc-100',
                    active ? 'bg-green-500/20' : 'hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800',
                    hasIcon ? 'ml-6' : 'ml-11'
                ].join(' ');
            },
        }
    }
</script>