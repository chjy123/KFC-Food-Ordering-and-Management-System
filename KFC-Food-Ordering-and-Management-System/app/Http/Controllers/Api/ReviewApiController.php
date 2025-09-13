<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReviewApiController extends Controller
{
    public function index(Request $request)
    {
        $rating = (int) $request->query('rating');
        $q      = $request->query('q');

        $reviews = Review::with(['user:id,name,phoneNo','food:id,name'])
            ->when($rating, fn($qq) => $qq->where('rating',$rating))
            ->when($q, fn($qq) => $qq->where(function ($w) use ($q) {
                $w->whereHas('user', fn($u)=>$u->where('name','like',"%$q%")->orWhere('phoneNo','like',"%$q%"))
                  ->orWhereHas('food', fn($f)=>$f->where('name','like',"%$q%"))
                  ->orWhere('comment','like',"%$q%");
            }))
            ->latest()->paginate(20);

        return ReviewResource::collection($reviews);
    }

    public function destroy(Review $review)
    {
        Gate::authorize('isAdmin');
        $review->delete();
        return response()->json(['status' => 'deleted']);
    }
}
