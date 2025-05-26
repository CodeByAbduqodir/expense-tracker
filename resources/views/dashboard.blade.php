<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background-color min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="balance-card mb-8">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-3xl font-bold">Balance</h1>
                <div class="flex space-x-4">
                    <button class="expense-button expense" x-data="{ open: false }" @click="open = !open">
                        -
                    </button>
                    <button class="expense-button income" x-data="{ open: false }" @click="open = !open">
                        +
                    </button>
                </div>
            </div>
            <div class="text-4xl font-bold">
                ${{ number_format($balance, 2) }}
            </div>
            <div class="mt-4 text-sm text-white/80">
                <div class="flex justify-between">
                    <span>Income</span>
                    <span>${{ number_format($totalIncome, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Expenses</span>
                    <span>${{ number_format($totalExpenses, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="expense-card">
                <h2 class="text-xl font-semibold mb-4">Daily Summary</h2>
                <div class="dashboard-chart" id="dailyChart"></div>
            </div>
            <div class="expense-card">
                <h2 class="text-xl font-semibold mb-4">Weekly Summary</h2>
                <div class="dashboard-chart" id="weeklyChart"></div>
            </div>
        </div>

        <div class="expense-card">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Recent Expenses</h2>
                <button class="text-primary-color hover:text-primary-color/80">
                    View All
                </button>
            </div>
            <div class="space-y-4">
                @foreach($recentExpenses as $expense)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <h3 class="font-medium">{{ $expense->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $expense->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-danger-color">${{ number_format($expense->amount, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div x-data="{ open: false }" x-show="open" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-md" x-show="open" x-transition>
            <h2 class="text-xl font-semibold mb-4">Add Expense</h2>
            <form action="{{ route('expenses.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block mb-2">Amount</label>
                    <input type="number" step="0.01" name="amount" class="input-field" required>
                </div>
                <div>
                    <label class="block mb-2">Category</label>
                    <select name="category" class="select-field" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-2">Payment Type</label>
                    <div class="payment-type">
                        <label>
                            <input type="radio" name="payment_type" value="cash" checked>
                            <span>Cash</span>
                        </label>
                        <label>
                            <input type="radio" name="payment_type" value="card">
                            <span>Card</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 text-gray-600 hover:text-gray-800" @click="open = false">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary-color text-white rounded-lg hover:bg-primary-color/90">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
