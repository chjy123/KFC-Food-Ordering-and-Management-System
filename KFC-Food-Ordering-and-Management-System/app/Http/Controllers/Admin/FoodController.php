<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoodController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $data['availability'] = true;
        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('foods', 'public');
        }
        $food = Food::create($data);
        return redirect()->route('admin.menu', ['category' => $food->category_id])->with('status','Food created');
    }

    public function update(Request $request, Food $food)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        if ($request->hasFile('image')) {
            if ($food->image_url) Storage::disk('public')->delete($food->image_url);
            $data['image_url'] = $request->file('image')->store('foods', 'public');
        }
        $food->update($data);
        return redirect()->route('admin.menu', ['category' => $food->category_id])->with('status','Food updated');
    }

    public function destroy(Food $food)
    {
        $categoryId = $food->category_id;
        if ($food->image_url) Storage::disk('public')->delete($food->image_url);
        $food->delete();
        return redirect()->route('admin.menu', ['category' => $categoryId])->with('status','Food deleted');
    }
}
