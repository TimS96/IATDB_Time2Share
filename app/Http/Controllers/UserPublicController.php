<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Item;
use App\Models\Review;

class UserPublicController extends Controller
{

    public function show(User $user)
    {
        // Items owned by this user
        $items = Item::where('user_id', $user->id)
            ->latest()
            ->get();

        // Reviews written about this user (they were the borrower)
        $reviews = Review::with(['reviewer', 'loan.item'])
            ->where('reviewee_id', $user->id)
            ->latest()
            ->get();

        $avgRating = round((float) Review::where('reviewee_id', $user->id)->avg('rating'), 2);

        return view('users.show', compact('user', 'items', 'reviews', 'avgRating'));
    }
}
