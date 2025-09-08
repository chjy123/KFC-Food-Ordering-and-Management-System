<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\Bus\CommandBus;
use App\Domain\Foods\Commands\CreateFoodCommand;
use App\Domain\Foods\Commands\UpdateFoodCommand;
use App\Domain\Foods\Commands\DeleteFoodCommand;

class FoodController extends Controller
{
    public function store(Request $request, CommandBus $bus)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Store file first; keep relative path like your current code (e.g. 'foods/abc.jpg')
        $imagePath = $request->file('image')
            ? $request->file('image')->store('foods', 'public')
            : null;

        $food = $bus->dispatch(new CreateFoodCommand(
            categoryId:  (int) $data['category_id'],
            name:        $data['name'],
            description: $data['description'] ?? null,
            price:       (float) $data['price'],
            availability:true,                 // your current default
            imagePath:   $imagePath,
            actorUserId: Auth::id() ?? 0
        ));

        return redirect()
            ->route('admin.menu', ['category' => $food->category_id])
            ->with('status', 'Food created');
    }

    public function update(Request $request, CommandBus $bus, Food $food)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $newImagePath = $request->file('image')
            ? $request->file('image')->store('foods', 'public')
            : null;

        $updated = $bus->dispatch(new UpdateFoodCommand(
            foodId:       $food->id,
            categoryId:   (int) $data['category_id'],
            name:         $data['name'],
            description:  $data['description'] ?? null,
            price:        (float) $data['price'],
            availability: null,               // not editing availability here
            newImagePath: $newImagePath,      // handler will delete old and replace
            actorUserId:  Auth::id() ?? 0
        ));

        return redirect()
            ->route('admin.menu', ['category' => $updated->category_id])
            ->with('status', 'Food updated');
    }

    public function destroy(CommandBus $bus, Food $food)
    {
        $categoryId = $food->category_id; // for redirect after delete

        $bus->dispatch(new DeleteFoodCommand(
            foodId: $food->id,
            actorUserId: Auth::id() ?? 0
        ));

        return redirect()
            ->route('admin.menu', ['category' => $categoryId])
            ->with('status', 'Food deleted');
    }
}
