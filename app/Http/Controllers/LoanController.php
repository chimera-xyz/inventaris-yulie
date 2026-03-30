<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        $search = trim($request->string('search')->toString());

        $query = Loan::query()
            ->with(['item.category', 'loanedBy'])
            ->latest('loan_date');

        if (filled($status)) {
            if ($status === 'overdue') {
                $query->where('status', 'active')
                    ->whereDate('expected_return_date', '<', today());
            } else {
                $query->where('status', $status);
            }
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('borrower_name', 'like', '%' . $search . '%')
                    ->orWhere('borrower_department', 'like', '%' . $search . '%')
                    ->orWhereHas('item', function ($itemQuery) use ($search) {
                        $itemQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('unique_code', 'like', '%' . $search . '%');
                    });
            });
        }

        $loans = $query->paginate(12)->withQueryString();
        $activeLoansCount = Loan::where('status', 'active')->count();
        $overdueLoansCount = Loan::where('status', 'active')
            ->whereDate('expected_return_date', '<', today())
            ->count();
        $returnedLoansCount = Loan::where('status', 'returned')->count();

        return view('loans.index', compact(
            'loans',
            'status',
            'search',
            'activeLoansCount',
            'overdueLoansCount',
            'returnedLoansCount'
        ));
    }

    public function create(Item $item)
    {
        if ($item->activeLoan()->exists()) {
            return redirect()->route('items.show', $item)
                ->with('error', 'Item ini masih memiliki peminjaman aktif.');
        }

        if ($item->status !== 'available') {
            return redirect()->route('items.show', $item)
                ->with('error', 'Hanya item berstatus tersedia yang bisa dipinjamkan.');
        }

        return view('loans.create', compact('item'));
    }

    public function store(Request $request, Item $item): RedirectResponse
    {
        if ($item->activeLoan()->exists()) {
            return redirect()->route('items.show', $item)
                ->with('error', 'Item ini masih memiliki peminjaman aktif.');
        }

        if ($item->status !== 'available') {
            return redirect()->route('items.show', $item)
                ->with('error', 'Hanya item berstatus tersedia yang bisa dipinjamkan.');
        }

        $validated = $request->validate([
            'borrower_name' => 'required|string|max:200',
            'borrower_department' => 'nullable|string|max:200',
            'loan_date' => 'required|date',
            'expected_return_date' => 'nullable|date|after_or_equal:loan_date',
            'notes' => 'nullable|string',
        ]);

        Loan::create([
            'item_id' => $item->id,
            'loaned_by' => auth()->id(),
            'borrower_name' => $validated['borrower_name'],
            'borrower_department' => $validated['borrower_department'] ?? null,
            'loan_date' => $validated['loan_date'],
            'expected_return_date' => $validated['expected_return_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'active',
        ]);

        return redirect()->route('items.show', $item)
            ->with('success', 'Peminjaman asset berhasil dicatat.');
    }

    public function markReturned(Request $request, Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'active') {
            return redirect()->route('items.show', $loan->item)
                ->with('error', 'Peminjaman ini sudah ditutup.');
        }

        $validated = $request->validate([
            'return_notes' => 'nullable|string',
        ]);

        $loan->markAsReturned($validated['return_notes'] ?? null);

        return redirect()->route('items.show', $loan->item)
            ->with('success', 'Asset berhasil dikembalikan ke status tersedia.');
    }
}
