<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Variant;

class InventoryController extends Controller
{
    public function index()
    {
        return view('admin.inventory.index');
    }

    public function list()
    {
        $inventories = Inventory::with(['variant.product', 'variant.color', 'variant.size'])
            ->get();

        return view('admin.inventory.list', compact('inventories'));
    }

    public function detail()
    {
        $variants = Variant::with(['product', 'color', 'size', 'inventory'])
            ->get();

        return view('admin.inventory.detail', compact('variants'));
    }
}
