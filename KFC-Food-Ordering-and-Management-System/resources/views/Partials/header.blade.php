<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/kfc.css">
    <title>KFC - Food Ordering System</title>

    {{-- Tailwind & Font Awesome --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body class="font-sans bg-gray-50">

{{-- Navigation --}}
<nav class="bg-red-600 text-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        
        {{-- Logo / Brand --}}
        <div class="flex items-center space-x-2">
            <i class="fas fa-utensils text-2xl"></i>
            <a href="{{ url('/') }}" class="text-xl font-bold">KFC Ordering</a>
        </div>
        
        {{-- Menu links (desktop) --}}
        <div class="hidden md:flex space-x-6">
            <a href="{{ url('/') }}" class="hover:text-yellow-300 font-medium">Home</a>
            <a href="{{ route('menu.index') }}" class="hover:text-yellow-300">Menu</a>
            <a href="{{ route('kfc.locations') }}" class="hover:text-yellow-300">Locations</a>
            <a href="{{ route('about') }}" class="hover:text-yellow-300">About</a>
        </div>
        
        {{-- Right side: cart + auth --}}
        <div class="flex items-center space-x-4">
            
            {{-- Cart --}}
<div class="relative">
    <a href="{{ route('cart.index') }}" class="p-2 rounded-full hover:bg-red-700 relative">
        <i class="fas fa-shopping-cart text-xl"></i>
        @php
    $cartCount = \App\Models\Cart::where('user_id', auth()->id())
                ->withSum('items', 'quantity')
                ->first()->items_sum_quantity ?? 0;
@endphp
@if($cartCount > 0)
    <span class="absolute -top-1 -right-1 bg-yellow-400 text-red-800 text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
        {{ $cartCount }}
    </span>
@endif
    </a>
</div>


            {{-- Auth aware buttons --}}
            @auth
                {{-- If logged in --}}
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button class="bg-red-600 text-white px-4 py-2 rounded-full font-bold hover:bg-red-700">
                        Logout
                    </button>
                </form>
                <a href="{{ route('dashboard') }}" class="hidden md:inline-block bg-white text-red-600 px-4 py-2 rounded-full font-bold hover:bg-gray-100">
                    Profile
                </a>
            @else
                {{-- If guest --}}
                <a href="{{ route('login.show') }}" class="bg-yellow-400 text-red-800 px-4 py-2 rounded-full font-bold hover:bg-yellow-300">
                    Sign In
                </a>
                <a href="{{ route('register.show') }}" class="bg-white text-red-600 px-4 py-2 rounded-full font-bold hover:bg-gray-100">
                    Register
                </a>
            @endauth

            {{-- Mobile menu toggle (hamburger) --}}
            <button class="md:hidden">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
    </div>
</nav>
