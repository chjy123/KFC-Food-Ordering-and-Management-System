<?php
#author’s name： Lim Jing Min
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\Bus\CommandBus;
use App\Domain\Reviews\Commands\DeleteReviewCommand;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $rating = (int) $request->input('rating');
        $q      = $request->input('q');

        $reviews = Review::with(['user:id,name,phoneNo', 'food:id,name'])
            ->when($rating, fn($q2) => $q2->where('rating', $rating))
            ->when($q, fn($q2) => $q2->where(function ($w) use ($q) {
                $w->whereHas('user', fn($u) => $u->where('name','like',"%$q%")
                                                ->orWhere('phoneNo','like',"%$q%"))
                  ->orWhereHas('food', fn($f) => $f->where('name','like',"%$q%"))
                  ->orWhere('comment','like',"%$q%");
            }))
            ->latest()->paginate(10)->withQueryString();

        $avg    = round(Review::avg('rating') ?? 0, 1);
        $counts = Review::selectRaw('rating, COUNT(*) c')->groupBy('rating')->pluck('c','rating')->all();
        for ($i=1; $i<=5; $i++) { $counts[$i] = $counts[$i] ?? 0; }
        $total  = array_sum($counts);

        return view('admin.reviews', compact('reviews','rating','q','avg','counts','total'));
    }

    public function destroy(Request $request, CommandBus $bus, Review $review)
    {
        $params = $request->only('q','rating','page');

        $bus->dispatch(new DeleteReviewCommand(
            reviewId: $review->id,
            actorUserId: Auth::id() ?? 0
        ));

        return redirect()->route('admin.reviews', $params)
            ->with('status', 'Review deleted.');
    }
}
