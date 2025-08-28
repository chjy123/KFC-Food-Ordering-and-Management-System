@include('Partials.header')

<section class="py-16 bg-gray-50">
  <div class="container mx-auto px-4">
    <div class="max-w-3xl mx-auto space-y-8">

      {{-- Page title + role --}}
      <div class="bg-white rounded-2xl shadow p-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">My Dashboard</h1>
            <p class="text-gray-600 mt-1">View and update your account details.</p>
          </div>
          <span class="px-3 py-1 rounded-full text-xs font-semibold
            {{ auth()->user()->isAdmin() ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
            {{ ucfirst(auth()->user()->role) }}
          </span>
        </div>
      </div>

      {{-- Flash alerts --}}
      @if (session('profile_status'))
        <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl p-4">
          {{ session('profile_status') }}
        </div>
      @endif
      @if (session('password_status'))
        <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl p-4">
          {{ session('password_status') }}
        </div>
      @endif

      {{-- Profile (view + edit) --}}
      <div class="bg-white rounded-2xl shadow p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Profile Information</h2>

        {{-- Errors for this form (default bag) --}}
        @if ($errors->any() && !($errors->updatePassword->any() ?? false))
          <div class="mb-6 bg-red-50 text-red-700 border border-red-200 rounded-xl p-4">
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

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
              <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                     class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
              <input type="text" name="phoneNo" value="{{ old('phoneNo', auth()->user()->phoneNo) }}"
                     class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
              <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                     class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
          </div>

          <div class="pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700">
              Save Changes
            </button>
          </div>
        </form>
      </div>

      {{-- Change password --}}
      <div class="bg-white rounded-2xl shadow p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Change Password</h2>

        {{-- Errors for password form (named bag) --}}
        @if ($errors->updatePassword->any() ?? false)
          <div class="mb-6 bg-red-50 text-red-700 border border-red-200 rounded-xl p-4">
            <ul class="list-disc list-inside">
              @foreach ($errors->updatePassword->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('dashboard.password') }}" method="POST" class="space-y-5">
          @csrf
          @method('PUT')

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
              <input type="password" name="current_password" required
                     class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
              <input type="password" name="password" required
                     class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
              <input type="password" name="password_confirmation" required
                     class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
          </div>

          <div class="pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gray-900 text-white font-semibold hover:bg-black">
              Update Password
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</section>

@include('Partials.footer')
