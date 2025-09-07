<footer class="bg-gray-900 text-white pt-12 pb-6">
  <div class="container mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
      <div>
        <h3 class="text-xl font-bold mb-4">KFC Ordering</h3>
        <p class="mb-4">Order your favorite KFC meals online and enjoy faster service.</p>
        <div class="flex space-x-4">
          <a href="#" class="text-white hover:text-yellow-400"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="text-white hover:text-yellow-400"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-white hover:text-yellow-400"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
      <div>
        <h4 class="font-bold mb-4">Quick Links</h4>
        <ul class="space-y-2">
          <li><a href="#" class="hover:text-yellow-400">Home</a></li>
          <li><a href="{{ route('menu.index') }}" class="hover:text-yellow-400">Menu</a></li>
          <li><a href="{{ route('kfc.locations') }}" class="hover:text-yellow-400">Locations</a></li>
          <li><a href="{{ route('about') }}" class="hover:text-yellow-400">About Us</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-bold mb-4">Contact</h4>
        <ul class="space-y-2">
          <li class="flex items-start">
            <i class="fas fa-map-marker-alt mt-1 mr-2"></i>
            <span>Tower 1, VSquare @ PJ City Centre, Jalan Utara, 46200 Petaling Jaya, Selangor Darul Ehsan</span>
          </li>
          <li class="flex items-center">
            <i class="fas fa-phone-alt mr-2"></i>
            <span>+603-7948 7188.</span>
          </li>
          <li class="flex items-center">
            <i class="fas fa-envelope mr-2"></i>
            <span>orders@kfc.com</span>
          </li>
        </ul>
      </div>
    </div>
    <div class="border-t border-gray-800 pt-6 text-center text-gray-400">
      <p>&copy; {{ now()->year }} KFC Ordering System. All rights reserved.</p>
    </div>
  </div>
</footer>
