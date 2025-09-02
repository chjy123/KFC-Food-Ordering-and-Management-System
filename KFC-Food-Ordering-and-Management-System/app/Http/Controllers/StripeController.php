<?php
#author’s name： Pang Jun Meng
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function stripe(Request $request)
    {
        return view('stripe');
    }
}
