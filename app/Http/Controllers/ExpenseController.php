<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseSplit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ExpenseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $expenses = Expense::where('user_id', $user->id)
            ->with('category')
            ->orderBy('expense_date', 'desc')
            ->paginate(10);

        $income = Expense::where('user_id', $user->id)
            ->income()
            ->sum('amount');

        $expensesTotal = Expense::where('user_id', $user->id)
            ->expense()
            ->sum('amount');

        return view('dashboard', compact('expenses', 'income', 'expensesTotal'));
    }

    public function create()
    {
        $categories = Category::where('user_id', Auth::id())
            ->get();

        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
            'payment_type' => 'required|in:cash,card',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'expense_date' => 'nullable|date',
        ]);

        $expense = Auth::user()->expenses()->create($validated);

        // Handle expense splits if provided
        if ($request->has('splits')) {
            foreach ($request->splits as $split) {
                $expense->splits()->create([
                    'name' => $split['name'],
                    'amount' => $split['amount'],
                    'description' => $split['description'] ?? null,
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Expense created successfully');
    }

    public function show(Expense $expense)
    {
        $this->authorize('view', $expense);
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $this->authorize('update', $expense);
        $categories = Category::where('user_id', Auth::id())->get();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update', $expense);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
            'payment_type' => 'required|in:cash,card',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'expense_date' => 'nullable|date',
        ]);

        $expense->update($validated);

        // Update splits if provided
        if ($request->has('splits')) {
            $expense->splits()->delete();
            foreach ($request->splits as $split) {
                $expense->splits()->create([
                    'name' => $split['name'],
                    'amount' => $split['amount'],
                    'description' => $split['description'] ?? null,
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Expense updated successfully');
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);
        $expense->delete();
        return redirect()->route('dashboard')->with('success', 'Expense deleted successfully');
    }

    public function split(Expense $expense, Request $request)
    {
        $this->authorize('update', $expense);

        $validated = $request->validate([
            'splits' => 'required|array',
            'splits.*.name' => 'required|string',
            'splits.*.amount' => 'required|numeric|min:0',
            'splits.*.description' => 'nullable|string',
        ]);

        // Delete existing splits
        $expense->splits()->delete();

        // Create new splits
        foreach ($validated['splits'] as $split) {
            $expense->splits()->create($split);
        }

        return redirect()->route('expenses.show', $expense)->with('success', 'Expense split successfully');
    }

    public function getDailySummary()
    {
        $user = Auth::user();
        $expenses = Expense::where('user_id', $user->id)
            ->expense()
            ->thisWeek()
            ->select(
                DB::raw('DAYOFWEEK(expense_date) as day'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('day')
            ->get();

        return response()->json($expenses);
    }

    public function getWeeklySummary()
    {
        $user = Auth::user();
        $expenses = Expense::where('user_id', $user->id)
            ->expense()
            ->select(
                DB::raw('WEEK(expense_date) as week'),
                DB::raw('YEAR(expense_date) as year'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('week', 'year')
            ->orderBy('year', 'desc')
            ->orderBy('week', 'desc')
            ->take(4)
            ->get();

        return response()->json($expenses);
    }

    public function getMonthlySummary()
    {
        $user = Auth::user();
        $expenses = Expense::where('user_id', $user->id)
            ->expense()
            ->select(
                DB::raw('MONTH(expense_date) as month'),
                DB::raw('YEAR(expense_date) as year'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month', 'year')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        return response()->json($expenses);
    }
}
