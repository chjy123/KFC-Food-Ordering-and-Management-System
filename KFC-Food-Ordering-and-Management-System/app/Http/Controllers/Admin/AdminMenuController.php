<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminMenuController extends Controller
{
    public function index(Request $request)
{
    $q = trim((string) $request->get('q'));

    $categories = \App\Models\Category::orderBy('category_name')
        ->with(['foods' => function ($foods) use ($q) {
            if ($q !== '') {
                $foods->where(fn($w) => $w->where('name','like',"%{$q}%")
                                          ->orWhere('description','like',"%{$q}%"));
            }
            $foods->latest();
        }])
        ->get();

    $allCategories = \App\Models\Category::orderBy('category_name')
        ->get(['id','category_name','description']);

    return view('admin.menu', compact('categories','allCategories','q'));
}
}
