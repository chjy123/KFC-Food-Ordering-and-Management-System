@include('Partials.header')
<!--Author's Name: Pang Jun Meng-->
<section class="py-16 bg-gray-50">
  <div class="container mx-auto px-4">
    <div class="bg-white rounded-2xl shadow p-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">My Payments (Full History)</h1>
        <a href="{{ route('dashboard') }}" class="text-sm text-red-600 hover:underline">Back to Dashboard</a>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border">
          <thead>
            <tr class="bg-gray-100">
              <th class="p-2">Payment ID</th>
              <th class="p-2">Method</th>
              <th class="p-2">Status</th>
              <th class="p-2">Date/Time</th>
              <th class="p-2">Amount (RM)</th>
            </tr>
          </thead>
          <tbody>
            @forelse($payments as $p)
              <tr>
                <td class="p-2">{{ $p->payment_id }}</td>
                <td class="p-2 capitalize">{{ $p->payment_method }}</td>
                <td class="p-2 capitalize">{{ $p->payment_status }}</td>
                <td class="p-2">
                  {{ \Carbon\Carbon::parse($p->payment_date)->timezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i') }}
                </td>
                <td class="p-2">RM {{ number_format($p->amount, 2) }}</td>
              </tr>
            @empty
              <tr><td class="p-4 text-center text-gray-600" colspan="5">No payments yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-6">
        {{ $payments->links() }} {{-- Laravel’s Tailwind pagination --}}
      </div>
    </div>
  </div>
</section>

@include('Partials.footer')
