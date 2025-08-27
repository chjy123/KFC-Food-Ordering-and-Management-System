@include('Partials.header')

<section class="py-14 bg-gray-50">
  <div class="container mx-auto px-4">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      {{-- Left: User summary --}}
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">My Profile</h2>
        <div class="space-y-2 text-gray-700">
          <div><span class="font-semibold">Name:</span> {{ auth()->user()->name }}</div>
          <div><span class="font-semibold">Email:</span> {{ auth()->user()->email }}</div>
          <div><span class="font-semibold">Phone:</span> {{ auth()->user()->phoneNo ?? '-' }}</div>
          <div><span class="font-semibold">Role:</span>
            <span class="px-2 py-1 rounded-full text-xs
              {{ auth()->user()->isAdmin() ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
              {{ auth()->user()->role }}
            </span>
          </div>
        </div>
      </div>

      {{-- Middle: Edit profile --}}
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold mb-4 text-gray-800">Edit Profile</h3>

        @if (session('profile_status'))
          <div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('profile_status') }}</div>
        @endif

        @if ($errors->any() && !$errors->updatePassword)
          <div class="mb-4 p-3 rounded bg-red-50 text-red-700">
            <ul class="list-disc list-inside">
              @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('dashboard.update') }}" method="POST" class="space-y-5">
          @csrf
          @method('PUT')

          <div>
            <label class="block text-gray-700 font-medium mb-2">Full Name</label>
            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500" required>
          </div>

          {{-- If you later allow changing email, add that field here with unique rule in controller --}}
          <div>
            <label class="block text-gray-700 font-medium mb-2">Phone</label>
            <input type="text" name="phoneNo" value="{{ old('phoneNo', auth()->user()->phoneNo) }}"
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500">
          </div>

          <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-lg font-bold hover:bg-red-700 transition">
            Save Changes
          </button>
        </form>
      </div>

      {{-- Right: Change password --}}
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold mb-4 text-gray-800">Change Password</h3>

        @if (session('password_status'))
          <div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('password_status') }}</div>
        @endif

        @if ($errors->updatePassword ?? false)
          <div class="mb-4 p-3 rounded bg-red-50 text-red-700">
            <ul class="list-disc list-inside">
              @foreach (($errors->updatePassword)->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('dashboard.password') }}" method="POST" class="space-y-4">
          @csrf
          @method('PUT')

          <div>
            <label class="block text-gray-700 font-medium mb-2">Current Password</label>
            <input type="password" name="current_password" required
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500">
          </div>

          <div>
            <label class="block text-gray-700 font-medium mb-2">New Password</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500">
          </div>

          <div>
            <label class="block text-gray-700 font-medium mb-2">Confirm New Password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500">
          </div>

          <button type="submit" class="w-full bg-gray-800 text-white py-3 rounded-lg font-bold hover:bg-black transition">
            Update Password
          </button>
        </form>
      </div>

    </div>
  </div>
</section>

@include('Partials.footer')
