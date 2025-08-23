<body class="font-sans bg-gray-50">

{{-- Navigation --}}
<nav class="bg-red-600 text-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <i class="fas fa-utensils text-2xl"></i>
            <a href="{{ url('/') }}" class="text-xl font-bold">KFC Ordering</a>
        </div>
        
        <div class="hidden md:flex space-x-6">
            <a href="{{ url('/') }}" class="hover:text-yellow-300 font-medium">Home</a>
            <a href="#" class="hover:text-yellow-300">Menu</a>
            <a href="#" class="hover:text-yellow-300">Deals</a>
            <a href="#" class="hover:text-yellow-300">Locations</a>
            <a href="#" class="hover:text-yellow-300">About</a>
        </div>
        
        <div class="flex items-center space-x-4">
            <div class="relative">
                <button class="p-2 rounded-full hover:bg-red-700">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span class="cart-badge bg-yellow-400 text-red-800 text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center" data-cart-count>3</span>
                </button>
            </div>
            <a href="#" class="bg-yellow-400 text-red-800 px-4 py-2 rounded-full font-bold hover:bg-yellow-300">Sign In</a>
            <button class="md:hidden">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
    </div>
</nav>