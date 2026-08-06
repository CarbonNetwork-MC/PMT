<div
    x-data="menuState()"
    x-init="init()"
    @keydown.window.escape="closeMenu()"
    class="lg:hidden"
>
    {{-- Mobile top bar --}}
    <header
        class="sticky top-0 z-30 border-b border-zinc-200 bg-white/90 backdrop-blur
               dark:border-zinc-800 dark:bg-zinc-900/90"
    >
        <div class="flex h-16 items-center justify-between px-4">
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    @click="openMenu()"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl
                           border border-zinc-200 text-zinc-700 transition
                           hover:bg-zinc-100 focus:outline-none focus:ring-2
                           focus:ring-green-500 focus:ring-offset-2
                           dark:border-zinc-700 dark:text-zinc-200
                           dark:hover:bg-zinc-800 dark:focus:ring-offset-zinc-900"
                    aria-label="Open menu"
                    :aria-expanded="isOpen.toString()"
                    aria-controls="mobile-navigation"
                >
                    <i class="fi fi-rr-menu-burger flex items-center"></i>
                </button>

                <a
                    href="{{ route('dashboard.render') }}"
                    class="text-lg font-rw-black text-zinc-900 dark:text-white"
                >
                    PMT
                </a>
            </div>

            <a
                href="{{ route('profile.overview.render') }}"
                class="rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500"
                aria-label="Open profile"
            >
                <img
                    src="{{ $userProfilePicture
                        ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name ?? 'U') . '&background=16a34a&color=ffffff' }}"
                    alt="{{ Auth::user()->name ?? 'User' }}"
                    class="h-10 w-10 rounded-xl object-cover"
                >
            </a>
        </div>
    </header>

    {{-- Mobile menu --}}
    <div
        x-show="isOpen"
        x-cloak
        class="fixed inset-0 z-50"
        aria-modal="true"
        role="dialog"
        aria-label="Mobile navigation"
    >
        {{-- Backdrop --}}
        <div
            x-show="isOpen"
            x-transition:enter="transition-opacity duration-200 ease-out"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-150 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="closeMenu()"
            class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            aria-hidden="true"
        ></div>

        {{-- Drawer --}}
        <aside
            id="mobile-navigation"
            x-show="isOpen"
            x-transition:enter="transition-transform duration-300 ease-out"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition-transform duration-200 ease-in"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            @click.outside="closeMenu()"
            class="fixed inset-y-0 left-0 grid h-dvh w-[85%] max-w-sm
                grid-rows-[auto_minmax(0,1fr)_auto] overflow-hidden
                border-r border-zinc-800 bg-zinc-950 shadow-2xl"
        >
            {{-- Drawer header --}}
            <div class="flex h-16 items-center justify-between border-b border-zinc-800 px-4">
                <a
                    href="{{ route('dashboard.render') }}"
                    class="flex items-center gap-3"
                >
                    <x-application-logo class="h-9 w-9 text-white" />

                    <span class="text-lg font-rw-black text-white">
                        PMT
                    </span>
                </a>

                <button
                    type="button"
                    @click="closeMenu()"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl
                           text-zinc-300 transition hover:bg-zinc-800 hover:text-white
                           focus:outline-none focus:ring-2 focus:ring-green-500"
                    aria-label="Close menu"
                >
                    <i class="fi fi-rr-cross-small flex items-center text-xl"></i>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="space-y-2 overflow-y-auto overscroll-contain p-4">
                <x-sidebar.nav-item
                    mobile
                    :href="route('dashboard.render')"
                    :active="request()->routeIs('dashboard.render')"
                    icon="fi fi-rr-home"
                    :label="__('sidebar.dashboard')"
                />

                <x-sidebar.nav-item
                    mobile
                    :href="route('projects.render')"
                    :active="request()->routeIs('projects.*')"
                    icon="fi fi-sr-department-structure"
                    :label="__('sidebar.projects.title')"
                />

                @if ($selectedProject)
                    <x-sidebar.nav-divider margin="mt-4 mb-2" />

                    <div class="pt-3">
                        <p class="mb-2 px-2 text-xs font-rw-semibold uppercase tracking-wider text-zinc-500">
                            {{ $selectedProject->name }}
                        </p>

                        <div class="space-y-2">
                            {{-- Dashboard --}}
                            <x-sidebar.nav-item
                                mobile
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
                                        mobile
                                        :href="route('projects.board.render', ['uuid' => $selectedProject->uuid, 'sprintUuid' => $activeSprint->uuid])"
                                        :active="request()->routeIs('projects.board.*') && request()->route('sprintUuid') === $activeSprint->uuid"
                                        icon="fi fi-sr-game-board-alt"
                                        :label="__('sidebar.projects.board')"
                                    />
                                @else
                                    <x-sidebar.nav-item
                                        mobile
                                        :href="route('projects.sprints.render', ['uuid' => $selectedProject->uuid])"
                                        :active="request()->routeIs('projects.board.*')"
                                        icon="fi fi-sr-game-board-alt"
                                        :label="__('sidebar.projects.board')"
                                    />
                                @endif
                            @else
                                <x-sidebar.nav-group mobile groupKey="boards" wire:key="boards" label="{{ __('sidebar.projects.board') }}" icon="sr-game-board-alt">
                                    @foreach ($selectedProject->sprints->where('status', 'active') as $sprint)
                                        <x-sidebar.nav-group-item
                                            mobile
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

                            {{-- Sprints --}}
                            <x-sidebar.nav-item
                                mobile
                                :href="route('projects.sprints.render', ['uuid' => $selectedProject->uuid])"
                                :active="request()->routeIs('projects.sprints.*')"
                                icon="fi fi-br-running"
                                :label="__('sidebar.projects.sprints')"
                            />

                            {{-- Backlog --}}
                            <x-sidebar.nav-item
                                mobile
                                :href="route('projects.backlog.render', ['uuid' => $selectedProject->uuid])"
                                :active="request()->routeIs('projects.backlog.*')"
                                icon="fi fi-br-cubes-stacked"
                                :label="__('sidebar.projects.backlog')"
                            />

                            {{-- Sprint Archive --}}
                            <x-sidebar.nav-item
                                mobile
                                :href="route('projects.archive.render', ['uuid' => $selectedProject->uuid])"
                                :active="request()->routeIs('projects.archive.*')"
                                icon="fi fi-br-archive"
                                :label="__('sidebar.projects.archive')"
                            />

                            {{-- Settings --}}
                            <x-sidebar.nav-item
                                mobile
                                :href="route('projects.settings.general.render', ['uuid' => $selectedProject->uuid])"
                                :active="request()->routeIs('projects.settings.*')"
                                icon="fi fi-br-settings-sliders"
                                :label="__('sidebar.projects.settings')"
                            />
                        </div>
                    </div>
                @endif
            </nav>

            {{-- User footer --}}
            <div class="border-t border-zinc-800 p-4">
                <div class="mb-3 flex items-center gap-3 rounded-xl bg-zinc-900 p-3">
                    <img
                        src="{{ $userProfilePicture
                            ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name ?? 'U') . '&background=16a34a&color=ffffff' }}"
                        alt="{{ Auth::user()->name ?? 'User' }}"
                        class="h-10 w-10 rounded-xl object-cover"
                    >

                    <div class="min-w-0">
                        <p class="truncate text-sm font-rw-semibold text-white">
                            {{ Auth::user()->name }}
                        </p>

                        <p class="truncate text-xs text-zinc-400">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                </div>

                <a
                    href="{{ route('profile.overview.render') }}"
                    class="flex items-center justify-center gap-2 rounded-xl
                            bg-zinc-800 px-3 py-2.5 text-sm text-zinc-200
                            transition hover:bg-zinc-700 mb-3"
                >
                    <i class="fi fi-rr-user flex items-center"></i>
                    <span>{{ __('sidebar.profile_settings') }}</span>
                </a>

                <div class="grid grid-cols-2 gap-2">
                    <a
                        href="{{ route('bug-report.render') }}"
                        class="flex items-center justify-center gap-2 rounded-xl
                               bg-zinc-800 px-3 py-2.5 text-sm text-zinc-200
                               transition hover:bg-zinc-700"
                    >
                        <i class="fi fi-rr-bug flex items-center"></i>
                        <span>{{ __('sidebar.bug_report') }}</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl
                                   bg-red-500/10 px-3 py-2.5 text-sm text-red-400
                                   transition hover:bg-red-500/20 hover:text-red-300"
                        >
                            <i class="fi fi-rr-exit flex items-center"></i>
                            <span>{{ __('sidebar.logout') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
    function menuState() {
        return {
            isOpen: false,
            openGroups: new Set(),

            init() {
                const savedGroups = JSON.parse(
                    localStorage.getItem('sidebar:openGroups') || '[]'
                );

                this.openGroups = new Set(savedGroups);

                this.$watch('isOpen', (open) => {
                    document.body.classList.toggle('overflow-hidden', open);
                });
            },

            toggleGroup(key) {
                if (this.openGroups.has(key)) {
                    this.openGroups.delete(key);
                } else {
                    this.openGroups.add(key);
                }

                localStorage.setItem(
                    'sidebar:openGroups',
                    JSON.stringify(Array.from(this.openGroups))
                );
            },

            isGroupOpen(key) {
                return this.openGroups.has(key);
            },

            openMenu() {
                this.isOpen = true;
            },

            closeMenu() {
                this.isOpen = false;
            },
        };
    }
</script>