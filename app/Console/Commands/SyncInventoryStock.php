<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncInventoryStock extends Command
{
    protected $signature = 'inventory:sync
                            {--dry-run : Chỉ hiện số bản ghi sai, không cập nhật}';

    protected $description = 'Tính lại soluong_dat từ đơn hàng confirmed/processing/delivered và đồng bộ soluong_co_the_ban';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $stale = $this->getStaleRecords();

        if ($stale->isEmpty()) {
            $this->info('Tất cả dữ liệu tồn kho đã đồng bộ, không cần cập nhật.');
            return self::SUCCESS;
        }

        $this->warn("Phát hiện {$stale->count()} bản ghi inventory bị lệch:");

        $this->table(
            ['variant_id', 'soluong_ton', 'soluong_dat (DB)', 'soluong_dat (confirmed+processing+delivered)', 'soluong_co_the_ban (DB)'],
            $stale->map(fn($r) => [
                $r->variant_id,
                $r->soluong_ton,
                $r->soluong_dat_hientai,
                $r->soluong_dat_thucte,
                $r->soluong_co_the_ban,
            ])->toArray()
        );

        if ($isDryRun) {
            $this->line('--dry-run: không có gì được thay đổi.');
            return self::SUCCESS;
        }

        if (!$this->confirm("Cập nhật {$stale->count()} bản ghi?", true)) {
            $this->line('Đã hủy.');
            return self::SUCCESS;
        }

        $this->syncDat();

        $this->info("Đồng bộ hoàn tất. Đã cập nhật {$stale->count()} bản ghi.");
        return self::SUCCESS;
    }

    private function getStaleRecords(): \Illuminate\Support\Collection
    {
        return collect(DB::select("
            SELECT
                i.inventory_id,
                i.variant_id,
                i.soluong_ton,
                i.soluong_dat             AS soluong_dat_hientai,
                COALESCE(SUM(od.quantity), 0) AS soluong_dat_thucte,
                i.soluong_co_the_ban
            FROM inventory i
            LEFT JOIN order_details od ON od.variant_id = i.variant_id
            LEFT JOIN orders o
                   ON o.id = od.order_id
                  AND o.order_status IN ('confirmed', 'processing', 'delivered')
            GROUP BY i.inventory_id, i.variant_id, i.soluong_ton, i.soluong_dat, i.soluong_co_the_ban
            HAVING soluong_dat_hientai != soluong_dat_thucte
        "));
    }

    /**
     * Chỉ đồng bộ soluong_dat từ đơn confirmed/processing.
     * Không đụng soluong_ton và soluong_co_the_ban vì chúng được cập nhật
     * chính xác theo từng bước confirm/cancel trong OrderService.
     */
    private function syncDat(): void
    {
        DB::statement("
            UPDATE inventory i
            JOIN (
                SELECT
                    i2.inventory_id,
                    COALESCE(SUM(od.quantity), 0) AS dat_thucte
                FROM inventory i2
                LEFT JOIN order_details od ON od.variant_id = i2.variant_id
                LEFT JOIN orders o
                       ON o.id = od.order_id
                      AND o.order_status IN ('confirmed', 'processing', 'delivered')
                GROUP BY i2.inventory_id
            ) AS calc ON calc.inventory_id = i.inventory_id
            SET i.soluong_dat = calc.dat_thucte
        ");
    }
}
