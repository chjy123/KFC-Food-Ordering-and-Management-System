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

<section class="py-20 bg-gray-50">
  <div class="container mx-auto px-4">
    <div class="max-w-lg mx-auto bg-white rounded-lg shadow-lg p-8">
      <h2 class="text-3xl font-bold text-center text-red-600 mb-6">Sign In</h2>

      @if (session('status'))
        <div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-50 text-red-700">
          <ul class="list-disc list-inside">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('login.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
          <label class="block text-gray-700 font-medium mb-2">Email Address</label>
          <input type="email" name="email" value="{{ old('email') }}" required
                 class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500">
        </div>

        <div>
          <label class="block text-gray-700 font-medium mb-2">Password</label>
          <input type="password" name="password" required
                 class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500">
        </div>

        <div class="flex items-center justify-between">
          <label class="inline-flex items-center gap-2 text-gray-700">
            <input type="checkbox" name="remember" class="rounded border-gray-300">
            <span>Remember me</span>
          </label>
          <a href="#" class="text-red-600 hover:underline text-sm">Forgot password?</a>
        </div>

        <button type="submit"
                class="w-full bg-red-600 text-white py-3 rounded-lg font-bold hover:bg-red-700 transition">
          Sign In
        </button>
      </form>

      <p class="text-center text-gray-600 mt-6">
        Don’t have an account?
        <a href="{{ route('register.show') }}" class="text-red-600 font-bold hover:underline">Create one</a>
      </p>
    </div>
  </div>
</section>

@include('partials.footer')

</body>
</html>
