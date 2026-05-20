<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    // ─── Từ khóa nhận diện ý định ────────────────────────────────────────────

    private const INTENT_KEYWORDS = [
        'product' => ['sản phẩm', 'áo', 'quần', 'giày', 'tất', 'phụ kiện', 'mẫu', 'hàng', 'mua', 'có bán', 'có không', 'loại nào', 'thương hiệu', 'size'],
        'order'   => ['đơn hàng', 'đơn', 'order', 'giao hàng', 'ship', 'trạng thái', 'theo dõi', 'đã mua', 'mua rồi', 'chưa nhận', 'hủy đơn', 'đổi hàng'],
        'price'   => ['giá', 'bao nhiêu', 'tiền', 'rẻ', 'đắt', 'khuyến mãi', 'sale', 'giảm giá', 'ưu đãi', 'voucher'],
        'policy'  => ['đổi trả', 'hoàn tiền', 'bảo hành', 'chính sách', 'điều kiện', 'quy định', 'thời hạn'],
        'payment' => ['thanh toán', 'momo', 'cod', 'chuyển khoản', 'banking', 'trả tiền', 'phí ship', 'phí vận chuyển'],
    ];

    // ─── Entry point ─────────────────────────────────────────────────────────

    public function chat(Request $request)
    {
        $request->validate([
            'message'        => 'required|string|max:500',
            'history'        => 'nullable|array|max:20',
            'history.*.role' => 'required|in:user,model',
            'history.*.text' => 'required|string|max:1000',
        ]);

        $apiKey = config('services.gemini.key');
        $model  = config('services.gemini.model');

        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'Chatbot chưa được cấu hình.']);
        }

        $userMessage = $request->message;
        $intents     = $this->detectIntents($userMessage);

        // Build conversation contents
        $contents = [];
        foreach ($request->history ?? [] as $msg) {
            $contents[] = [
                'role'  => $msg['role'],
                'parts' => [['text' => $msg['text']]],
            ];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

        $response = Http::timeout(25)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
            [
                'system_instruction' => [
                    'parts' => [['text' => $this->buildSystemPrompt($userMessage, $intents)]],
                ],
                'contents'         => $contents,
                'generationConfig' => [
                    'temperature'     => 0.6,
                    'maxOutputTokens' => 700,
                ],
            ]
        );

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Xin lỗi, tôi đang gặp sự cố. Vui lòng thử lại sau!',
            ]);
        }

        $text = $response->json('candidates.0.content.parts.0.text')
            ?? 'Xin lỗi, tôi không hiểu câu hỏi. Bạn có thể hỏi lại không?';

        return response()->json(['success' => true, 'message' => trim($text)]);
    }

    // ─── Nhận diện ý định ─────────────────────────────────────────────────────

    private function detectIntents(string $message): array
    {
        $lower   = mb_strtolower($message);
        $matched = [];
        foreach (self::INTENT_KEYWORDS as $intent => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($lower, $kw)) {
                    $matched[] = $intent;
                    break;
                }
            }
        }
        return $matched ?: ['general'];
    }

    // ─── Xây dựng system prompt ────────────────────────────────────────────────

    private function buildSystemPrompt(string $userMessage, array $intents): string
    {
        $user = Auth::user();

        // ── Persona & vai trò ──
        $p  = "Bạn là **VoxBot** - trợ lý AI chính thức của **VoxFootball**, cửa hàng bán đồ thể thao bóng đá tại Việt Nam.\n\n";

        // ── Chào cá nhân hóa ──
        if ($user) {
            $p .= "Khách hàng đang chat: **{$user->fullname}** (đã đăng nhập)\n";
            $p .= "Hãy xưng hô thân thiện, gọi khách là \"{$user->fullname}\" hoặc \"bạn\".\n\n";
        } else {
            $p .= "Khách hàng chưa đăng nhập. Nếu hỏi về đơn hàng, nhắc đăng nhập để tra cứu.\n\n";
        }

        // ── Chính sách cửa hàng (luôn có) ──
        $p .= "**THÔNG TIN CỬA HÀNG:**\n";
        $p .= "- Hotline: 0123-456-789 (8h-21h hàng ngày)\n";
        $p .= "- Giao hàng: toàn quốc, 2-5 ngày làm việc\n";
        $p .= "- Phí ship: miễn phí đơn từ 500.000đ, dưới 500.000đ thu 30.000đ\n";
        $p .= "- Đổi trả: trong 7 ngày, hàng còn nguyên tem nhãn\n";
        $p .= "- Thanh toán: COD (tiền mặt khi nhận), MoMo, chuyển khoản ngân hàng\n\n";

        // ── Danh mục sản phẩm ──
        $p .= $this->buildCategoryContext();

        // ── Context theo ý định ──
        if (in_array('product', $intents) || in_array('price', $intents)) {
            $p .= $this->buildProductContext($userMessage);
        }

        if (in_array('order', $intents) && $user) {
            $p .= $this->buildOrderContext();
        }

        // ── Quy tắc trả lời ──
        $p .= "**QUY TẮC:**\n";
        $p .= "- Trả lời bằng tiếng Việt, thân thiện, ngắn gọn (tối đa 5-6 câu)\n";
        $p .= "- Khi nhắc đến sản phẩm, thêm link xem: " . url('/danh-muc') . "\n";
        $p .= "- Không bịa thông tin, không trả lời ngoài phạm vi cửa hàng\n";
        $p .= "- Nếu không chắc, hướng dẫn liên hệ hotline\n";

        return $p;
    }

    // ─── Context danh mục ─────────────────────────────────────────────────────

    private function buildCategoryContext(): string
    {
        try {
            $categories = Category::with('subcategories')
                ->withCount('products')
                ->get();

            if ($categories->isEmpty()) return '';

            $ctx = "**DANH MỤC SẢN PHẨM:**\n";
            foreach ($categories as $cat) {
                $ctx .= "- {$cat->category_name} ({$cat->products_count} sản phẩm)";
                if ($cat->subcategories->isNotEmpty()) {
                    $subs = $cat->subcategories->pluck('subcategory_name')->join(', ');
                    $ctx .= ": {$subs}";
                }
                $ctx .= "\n";
            }
            return $ctx . "\n";
        } catch (\Throwable) {
            return '';
        }
    }

    // ─── Context sản phẩm liên quan ───────────────────────────────────────────

    private function buildProductContext(string $message): string
    {
        try {
            // Tách keywords từ câu hỏi (loại stopwords đơn giản)
            $stopwords = ['cho', 'tôi', 'xem', 'có', 'không', 'bao', 'nhiêu', 'giá', 'của', 'các', 'những', 'loại', 'nào', 'muốn', 'mua'];
            $words     = array_filter(
                explode(' ', mb_strtolower($message)),
                fn($w) => mb_strlen($w) > 2 && !in_array($w, $stopwords)
            );

            if (empty($words)) {
                // Không có keyword → lấy sản phẩm hot
                $products = Product::where('product_hot', true)->take(5)->get(['product_id', 'product_name', 'product_gia']);
            } else {
                $query = Product::query();
                foreach (array_slice($words, 0, 3) as $word) {
                    $query->orWhere('product_name', 'like', "%{$word}%");
                }
                $products = $query->take(5)->get(['product_id', 'product_name', 'product_gia']);
            }

            if ($products->isEmpty()) return '';

            $ctx = "**SẢN PHẨM LIÊN QUAN (dữ liệu thực từ DB):**\n";
            foreach ($products as $p) {
                $price = number_format($p->product_gia) . 'đ';
                $link  = url("/san-pham/{$p->product_id}");
                $ctx  .= "- {$p->product_name} | Giá: {$price} | Link: {$link}\n";
            }
            return $ctx . "\n";
        } catch (\Throwable) {
            return '';
        }
    }

    // ─── Context đơn hàng chi tiết ────────────────────────────────────────────

    private function buildOrderContext(): string
    {
        try {
            $orders = Order::with(['orderDetails.product'])
                ->where('user_id', Auth::id())
                ->latest('order_date')
                ->take(3)
                ->get();

            if ($orders->isEmpty()) return "Khách chưa có đơn hàng nào.\n\n";

            $statusMap = [
                'pending'    => 'Chờ xác nhận',
                'confirmed'  => 'Đã xác nhận',
                'processing' => 'Đang xử lý',
                'delivered'  => 'Đã giao',
                'cancelled'  => 'Đã hủy',
            ];

            $ctx = "**ĐƠN HÀNG CỦA KHÁCH (dữ liệu thực):**\n";
            foreach ($orders as $order) {
                $status = $statusMap[$order->order_status] ?? $order->order_status;
                $date   = $order->order_date?->format('d/m/Y') ?? '';
                $total  = number_format($order->total_amount) . 'đ';
                $ctx   .= "Đơn #{$order->order_number} | {$date} | {$total} | **{$status}**\n";

                foreach ($order->orderDetails->take(3) as $detail) {
                    $name  = $detail->product->product_name ?? 'Sản phẩm';
                    $ctx  .= "  + {$name} x{$detail->quantity}\n";
                }
            }
            return $ctx . "\n";
        } catch (\Throwable) {
            return '';
        }
    }
}
