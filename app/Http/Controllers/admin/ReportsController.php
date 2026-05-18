<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    // Route group sử dụng middleware 'admin' — không cần kiểm tra session trong constructor.

    public function index()
    {
        return view('admin.reports.overview');
    }

    // ==================== ACTIONS ====================

    public function orders(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('date_to',   now()->toDateString());
        $groupBy  = $request->input('group_by', 'day');

        return view('admin.reports.orders', [
            'dateFrom'         => $dateFrom,
            'dateTo'           => $dateTo,
            'groupBy'          => $groupBy,
            'overallStats'     => $this->getOrderOverallStats($dateFrom, $dateTo),
            'statusStats'      => $this->getOrderStatusStats($dateFrom, $dateTo),
            'regionStats'      => $this->getOrderRegionStats($dateFrom, $dateTo),
            'successRateStats' => $this->getOrderSuccessRateStats($dateFrom, $dateTo),
            'trendStats'       => $this->getOrderTrendStats($dateFrom, $dateTo, $groupBy),
        ]);
    }

    public function products(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('date_to',   now()->toDateString());

        return view('admin.reports.products', [
            'dateFrom'         => $dateFrom,
            'dateTo'           => $dateTo,
            'topProducts'      => $this->getTopSellingProducts($dateFrom, $dateTo, 20),
            'stockStats'       => $this->getStockStats(),
            'lowStockProducts' => $this->getLowStockProducts(10),
            'categoryStats'    => $this->getProductCategoryStats($dateFrom, $dateTo),
        ]);
    }

    public function revenue(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('date_to',   now()->toDateString());
        $groupBy  = $request->input('group_by', 'day');

        return view('admin.reports.revenue', [
            'dateFrom'           => $dateFrom,
            'dateTo'             => $dateTo,
            'groupBy'            => $groupBy,
            'revenueStats'       => $this->getRevenueStats($dateFrom, $dateTo),
            'revenueTrend'       => $this->getRevenueTrend($dateFrom, $dateTo, $groupBy),
            'paymentMethodStats' => $this->getRevenueByPaymentMethod($dateFrom, $dateTo),
            'comparison'         => $this->getRevenueComparison($dateFrom, $dateTo),
        ]);
    }

    // ==================== ORDER STATS ====================

    private function getOrderOverallStats(string $dateFrom, string $dateTo): object|null
    {
        return DB::selectOne("
            SELECT
                COUNT(*)                                                            AS total_orders,
                SUM(order_status = 'delivered')                                    AS delivered_orders,
                SUM(order_status = 'cancelled')                                    AS cancelled_orders,
                SUM(total_amount)                                                  AS total_revenue,
                AVG(total_amount)                                                  AS avg_order_value
            FROM orders
            WHERE DATE(created_at) BETWEEN ? AND ?
        ", [$dateFrom, $dateTo]);
    }

    private function getOrderStatusStats(string $dateFrom, string $dateTo): array
    {
        return DB::select("
            SELECT
                order_status,
                COUNT(*)                                                            AS cnt,
                SUM(total_amount)                                                  AS revenue,
                ROUND(COUNT(*) * 100.0 / (
                    SELECT COUNT(*) FROM orders WHERE DATE(created_at) BETWEEN ? AND ?
                ), 2)                                                              AS percentage
            FROM orders
            WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY order_status
            ORDER BY cnt DESC
        ", [$dateFrom, $dateTo, $dateFrom, $dateTo]);
    }

    /**
     * Thống kê theo tỉnh/thành từ JSON trong cột notes: {"province": "...", ...}
     */
    private function getOrderRegionStats(string $dateFrom, string $dateTo): array
    {
        return DB::select("
            SELECT
                JSON_UNQUOTE(JSON_EXTRACT(notes, '$.province')) AS province_name,
                COUNT(*)                                        AS order_count,
                SUM(total_amount)                              AS revenue,
                ROUND(AVG(total_amount), 0)                    AS avg_order_value
            FROM orders
            WHERE DATE(created_at) BETWEEN ? AND ?
              AND JSON_EXTRACT(notes, '$.province') IS NOT NULL
            GROUP BY province_name
            ORDER BY order_count DESC
            LIMIT 20
        ", [$dateFrom, $dateTo]);
    }

    private function getOrderSuccessRateStats(string $dateFrom, string $dateTo): object|null
    {
        return DB::selectOne("
            SELECT
                SUM(order_status = 'delivered')                AS success_count,
                SUM(order_status IN ('cancelled'))             AS failed_count,
                COUNT(*)                                       AS total_count
            FROM orders
            WHERE DATE(created_at) BETWEEN ? AND ?
        ", [$dateFrom, $dateTo]);
    }

    private function getOrderTrendStats(string $dateFrom, string $dateTo, string $groupBy): array
    {
        $fmt = $this->dateFormat($groupBy);

        return DB::select("
            SELECT
                DATE_FORMAT(created_at, ?) AS period,
                COUNT(*)                   AS order_count,
                SUM(total_amount)          AS revenue
            FROM orders
            WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY period
            ORDER BY period
        ", [$fmt, $dateFrom, $dateTo]);
    }

    // ==================== PRODUCT / STOCK STATS ====================

    private function getTopSellingProducts(string $dateFrom, string $dateTo, int $limit = 20): array
    {
        return DB::select("
            SELECT
                od.product_name                    AS product_name,
                SUM(od.quantity)                   AS total_sold,
                SUM(od.subtotal)                   AS total_revenue,
                AVG(od.product_gia)                AS avg_price,
                COUNT(DISTINCT o.id)               AS order_count
            FROM orders o
            JOIN order_details od ON o.id = od.order_id
            WHERE DATE(o.created_at) BETWEEN ? AND ?
              AND o.order_status NOT IN ('cancelled')
            GROUP BY od.product_id, od.product_name
            ORDER BY total_sold DESC
            LIMIT ?
        ", [$dateFrom, $dateTo, $limit]);
    }

    private function getStockStats(): object|null
    {
        return DB::selectOne("
            SELECT
                COUNT(*)                                                    AS total_variants,
                SUM(soluong_co_the_ban > 0)                                AS in_stock,
                SUM(soluong_co_the_ban <= 0)                               AS out_of_stock,
                SUM(soluong_co_the_ban > 0 AND soluong_co_the_ban <= muc_canh_bao) AS low_stock,
                SUM(soluong_ton)                                           AS total_quantity,
                AVG(soluong_ton)                                           AS avg_quantity
            FROM inventory
        ");
    }

    private function getLowStockProducts(int $limit = 10): array
    {
        return DB::select("
            SELECT
                p.product_name,
                c.color_ten,
                ps.product_size,
                i.soluong_co_the_ban AS stock_qty,
                i.muc_canh_bao       AS warning_level
            FROM inventory i
            JOIN variant v       ON i.variant_id      = v.variant_id
            JOIN product p       ON v.product_id      = p.product_id
            JOIN colors c        ON v.color_id         = c.color_id
            JOIN product_size ps ON v.product_size_id  = ps.product_size_id
            WHERE i.soluong_co_the_ban > 0
              AND i.soluong_co_the_ban <= i.muc_canh_bao
            ORDER BY i.soluong_co_the_ban ASC
            LIMIT ?
        ", [$limit]);
    }

    private function getProductCategoryStats(string $dateFrom, string $dateTo): array
    {
        return DB::select("
            SELECT
                c.category_name,
                COUNT(DISTINCT p.product_id) AS product_count,
                COALESCE(SUM(od.quantity), 0) AS total_sold,
                COALESCE(SUM(od.subtotal), 0) AS revenue
            FROM categories c
            LEFT JOIN product p  ON c.category_id = p.category_id
            LEFT JOIN order_details od ON p.product_id = od.product_id
            LEFT JOIN orders o ON od.order_id = o.id
                AND DATE(o.created_at) BETWEEN ? AND ?
                AND o.order_status NOT IN ('cancelled')
            GROUP BY c.category_id, c.category_name
            ORDER BY revenue DESC
        ", [$dateFrom, $dateTo]);
    }

    // ==================== REVENUE STATS ====================

    private function getRevenueStats(string $dateFrom, string $dateTo): object|null
    {
        return DB::selectOne("
            SELECT
                SUM(total_amount)   AS total_revenue,
                COUNT(*)            AS total_orders,
                AVG(total_amount)   AS avg_order_value,
                MAX(total_amount)   AS max_order_value,
                MIN(total_amount)   AS min_order_value
            FROM orders
            WHERE DATE(created_at) BETWEEN ? AND ?
              AND order_status NOT IN ('cancelled')
        ", [$dateFrom, $dateTo]);
    }

    private function getRevenueTrend(string $dateFrom, string $dateTo, string $groupBy): array
    {
        $fmt = $this->dateFormat($groupBy);

        return DB::select("
            SELECT
                DATE_FORMAT(created_at, ?) AS period,
                SUM(total_amount)          AS revenue,
                COUNT(*)                   AS order_count,
                AVG(total_amount)          AS avg_order_value
            FROM orders
            WHERE DATE(created_at) BETWEEN ? AND ?
              AND order_status NOT IN ('cancelled')
            GROUP BY period
            ORDER BY period
        ", [$fmt, $dateFrom, $dateTo]);
    }

    private function getRevenueByPaymentMethod(string $dateFrom, string $dateTo): array
    {
        return DB::select("
            SELECT
                p.payment_method,
                COUNT(*)            AS order_count,
                SUM(o.total_amount) AS revenue,
                AVG(o.total_amount) AS avg_order_value
            FROM orders o
            JOIN payments p ON o.id = p.order_id
            WHERE DATE(o.created_at) BETWEEN ? AND ?
              AND o.order_status NOT IN ('cancelled')
              AND p.payment_status = 'completed'
            GROUP BY p.payment_method
            ORDER BY revenue DESC
        ", [$dateFrom, $dateTo]);
    }

    private function getRevenueComparison(string $dateFrom, string $dateTo): array
    {
        $days         = (int) now()->parse($dateFrom)->diffInDays(now()->parse($dateTo)) + 1;
        $prevFrom     = now()->parse($dateFrom)->subDays($days)->toDateString();
        $prevTo       = now()->parse($dateTo)->subDays($days)->toDateString();

        return DB::select("
            SELECT 'current' AS period, SUM(total_amount) AS revenue, COUNT(*) AS orders
            FROM orders
            WHERE DATE(created_at) BETWEEN ? AND ?
              AND order_status NOT IN ('cancelled')
            UNION ALL
            SELECT 'previous' AS period, SUM(total_amount) AS revenue, COUNT(*) AS orders
            FROM orders
            WHERE DATE(created_at) BETWEEN ? AND ?
              AND order_status NOT IN ('cancelled')
        ", [$dateFrom, $dateTo, $prevFrom, $prevTo]);
    }

    // ==================== HELPERS ====================

    private function dateFormat(string $groupBy): string
    {
        return match ($groupBy) {
            'week'  => '%Y-%u',
            'month' => '%Y-%m',
            'year'  => '%Y',
            default => '%Y-%m-%d',
        };
    }
}
