@props(['session'])

@php
    $agent = new \Jenssegers\Agent\Agent();
    $agent->setUserAgent($session->user_agent);

    $icon = '';
    if ($agent->isDesktop()) {
        $icon = 'fi-rr-computer';
    } elseif ($agent->isTablet()) {
        $icon = 'fi-rr-tablet';
    } elseif ($agent->isPhone()) {
        $icon = 'fi-rr-mobile-notch';
    } else {
        $icon = 'fi-rr-interrogation';
    }
@endphp

<div class="flex items-center gap-2">
    <i class="fi {{ $icon }} text-2xl text-gray-500 dark:text-gray-400"></i>

    <div class="flex flex-col">
        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
            {{ $agent->platform() }} - {{ $agent->browser() }}
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ $session->ip_address }},

            @if ($session->id === session()->id())
                <span class="text-xs font-semibold text-green-600 dark:text-green-400">
                    {{ __('profile.labels.this_device') }}
                </span>
            @else
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                    {{ __('profile.labels.last_active') }}
                    {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                </span>
            @endif
        </div>
    </div>
</div>