<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
      $rating = (int) request('rating');
$q      = request('q');

$reviews = \App\Models\Review::with(['user:id,name,phoneNo', 'food:id,name'])
  ->when($rating, fn($q2) => $q2->where('rating', $rating))
  ->when($q, fn($q2) => $q2->where(function ($w) use ($q) {
      $w->whereHas('user', fn($u) => $u->where('name','like',"%$q%")
                                      ->orWhere('phoneNo','like',"%$q%"))
        ->orWhereHas('food', fn($f) => $f->where('name','like',"%$q%"))
        ->orWhere('comment','like',"%$q%");
  }))
  ->latest()->paginate(10)->withQueryString();

$avg     = round(\App\Models\Review::avg('rating'), 1);
$counts  = \App\Models\Review::selectRaw('rating, COUNT(*) c')->groupBy('rating')->pluck('c','rating')->all();
for ($i=1; $i<=5; $i++) { $counts[$i] = $counts[$i] ?? 0; }
$total   = array_sum($counts);

return view('admin.reviews', compact('reviews','rating','q','avg','counts','total'));

    }
}
