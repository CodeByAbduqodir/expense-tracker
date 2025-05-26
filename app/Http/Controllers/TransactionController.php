<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::all();
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
            'payment_type' => 'required|in:cash,card,bank_transfer',
            'date' => 'required|date',
        ]);

        Transaction::create([
            'amount' => $request->amount,
            'type' => $request->type,
            'category' => $request->category,
            'payment_type' => $request->payment_type,
            'date' => $request->date
        ]);

        return redirect()->route('tracker.index')->with('success', 'Transaction added!');
    }
}