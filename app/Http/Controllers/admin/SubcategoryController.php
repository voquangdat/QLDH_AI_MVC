<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubcategoryController extends Controller
{
    public function index()
    {
        $subcategories = Subcategory::with('category')
            ->withCount('products')
            ->orderBy('category_id')
            ->get();

        return view('admin.subcategories.index', compact('subcategories'));
    }

    public function create()
    {
        $categories = Category::orderBy('category_id')->get();
        return view('admin.subcategories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'      => 'required|exists:categories,category_id',
            'subcategory_name' => [
                'required', 'string', 'min:2', 'max:150',
                Rule::unique('subcategories')->where('category_id', $request->category_id),
            ],
        ], [
            'category_id.required'      => 'Vui lòng chọn danh mục.',
            'category_id.exists'        => 'Danh mục không hợp lệ.',
            'subcategory_name.required' => 'Vui lòng nhập tên loại sản phẩm.',
            'subcategory_name.min'      => 'Tên loại phải có ít nhất 2 ký tự.',
            'subcategory_name.unique'   => 'Tên loại sản phẩm đã tồn tại trong danh mục này.',
        ]);

        Subcategory::create([
            'category_id'      => $request->category_id,
            'subcategory_name' => $request->subcategory_name,
        ]);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Thêm loại sản phẩm thành công!');
    }

    public function edit(Subcategory $subcategory)
    {
        $categories = Category::orderBy('category_id')->get();
        return view('admin.subcategories.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, Subcategory $subcategory)
    {
        $request->validate([
            'category_id'      => 'required|exists:categories,category_id',
            'subcategory_name' => [
                'required', 'string', 'min:2', 'max:150',
                Rule::unique('subcategories')
                    ->where('category_id', $request->category_id)
                    ->ignore($subcategory->subcategory_id, 'subcategory_id'),
            ],
        ], [
            'category_id.required'      => 'Vui lòng chọn danh mục.',
            'category_id.exists'        => 'Danh mục không hợp lệ.',
            'subcategory_name.required' => 'Vui lòng nhập tên loại sản phẩm.',
            'subcategory_name.min'      => 'Tên loại phải có ít nhất 2 ký tự.',
            'subcategory_name.unique'   => 'Tên loại sản phẩm đã tồn tại trong danh mục này.',
        ]);

        $subcategory->update([
            'category_id'      => $request->category_id,
            'subcategory_name' => $request->subcategory_name,
        ]);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Cập nhật loại sản phẩm thành công!');
    }

    public function destroy(Subcategory $subcategory)
    {
        if ($subcategory->products()->exists()) {
            return redirect()->route('admin.subcategories.index')
                ->with('error', 'Không thể xóa! Loại sản phẩm này vẫn còn sản phẩm.');
        }

        $subcategory->delete();

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Xóa loại sản phẩm thành công!');
    }
}
