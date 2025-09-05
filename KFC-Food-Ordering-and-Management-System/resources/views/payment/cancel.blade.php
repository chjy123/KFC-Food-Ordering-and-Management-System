@include('partials.header')
<!-- Author's Name: Pang Jun Meng -->
<div class="container mx-auto py-16 text-center">
    <h1 class="text-3xl font-bold text-red-600 mb-4">Payment Failed / Cancelled</h1>
    <p class="mb-8">Your payment attempt was not completed. You can try again.</p>

    <div class="space-x-3">
        <a href="{{ $homeUrl }}" class="px-6 py-2 bg-gray-200 rounded hover:bg-gray-300">Main Page</a>
        <a href="{{ $retryUrl }}" class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700">Proceed with payments again</a>
    </div>
</div>

@include('partials.footer')

