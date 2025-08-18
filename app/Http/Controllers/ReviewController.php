<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    public function store(Request $request)
    {
        $data = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'rating'  => 'required|integer|min:1|max:5',
            'body'    => 'nullable|string',
        ]);

        $loan = Loan::with(['item','borrower','review'])->findOrFail($data['loan_id']);

        // Must be the owner of the item
        if ($loan->item?->user_id !== auth()->id()) {
            abort(403);
        }

        // Loan must be returned
        if ($loan->status !== 'teruggebracht') {
            return back()->with('status', 'Je kunt pas reviewen nadat het item is teruggebracht.');
        }

        // Prevent duplicate review
        if ($loan->review) {
            return back()->with('status', 'Er is al een review geplaatst voor deze uitlening.');
        }

        $review = Review::create([
            'loan_id'     => $loan->id,
            'reviewer_id' => $loan->item->user_id,   
            'reviewee_id' => $loan->borrower_id,     
            'rating'      => $data['rating'],
            'body'        => $data['body'] ?? null,
        ]);


        return redirect()
            ->route('loans.index')
            ->with('status', 'Review geplaatst!')
            ->with('highlight_review_id', $review->id);
    }


    public function update(Request $request, Review $review)
    {
        if ($review->reviewer_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'body'   => 'nullable|string',
        ]);

        $review->update($data);

        return back()->with('status', 'Review bijgewerkt!');
    }

 
    public function destroy(Review $review)
    {
        if ($review->reviewer_id !== auth()->id()) {
            abort(403);
        }

        $review->delete();

        return back()->with('status', 'Review verwijderd.');
    }

  
    public function index() { abort(404); }
    public function create() { abort(404); }
    public function show(Review $review) { abort(404); }
    public function edit(Review $review) { abort(404); }
}
