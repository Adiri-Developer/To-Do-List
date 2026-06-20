@extends('layouts.auth')

@section('title', 'Login - ' . config('app.name'))

@section('content')
    <div class="w-full max-w-md p-8 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">{{ config('app.name') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Welcome back! Please login to your account.</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-lg mb-6 text-sm">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border transition-colors">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                <input type="password" name="password" id="password" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border transition-colors">
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600 transition-colors">
                    <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Remember me
                    </label>
                </div>
            </div>

            <button type="submit" :disabled="loading" class="cursor-pointer w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-75 disabled:cursor-not-allowed">
                <span x-text="loading ? 'Loading...' : 'Sign In'">Sign In</span>
            </button>
        </form>

        @if(config('services.telegram.bot_username'))
            <div class="mt-6">
                <div class="relative flex items-center justify-center my-4">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                    </div>
                    <div class="relative bg-white dark:bg-gray-800 px-4 text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Or continue with</span>
                    </div>
                </div>

                <div class="flex justify-center mt-4">
                    <script async src="https://telegram.org/js/telegram-widget.js?22" 
                        data-telegram-login="{{ config('services.telegram.bot_username') }}" 
                        data-size="large" 
                        data-auth-url="{{ route('telegram.callback') }}" 
                        data-request-access="write">
                    </script>
                </div>
            </div>
        @endif

        <p class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
            Don't have an account? 
            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">Sign up</a>
        </p>
    </div>
@endsection
