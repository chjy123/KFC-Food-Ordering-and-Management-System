@include('partials.header')
<!-- Author's Name: Pang Jun Meng -->
<div class="container mx-auto py-16 text-center">
    @if($success)
        <h1 class="text-3xl font-bold text-green-600 mb-4">Payment Successful</h1>
        <p class="mb-8">Thank you! Your payment for Order #{{ $orderId }} has been processed.</p>
    @else
        <h1 class="text-3xl font-bold text-yellow-600 mb-4">Payment Pending</h1>
        <p class="mb-8">We could not confirm your payment. You may try again.</p>
    @endif

    <div class="space-x-3">
        <a href="{{ $homeUrl }}" class="px-6 py-2 bg-gray-200 rounded hover:bg-gray-300">Main Page</a>
        <a href="{{ $retryUrl }}" class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700">Proceed with payments again</a>
    </div>
</div>

@include('partials.footer')
