<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->latest('category_id')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|min:2|max:255|unique:categories,category_name',
        ], [
            'category_name.required' => 'Vui lòng nhập tên danh mục.',
            'category_name.min'      => 'Tên danh mục phải có ít nhất 2 ký tự.',
            'category_name.unique'   => 'Tên danh mục đã tồn tại.',
        ]);

        Category::create(['category_name' => $request->category_name]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Thêm danh mục thành công!');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'category_name' => 'required|string|min:2|max:255|unique:categories,category_name,' . $category->category_id . ',category_id',
        ], [
            'category_name.required' => 'Vui lòng nhập tên danh mục.',
            'category_name.min'      => 'Tên danh mục phải có ít nhất 2 ký tự.',
            'category_name.unique'   => 'Tên danh mục đã tồn tại.',
        ]);

        $category->update(['category_name' => $request->category_name]);

        return redirect()->route('admin.categories.edit', $category)
            ->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Không thể xóa! Danh mục này vẫn còn sản phẩm.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Xóa danh mục thành công!');
    }
}