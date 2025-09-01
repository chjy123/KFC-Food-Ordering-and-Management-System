<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_name' => 'required|string|max:120',
            'description' => 'nullable|string|max:255',
        ]);
        Category::create($data);
        return back()->with('status','Category created');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'category_name' => 'required|string|max:120',
            'description' => 'nullable|string|max:255',
        ]);
        $category->update($data);
        return back()->with('status','Category updated');
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return redirect()->route('admin.menu')->with('status','Category deleted');
        } catch (\Throwable $e) {
            return back()->withErrors('Delete failed. Move or delete foods in this category first.');
        }
    }
}
