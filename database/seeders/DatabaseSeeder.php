<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Lookup tables (no dependencies)
            RoleSeeder::class,
            CategorySeeder::class,
            ColorSeeder::class,
            ProductSizeSeeder::class,
            CouponSeeder::class,

            // Depends on roles & categories
            UserSeeder::class,
            SubcategorySeeder::class,

            // Depends on categories & subcategories
            ProductSeeder::class,

            // Depends on products & users
            ShippingAddressSeeder::class,
            ProductImageSeeder::class,
            VariantSeeder::class,

            // Depends on variants
            InventorySeeder::class,

            // Depends on users
            OrderSeeder::class,

            // Depends on orders, products, variants
            OrderDetailSeeder::class,
            PaymentSeeder::class,
            ShippingSeeder::class,
            InvoiceSeeder::class,

            // Depends on orders & users
            WishlistSeeder::class,
            CartSeeder::class,
            NotificationSeeder::class,
            ReviewSeeder::class,
            OrderStatusHistorySeeder::class,
            OrderCouponSeeder::class,

            // Depends on order_details & shipping
            ReturnRequestSeeder::class,
            ShippingTrackingSeeder::class,
        ]);
    }
}
