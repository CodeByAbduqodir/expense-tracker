<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\SubTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('subTransactions')->get();
        $balance = Transaction::where('type', 'income')->sum('amount') - 
                  Transaction::where('type', 'expense')->sum('amount');

        $daily = [
            'income' => Transaction::where('type', 'income')
                ->whereDate('date', Carbon::today())
                ->sum('amount'),
            'expense' => Transaction::where('type', 'expense')
                ->whereDate('date', Carbon::today())
                ->sum('amount')
        ];

        $weekly = [
            'income' => Transaction::where('type', 'income')
                ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->sum('amount'),
            'expense' => Transaction::where('type', 'expense')
                ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->sum('amount')
        ];

        $monthly = [
            'income' => Transaction::where('type', 'income')
                ->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->sum('amount'),
            'expense' => Transaction::where('type', 'expense')
                ->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->sum('amount')
        ];

        return view('tracker', compact('transactions', 'balance', 'daily', 'weekly', 'monthly'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
            'category' => 'nullable|string|max:255',
            'payment_type' => 'nullable|string|max:255',
            'date' => 'required|date',
            'sub_amounts.*' => 'nullable|numeric|min:0',
            'sub_categories.*' => 'nullable|string|max:255'
        ]);

        $transaction = Transaction::create([
            'amount' => $request->amount,
            'type' => $request->type,
            'category' => $request->category,
            'payment_type' => $request->payment_type,
            'date' => $request->date
        ]);

        if ($request->sub_amounts && $request->sub_categories) {
            foreach ($request->sub_amounts as $index => $sub_amount) {
                if ($sub_amount && $request->sub_categories[$index]) {
                    SubTransaction::create([
                        'transaction_id' => $transaction->id,
                        'amount' => $sub_amount,
                        'category' => $request->sub_categories[$index]
                    ]);
                }
            }
        }

        return redirect()->route('tracker.index')->with('success', 'Транзакция добавлена!');
    }
}