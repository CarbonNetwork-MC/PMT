@extends('layouts.guest')

@section('title', __('titles.auth.login'))

@section('content')
    <div class="auth-background hidden lg:block">
        <div class="grid grid-cols-3 xl:grid-cols-5!">
            <div class="col-span-1 bg-carbon-950 h-dvh flex flex-col">
                {{-- Branding --}}
                <div class="flex justify-center items-center">
                    <x-application-logo class="w-32 h-32 text-white" />
                </div>

                {{-- Login form --}}
                <div class="mt-20 flex justify-center">
                    <div class="text-3xl font-rw-black text-white uppercase">{{ __('auth.form.login') }}</div>
                </div>
                <div class="mt-12 px-4">
                    <x-validation-errors class="mb-4" />

                    @session('status')
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ $value }}
                        </div>
                    @endsession
                
                    <form class="max-w-sm mx-auto" method="POST" action="{{ route('login.post') }}">
                        @csrf
                        <div class="mb-5">
                            <label for="email" class="block mb-2 text-sm font-rw-semibold text-white">{{ __('auth.form.email') }}</label>
                            <input type="email" id="email" name="email" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:border-gray-600 dark:placeholder-gray-700 dark:focus:ring-green-500 dark:focus:border-green-500" placeholder="you@email.com" required autofocus />
                        </div>
                        <div class="mb-5">
                            <label for="password" class="block mb-2 text-sm font-rw-semibold text-white">{{ __('auth.form.password') }}</label>
                            <input type="password" id="password" name="password" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:border-gray-600 dark:placeholder-gray-700 dark:focus:ring-green-500 dark:focus:border-green-500" placeholder="password" required autofocus />
                        </div>
                        <div class="flex items-start mb-5">
                            <div class="flex items-center h-5">
                            <input id="remember" type="checkbox" name="remember" value="" class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-green-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-green-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800" />
                            </div>
                            <label for="remember" class="ms-2 text-sm font-rw-semibold text-white">{{ __('auth.form.remember_me') }}</label>
                        </div>
                        @if (config('app.allow_registration'))
                            <div class="font-rw-semibold text-white text-sm mb-5">
                                {{ __('auth.form.no_account') }}
                                <a href="{{ route('register') }}" class="text-green-500 hover:underline">{{ __('auth.form.register') }}</a>.
                            </div>
                        @endif
                        <div class="flex items-center justify-end gap-x-4 mt-4">
                            @if (Route::has('password.request'))
                                <a class="underline text-sm text-white hover:text-gray-900 dark:hover:text-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" href="{{ route('password.request') }}">
                                    {{ __('auth.form.forgot_password') }}
                                </a>
                            @endif

                            <button type="submit" class="font-rw-semibold text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">{{ __('auth.form.login') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-background lg:hidden min-h-dvh flex items-center justify-center p-6">
        <div class="w-full max-w-md rounded-2xl bg-carbon-950/75 backdrop-blur-md p-8 shadow-2xl border border-white/10">

            {{-- Branding --}}
            <div class="flex justify-center mb-8">
                <x-application-logo class="w-24 h-24 text-white" />
            </div>

            <h1 class="text-center text-3xl font-rw-black uppercase text-white mb-8">
                {{ __('auth.form.login') }}
            </h1>

            <x-validation-errors class="mb-4" />

            @session('status')
                <div class="mb-4 text-sm font-medium text-green-400">
                    {{ $value }}
                </div>
            @endsession

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="mb-5">
                    <label for="mobile-email"
                        class="block mb-2 text-sm font-rw-semibold text-white">
                        {{ __('auth.form.email') }}
                    </label>

                    <input
                        id="mobile-email"
                        name="email"
                        type="email"
                        placeholder="you@email.com"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-gray-100 p-2.5 text-sm focus:border-green-500 focus:ring-green-500">
                </div>

                <div class="mb-5">
                    <label for="mobile-password"
                        class="block mb-2 text-sm font-rw-semibold text-white">
                        {{ __('auth.form.password') }}
                    </label>

                    <input
                        id="mobile-password"
                        name="password"
                        type="password"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-gray-100 p-2.5 text-sm focus:border-green-500 focus:ring-green-500">
                </div>

                <div class="flex items-center mb-6">
                    <input
                        id="mobile-remember"
                        name="remember"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300">

                    <label for="mobile-remember"
                        class="ml-2 text-sm text-white">
                        {{ __('auth.form.remember_me') }}
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-green-700 py-3 font-rw-semibold text-white hover:bg-green-800">
                    {{ __('auth.form.login') }}
                </button>

                @if (Route::has('password.request'))
                    <div class="mt-4 text-center">
                        <a
                            href="{{ route('password.request') }}"
                            class="text-sm text-white/80 underline">
                            {{ __('auth.form.forgot_password') }}
                        </a>
                    </div>
                @endif

                @if (config('app.allow_registration'))
                    <p class="mt-6 text-center text-sm text-white">
                        {{ __('auth.form.no_account') }}
                        <a
                            href="{{ route('register') }}"
                            class="text-green-400 hover:underline">
                            {{ __('auth.form.register') }}
                        </a>
                    </p>
                @endif
            </form>
        </div>
    </div>
@endsection