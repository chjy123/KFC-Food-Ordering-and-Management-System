<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/kfc.css">
    <title>KFC - Food Ordering System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
@include('partials.header')
{{-- Hero Section --}}
<section class="hero-bg text-white py-20 md:py-32" style="background-image: url('https://www.therakyatpost.com/wp-content/uploads/2022/02/kfc-2.jpg');">
    <div class="container mx-auto px-4 text-center backdrop-brightness-50 py-10">
        <h1 class="text-4xl md:text-6xl font-bold mb-4">Finger Lickin' Good</h1>
        <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto">Order your favorite KFC meals online and skip the queue!</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('menu.index') }}" class="bg-yellow-400 text-red-800 px-8 py-3 rounded-full font-bold text-lg hover:bg-yellow-300 transition">Order Now</a>
        </div>
    </div>
</section>

   <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Why Order With Us?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 rounded-lg">
                    <div class="bg-red-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-bolt text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Fast Service</h3>
                    <p class="text-gray-600">Skip the queue and get your food faster with our online ordering system.</p>
                </div>
                <div class="text-center p-6 rounded-lg">
                    <div class="bg-red-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-percent text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Exclusive Deals</h3>
                    <p class="text-gray-600">Get access to online-only promotions and combo deals.</p>
                </div>
                <div class="text-center p-6 rounded-lg">
                    <div class="bg-red-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-history text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Order History</h3>
                    <p class="text-gray-600">Easily reorder your favorites from your order history.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Items -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Our Popular Items</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Item 1 -->
                <div class="bg-white rounded-lg overflow-hidden shadow-md menu-card transition duration-300">
                    <img src="https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Fried Chicken" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-bold text-xl mb-2">Original Recipe Chicken</h3>
                        <p class="text-gray-600 mb-4">Our signature 11 herbs and spices recipe.</p>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-lg">$9.99</span>
                            <button class="bg-red-600 text-white px-4 py-2 rounded-full hover:bg-red-700">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <!-- Item 2 -->
                <div class="bg-white rounded-lg overflow-hidden shadow-md menu-card transition duration-300">
                    <img src="https://images.unsplash.com/photo-1603360946369-dc9bb6258143?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Zinger Burger" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-bold text-xl mb-2">Zinger Burger</h3>
                        <p class="text-gray-600 mb-4">Spicy, crunchy chicken fillet in a soft bun.</p>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-lg">$7.49</span>
                            <button class="bg-red-600 text-white px-4 py-2 rounded-full hover:bg-red-700">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <!-- Item 3 -->
                <div class="bg-white rounded-lg overflow-hidden shadow-md menu-card transition duration-300">
                    <img src="https://images.unsplash.com/photo-1605497788044-5a32c7078486?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80" alt="Fries" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-bold text-xl mb-2">Crispy Fries</h3>
                        <p class="text-gray-600 mb-4">Golden, crispy fries with just the right amount of salt.</p>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-lg">$3.99</span>
                            <button class="bg-red-600 text-white px-4 py-2 rounded-full hover:bg-red-700">Add to Cart</button>
                        </div>
                    </div>
                </div>
                
                <!-- Item 4 -->
                <div class="bg-white rounded-lg overflow-hidden shadow-md menu-card transition duration-300">
                    <img src="https://images.unsplash.com/photo-1561758033-48d52648ae8b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80" alt="Coleslaw" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-bold text-xl mb-2">Creamy Coleslaw</h3>
                        <p class="text-gray-600 mb-4">Fresh cabbage and carrots in our signature dressing.</p>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-lg">$2.99</span>
                            <button class="bg-red-600 text-white px-4 py-2 rounded-full hover:bg-red-700">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-10">
                <a href="{{ route('menu.index') }}" class="inline-block bg-red-600 text-white px-8 py-3 rounded-full font-bold hover:bg-red-700 transition">View Full Menu</a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">How It Works</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6">
                    <div class="bg-red-600 text-white w-12 h-12 mx-auto rounded-full flex items-center justify-center mb-4 text-xl font-bold">1</div>
                    <h3 class="text-xl font-bold mb-2">Browse Menu</h3>
                    <p class="text-gray-600">Select from our delicious menu items and add them to your cart.</p>
                </div>
                <div class="text-center p-6">
                    <div class="bg-red-600 text-white w-12 h-12 mx-auto rounded-full flex items-center justify-center mb-4 text-xl font-bold">2</div>
                    <h3 class="text-xl font-bold mb-2">Checkout</h3>
                    <p class="text-gray-600">Choose pickup or delivery, then securely pay online.</p>
                </div>
                <div class="text-center p-6">
                    <div class="bg-red-600 text-white w-12 h-12 mx-auto rounded-full flex items-center justify-center mb-4 text-xl font-bold">3</div>
                    <h3 class="text-xl font-bold mb-2">Enjoy Your Meal</h3>
                    <p class="text-gray-600">Pick up your order or get it delivered to your doorstep.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16 bg-red-600 text-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">What Our Customers Say</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white text-gray-800 p-6 rounded-lg shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 mr-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="mb-4">"The online ordering system is so convenient! My order was ready exactly when I arrived."</p>
                    <div class="flex items-center">
                        <img src="https://randomuser.me/api/portraits/women/43.jpg" alt="Customer" class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <h4 class="font-bold">Sarah Johnson</h4>
                            <p class="text-sm text-gray-600">Regular Customer</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white text-gray-800 p-6 rounded-lg shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 mr-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                    <p class="mb-4">"Never had a wrong order since they started this system. The app remembers my favorites too!"</p>
                    <div class="flex items-center">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Customer" class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <h4 class="font-bold">Michael Chen</h4>
                            <p class="text-sm text-gray-600">Food Blogger</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white text-gray-800 p-6 rounded-lg shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 mr-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="mb-4">"The family bucket deal ordered online saved me 15 minutes of waiting in line. Worth it!"</p>
                    <div class="flex items-center">
                        <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Customer" class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <h4 class="font-bold">Emily Rodriguez</h4>
                            <p class="text-sm text-gray-600">Busy Mom</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-yellow-400 text-red-800">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-6">Ready to Order?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">Download our app for exclusive mobile-only deals and faster ordering!</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#" class="bg-red-600 text-white px-8 py-3 rounded-full font-bold hover:bg-red-700 flex items-center justify-center gap-2">
                    <i class="fab fa-apple text-2xl"></i>
                    <span>App Store</span>
                </a>
                <a href="#" class="bg-red-600 text-white px-8 py-3 rounded-full font-bold hover:bg-red-700 flex items-center justify-center gap-2">
                    <i class="fab fa-google-play text-2xl"></i>
                    <span>Google Play</span>
                </a>
            </div>
        </div>
    </section>

@include('partials.footer')

    <!-- Floating Order Button (Mobile) -->
    <div class="md:hidden fixed bottom-6 right-6">
        <button class="floating-btn bg-red-600 text-white p-4 rounded-full shadow-lg hover:bg-red-700">
            <i class="fas fa-shopping-cart text-2xl"></i>
            <span class="absolute -top-1 -right-1 bg-yellow-400 text-red-800 text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center">3</span>
        </button>
    </div>

    <script>
        // Simple cart counter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const addToCartButtons = document.querySelectorAll('button:contains("Add to Cart")');
            const cartBadges = document.querySelectorAll('.cart-badge');
            let cartCount = 3; // Starting with 3 items
            
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    cartCount++;
                    cartBadges.forEach(badge => {
                        badge.textContent = cartCount;
                    });
                    
                    // Animation feedback
                    button.textContent = 'Added!';
                    button.classList.add('bg-green-500');
                    setTimeout(() => {
                        button.textContent = 'Add to Cart';
                        button.classList.remove('bg-green-500');
                    }, 1000);
                });
            });
        });
    </script>
</body>
</html>
