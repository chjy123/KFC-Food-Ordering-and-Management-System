<?php
#Author’s name： Pang Jun Meng
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\PaymentService;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class PaymentWebController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    // Show checkout form for an order
    public function showCheckout(int $orderId)
    {
        // You may fetch order details via OrderClient (or pass the order summary from session)
        // Minimal example: render form with order id and example amount (replace with real order fetch)
        $order = null;
        if (class_exists(\App\Services\OrderClient::class)) {
            $orderClient = app(\App\Services\OrderClient::class);
            $order = $orderClient->getOrder($orderId);
        }

        // fallback: require that caller passes amount param through query if order not found
        $amount = $order['totalAmount'] ?? request()->query('amount', 0);

        return view('payments.checkout', [
            'orderId' => $orderId,
            'amount' => $amount,
            'user' => Auth::user()
        ]);
    }

    // Handle checkout form submit (server-side). Generates idempotency key and calls PaymentService.
    public function processCheckout(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|integer',
            'amount' => 'required|numeric',
            'method' => 'required|string',
            'payment_payload' => 'nullable|array'
        ]);

        $user = Auth::user();
        $input = [
            'order_id' => $data['order_id'],
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'method' => $data['method'],
            'payment_payload' => $data['payment_payload'] ?? null,
            'currency' => $request->input('currency', 'MYR')
        ];

        // create an idempotency key (server-side)
        $idempotencyKey = (string) Str::uuid();

        $result = $this->paymentService->processPayment($input, $idempotencyKey);

        if ($result['success']) {
            $payment = $result['payment'];
            return redirect()->route('payments.success', ['id' => $payment->id]);
        }

        // redirect to failed page with message
        return redirect()->route('payments.failed')->with('error', $result['message'] ?? 'Payment failed');
    }

    // Success page
    public function success($id)
    {
        $payment = Payment::with(['order','user'])->findOrFail($id);
        return view('payments.result_success', ['payment' => $payment]);
    }

    // Failed page
    public function failed(Request $request)
    {
        return view('payments.result_failed', ['message' => session('error', 'Payment failed')]);
    }

    // Customer payment history
    public function history()
    {
        $user = Auth::user();
        $payments = Payment::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        return view('payments.history', ['payments' => $payments]);
    }

    // Admin payment history
    public function adminHistory()
    {
        // gate/middleware ensures admin only
        $payments = Payment::with(['user','order'])->orderBy('created_at','desc')->paginate(30);
        return view('payments.admin_history', ['payments' => $payments]);
    }
/*
    // Show refund form for a payment
    public function showRefundForm($id)
    {
        $payment = Payment::findOrFail($id);
        return view('payments.refund_form', ['payment' => $payment]);
    }

    // Process refund (admin)
    public function postRefund(Request $request, $id)
    {
        // verify admin permission via middleware/gate
        $res = $this->paymentService->refundPayment((int)$id);
        if ($res['success']) {
            return redirect()->route('admin.payments')->with('success', $res['message'] ?? 'Refunded');
        }
        return redirect()->back()->with('error', $res['message'] ?? 'Refund failed');
    }
*/
}
