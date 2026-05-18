<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    // ==================== MoMo Config ====================
    private string $momoEndpoint  = 'https://test-payment.momo.vn/v2/gateway/api/create';
    private string $partnerCode   = 'MOMOBKUN20180529';
    private string $accessKey     = 'klm05TvNBzhg7h7j';
    private string $secretKey     = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
    private string $requestType   = 'payWithATM';

    // =====================================================

    public function show(int $orderId)
    {
        $order = Order::with('orderDetails')->findOrFail($orderId);

        // Kiểm tra payment đã tồn tại chưa để hiển thị trạng thái
        $existingPayment = Payment::where('order_id', $orderId)->latest()->first();

        return view('clients.page.payment', compact('order', 'existingPayment'));
    }

    public function process(Request $request, int $orderId)
    {
        $request->validate([
            'delivery_method' => 'required|string',
            'payment_method'  => 'required|in:COD,momo',
        ], [
            'delivery_method.required' => 'Vui lòng chọn phương thức giao hàng.',
            'payment_method.required'  => 'Vui lòng chọn phương thức thanh toán.',
        ]);

        $order = Order::findOrFail($orderId);

        // Kiểm tra payment đã tồn tại — tránh tạo trùng
        $existingPayment = Payment::where('order_id', $orderId)
            ->whereIn('payment_status', ['pending', 'completed'])
            ->first();

        if ($existingPayment) {
            if ($request->payment_method === 'momo' && $existingPayment->payment_status === 'pending') {
                // Cho phép thử lại MoMo nếu chưa hoàn tất
                return $this->redirectToMomo($order, $existingPayment->transaction_id);
            }

            return redirect()->route('payment.success', $orderId)
                ->with('info', 'Đơn hàng này đã được thanh toán.');
        }

        if ($request->payment_method === 'COD') {
            return $this->processCod($order);
        }

        return $this->processMomo($order);
    }

    // ==================== COD ====================

    private function processCod(Order $order): \Illuminate\Http\RedirectResponse
    {
        DB::transaction(function () use ($order) {
            Payment::create([
                'order_id'       => $order->id,
                'payment_method' => 'COD',
                'transaction_id' => 'COD-' . strtoupper(uniqid()),
                'payment_date'   => now(),
                'amount'         => $order->total_amount,
                'payment_status' => 'pending',
            ]);

            $order->update([
                'payment_status' => 'unpaid',
                'order_status'   => 'pending',
            ]);
        });

        return redirect()->route('payment.success', $order->id);
    }

    // ==================== MoMo ====================

    private function processMomo(Order $order): mixed
    {
        $amount = (int) $order->total_amount;

        if ($amount < 10_000 || $amount > 50_000_000) {
            return back()->with('error', 'Số tiền thanh toán phải từ 10.000đ đến 50.000.000đ');
        }

        $transactionId = 'MOMO-' . time() . '-' . $order->id;

        DB::transaction(function () use ($order, $transactionId) {
            Payment::create([
                'order_id'       => $order->id,
                'payment_method' => 'momo',
                'transaction_id' => $transactionId,
                'payment_date'   => now(),
                'amount'         => $order->total_amount,
                'payment_status' => 'pending',
            ]);
        });

        return $this->redirectToMomo($order, $transactionId);
    }

    private function redirectToMomo(Order $order, string $transactionId): mixed
    {
        $amount      = (int) $order->total_amount;
        $orderInfo   = 'Thanh toán đơn hàng ' . $order->order_number;
        $redirectUrl = route('payment.momo.callback');
        $ipnUrl      = route('payment.momo.ipn');
        $requestId   = $transactionId;
        $extraData   = '';

        $rawHash = "accessKey={$this->accessKey}"
            . "&amount={$amount}"
            . "&extraData={$extraData}"
            . "&ipnUrl={$ipnUrl}"
            . "&orderId={$transactionId}"
            . "&orderInfo={$orderInfo}"
            . "&partnerCode={$this->partnerCode}"
            . "&redirectUrl={$redirectUrl}"
            . "&requestId={$requestId}"
            . "&requestType={$this->requestType}";

        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        $payload = json_encode([
            'partnerCode' => $this->partnerCode,
            'partnerName' => 'VoxFootball',
            'storeId'     => 'VoxFootballStore',
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $transactionId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $this->requestType,
            'signature'   => $signature,
        ]);

        $response = $this->postRequest($this->momoEndpoint, $payload);
        $result   = json_decode($response, true);

        if (!empty($result['payUrl'])) {
            return redirect()->away($result['payUrl']);
        }

        Log::error('MoMo error', $result ?? []);

        // Xóa payment pending vừa tạo nếu MoMo từ chối
        Payment::where('transaction_id', $transactionId)->delete();

        return back()->with('error', 'Lỗi MoMo: ' . ($result['message'] ?? 'Không kết nối được'));
    }

    /**
     * MoMo redirect về sau khi người dùng thanh toán xong trên app/web MoMo.
     */
    public function momoCallback(Request $request)
    {
        $resultCode    = $request->query('resultCode', -1);
        $transactionId = $request->query('orderId');

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin thanh toán.');
        }

        $order = Order::findOrFail($payment->order_id);

        if ((int) $resultCode === 0) {
            DB::transaction(function () use ($payment, $order, $request) {
                $payment->update([
                    'payment_status' => 'completed',
                    'transaction_id' => $request->query('transId', $payment->transaction_id),
                ]);

                $order->update([
                    'payment_status' => 'paid',
                    'order_status'   => 'confirmed',
                ]);
            });

            return redirect()->route('payment.success', $order->id)
                ->with('success', 'Thanh toán MoMo thành công!');
        }

        // Thanh toán thất bại — đặt lại payment về failed
        $payment->update(['payment_status' => 'failed']);

        return redirect()->route('payment.show', $order->id)
            ->with('error', 'Thanh toán MoMo không thành công: ' . $request->query('message', 'Lỗi không xác định'));
    }

    /**
     * MoMo IPN — server-to-server, xác nhận thanh toán từ phía MoMo.
     * Route này phải exempt CSRF.
     */
    public function momoIpn(Request $request)
    {
        $data = $request->all();

        // Xác thực chữ ký từ MoMo
        $rawHash = "accessKey={$this->accessKey}"
            . "&amount={$data['amount']}"
            . "&extraData={$data['extraData']}"
            . "&message={$data['message']}"
            . "&orderId={$data['orderId']}"
            . "&orderInfo={$data['orderInfo']}"
            . "&orderType={$data['orderType']}"
            . "&partnerCode={$data['partnerCode']}"
            . "&payType={$data['payType']}"
            . "&requestId={$data['requestId']}"
            . "&responseTime={$data['responseTime']}"
            . "&resultCode={$data['resultCode']}"
            . "&transId={$data['transId']}";

        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        if ($signature !== $data['signature']) {
            Log::warning('MoMo IPN: chữ ký không hợp lệ', $data);
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $payment = Payment::where('transaction_id', $data['orderId'])->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        if ((int) $data['resultCode'] === 0 && $payment->payment_status !== 'completed') {
            DB::transaction(function () use ($payment, $data) {
                $payment->update([
                    'payment_status' => 'completed',
                    'transaction_id' => $data['transId'],
                ]);

                Order::where('id', $payment->order_id)->update([
                    'payment_status' => 'paid',
                    'order_status'   => 'confirmed',
                ]);
            });
        }

        return response()->json(['message' => 'OK'], 200);
    }

    // ==================== Các trang còn lại ====================

    public function success(int $orderId)
    {
        $order = Order::with(['orderDetails', 'payments'])->findOrFail($orderId);

        return view('clients.page.payment_success', compact('order'));
    }

    public function detail(int $orderId)
    {
        $order = Order::with([
            'orderDetails.variant.color',
            'orderDetails.variant.size',
            'orderDetails.product.images',
            'payments',
            'statusHistories',
        ])->findOrFail($orderId);

        return view('clients.page.order_detail', compact('order'));
    }

    public function guestDetail(Request $request)
    {
        $sessionOrderId = $request->session()->get('last_order_id');

        if (!$sessionOrderId) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $order = Order::with([
            'orderDetails.variant.color',
            'orderDetails.variant.size',
            'orderDetails.product.images',
            'payments',
            'statusHistories',
        ])->where('id', $sessionOrderId)
          ->whereNull('user_id')
          ->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        return view('clients.page.order_detail', compact('order'));
    }

    // ==================== Helper ====================

    private function postRequest(string $url, string $payload): string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload),
            ],
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result ?: '';
    }
}
