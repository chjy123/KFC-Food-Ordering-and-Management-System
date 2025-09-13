<?php
// Author's Name: Chow Jun Yu
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserWebServiceController extends Controller
{
    public function getUserInfo(Request $request, $userId)
    {
        $validated = $request->validate([
            'queryFlag' => ['required', Rule::in([1, 2, 3])],
        ]);

        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'status'  => 'N',
                'message' => 'User not found',
            ], 404);
        }

        $details = [
            'hpNo'     => null,
            'offNo'    => null,
            'houseAdd' => null,
            'offAdd'   => null,
        ];

        if (in_array($validated['queryFlag'], [1, 3])) {
            $details['hpNo']  = $user->phoneNo;
        }

        return response()->json([
            'status'      => 'A',
            'userName'    => $user->name,
            'userEmail'   => $user->email,
            'userDetails' => $details,
        ]);
    }
}
