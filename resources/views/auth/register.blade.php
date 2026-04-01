@extends('layouts.guest')

@section('content')
    <div class="auth-background hidden lg:block">
        <div class="grid grid-cols-3 xl:grid-cols-5!">
            <div class="col-span-1 bg-carbon-950 h-dvh flex flex-col">
                {{-- Branding --}}
                <div class="flex justify-center items-center">
                    <x-application-logo class="w-32 h-32 text-white" />
                </div>

                {{-- Register form --}}
                <div class="mt-20 flex justify-center">
                    <div class="text-3xl font-rw-black text-white uppercase">{{ __('auth.form.register') }}</div>
                </div>
                <div class="mt-12 px-4">
                    <x-validation-errors class="mb-4" />

                    @session('status')
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ $value }}
                        </div>
                    @endsession

                    <form class="max-w-sm mx-auto" method="POST" action="{{ route('register.post') }}">
                        @csrf
                        <div class="mb-5">
                            <label for="name" class="block mb-2 text-sm font-rw-semibold text-white">{{ __('auth.form.name') }}</label>
                            <input type="text" id="name" name="name" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:border-gray-600 dark:placeholder-gray-700 dark:focus:ring-green-500 dark:focus:border-green-500" placeholder="Your Name" required autofocus />
                        </div>
                        <div class="mb-5">
                            <label for="email" class="block mb-2 text-sm font-rw-semibold text-white">{{ __('auth.form.email') }}</label>
                            <input type="email" id="email" name="email" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:border-gray-600 dark:placeholder-gray-700 dark:focus:ring-green-500 dark:focus:border-green-500" placeholder="You@example.com" required />
                        </div>
                        <div class="mb-5">
                            <label for="password" class="block mb-2 text-sm font-rw-semibold text-white">{{ __('auth.form.password') }}</label>
                            <input type="password" id="password" name="password" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:border-gray-600 dark:placeholder-gray-700 dark:focus:ring-green-500 dark:focus:border-green-500" placeholder="Password" required />
                        </div>
                        <div class="mb-5">
                            <label for="password_confirmation" class="block mb-2 text-sm font-rw-semibold text-white">{{ __('auth.form.confirm_password') }}</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:border-gray-600 dark:placeholder-gray-700 dark:focus:ring-green-500 dark:focus:border-green-500" placeholder="{{ __('auth.form.confirm_password_placeholder') }}" required />
                        </div>
                        <div class="font-rw-semibold text-white text-sm mb-5">
                            {{ __('auth.form.already_registered') }}
                            <a href="{{ route('login') }}" class="text-green-500 hover:underline">{{ __('auth.form.login') }}</a>.
                        </div>
                        <div class="flex items-center justify-end gap-x-4 mt-4">
                            <button type="submit" class="font-rw-semibold text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">{{ __('auth.form.register') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection