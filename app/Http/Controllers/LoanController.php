<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Item;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;


    public function index()
    {
        $userId = auth()->id();

        $geleendDoorMij = Loan::with(['item.user', 'borrower', 'review'])
            ->where('borrower_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $uitgeleendDoorMij = Loan::with(['item.user', 'borrower', 'review'])
            ->whereHas('item', fn ($q) => $q->where('user_id', $userId))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('loans.index', compact('geleendDoorMij', 'uitgeleendDoorMij'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_id'    => 'required|exists:items,id',
            'start_date' => 'required|date',
            'due_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $item = Item::with('user')->findOrFail($data['item_id']);

        if ($item->user_id === auth()->id()) {
            return back()->with('status', 'Je kunt je eigen item niet aanvragen.');
        }
        if ($item->status !== 'beschikbaar') {
            return back()->with('status', 'Item is niet beschikbaar.');
        }

        $hasOpen = Loan::where('item_id', $item->id)
            ->where('borrower_id', auth()->id())
            ->whereIn('status', ['aangevraagd', 'geaccepteerd', 'actief'])
            ->exists();

        if ($hasOpen) {
            return back()->with('status', 'Je hebt al een open uitlening/aanvraag voor dit item.');
        }

        Loan::create([
            'item_id'     => $item->id,
            'borrower_id' => auth()->id(),
            'start_date'  => $data['start_date'],
            'due_date'    => $data['due_date'],
            'status'      => 'aangevraagd',
        ]);

        return redirect()->route('loans.index')->with('status', 'Aanvraag verstuurd!');
    }

    public function update(Request $request, Loan $loan)
    {
        $request->validate([
            'action' => 'required|string',
        ]);

        $loan->load('item');
        if (!$loan->item) {
            return back()->with('status', 'Onbekend item bij deze uitlening.');
        }

        $action = $request->input('action');

        $this->authorize('updateStatus', [$loan, $action]);

        switch ($action) {
            case 'accept':
                $loan->status = 'geaccepteerd';
                $loan->save();
                return back()->with('status', 'Aanvraag geaccepteerd.');

            case 'start':
                $loan->status = 'actief';
                $loan->save();

                $loan->item->status = 'uitgeleend';
                $loan->item->save();

                return back()->with('status', 'Uitlening gestart.');

            case 'returned':
                $loan->status = 'teruggebracht';
                $loan->returned_at = now();
                $loan->save();

                $loan->item->status = 'beschikbaar';
                $loan->item->save();

                return redirect()
                    ->route('loans.index')
                    ->with('status', 'Uitlening gemarkeerd als teruggebracht.')
                    ->with('prompt_review_loan_id', $loan->id);

            case 'reject':
                $loan->status = 'geweigerd';
                $loan->save();
                return back()->with('status', 'Aanvraag geweigerd.');

            case 'cancel':
                $loan->status = 'geannuleerd';
                $loan->save();
                return back()->with('status', 'Aanvraag geannuleerd.');

            default:
                return back()->with('status', 'Onbekende actie.');
        }
    }
 
    public function create() { abort(404); }
    public function show(Loan $loan) { abort(404); }
    public function edit(Loan $loan) { abort(404); }
    public function destroy(Loan $loan) { abort(404); }
}
