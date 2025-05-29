<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Expense Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-emerald-100 to-teal-200 font-sans min-h-screen flex flex-col">
    <header class="bg-teal-800 text-white shadow-lg">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-extrabold drop-shadow-md">
                <i class="fas fa-wallet mr-2 text-teal-300"></i> Expense Tracker
            </h1>
            <nav>
                <ul class="flex space-x-6">
                    <li>
                        <a href="#add-transaction" class="hover:text-teal-300 transition-colors duration-200 flex items-center">
                            <i class="fas fa-plus-circle mr-1"></i> Add Transaction
                        </a>
                    </li>
                    <li>
                        <a href="#transactions" class="hover:text-teal-300 transition-colors duration-200 flex items-center">
                            <i class="fas fa-list-ul mr-1"></i> Transactions
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-6 max-w-4xl flex-grow">
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            </div>
        @endif
        @if (session('success'))
            <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded mb-4 flex items-center">
                <i class="fas fa-check mr-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card bg-white p-6 rounded-xl shadow-2xl mb-6 transform hover:scale-105 transition-transform duration-300">
            <h2 class="text-2xl font-semibold text-gray-700">
                <i class="fas fa-dollar-sign mr-2 text-emerald-600"></i> 
                Balance: <span class="text-emerald-600 font-bold">${{ number_format($balance ?? 0, 2) }}</span>
            </h2>
        </div>

        <div id="add-transaction" class="card bg-white p-6 rounded-xl shadow-2xl mb-6">
            <form id="transaction-form" action="{{ route('tracker.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-medium">
                            <i class="fas fa-coins mr-1 text-yellow-500"></i> Amount
                        </label>
                        <input type="number" name="amount" step="0.01" required
                               class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 transition-colors duration-200"
                               value="{{ old('amount') }}">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">
                            <i class="fas fa-exchange-alt mr-1 text-blue-500"></i> Type
                        </label>
                        <select name="type" id="type" required
                                class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 transition-colors duration-200">
                            <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">
                            <i class="fas fa-tag mr-1 text-purple-500"></i> Category
                        </label>
                        <input type="text" name="category" placeholder="e.g. Shopping"
                               class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 transition-colors duration-200"
                               value="{{ old('category') }}">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">
                            <i class="fas fa-credit-card mr-1 text-green-500"></i> Payment Type
                        </label>
                        <select name="payment_type" required
                                class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 transition-colors duration-200">
                            <option value="cash" {{ old('payment_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="card" {{ old('payment_type') == 'card' ? 'selected' : '' }}>Card</option>
                            <option value="bank_transfer" {{ old('payment_type') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">
                            <i class="fas fa-calendar-alt mr-1 text-red-500"></i> Date
                        </label>
                        <input type="date" name="date" required
                               class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 transition-colors duration-200"
                               value="{{ old('date') ?? date('Y-m-d') }}">
                    </div>
                </div>
                <div class="mt-6 flex space-x-4 justify-center">
                    <button type="submit"
                            class="btn btn-confirm bg-teal-600 text-white px-6 py-2 rounded-md hover:bg-teal-700 flex items-center transition-colors duration-200">
                        <i class="fas fa-check-circle mr-2"></i> Confirm
                    </button>
                </div>
            </form>
        </div>

        <div class="card bg-white p-6 rounded-xl shadow-2xl mb-6 transform hover:scale-105 transition-transform duration-300">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">
                <i class="fas fa-chart-bar mr-2 text-indigo-600"></i> Statistics
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-emerald-50 rounded-md hover:bg-emerald-100 transition-colors duration-200">
                    <p class="text-gray-700 font-medium">Daily</p>
                    <p>Income: <span class="font-semibold text-emerald-600">${{ number_format($stats['daily']['income'] ?? 0, 2) }}</span></p>
                    <p>Expense: <span class="font-semibold text-red-600">${{ number_format($stats['daily']['expense'] ?? 0, 2) }}</span></p>
                </div>
                <div class="p-4 bg-emerald-50 rounded-md hover:bg-emerald-100 transition-colors duration-200">
                    <p class="text-gray-700 font-medium">Weekly</p>
                    <p>Income: <span class="font-semibold text-emerald-600">${{ number_format($stats['weekly']['income'] ?? 0, 2) }}</span></p>
                    <p>Expense: <span class="font-semibold text-red-600">${{ number_format($stats['weekly']['expense'] ?? 0, 2) }}</span></p>
                </div>
                <div class="p-4 bg-emerald-50 rounded-md hover:bg-emerald-100 transition-colors duration-200">
                    <p class="text-gray-700 font-medium">Monthly</p>
                    <p>Income: <span class="font-semibold text-emerald-600">${{ number_format($stats['monthly']['income'] ?? 0, 2) }}</span></p>
                    <p>Expense: <span class="font-semibold text-red-600">${{ number_format($stats['monthly']['expense'] ?? 0, 2) }}</span></p>
                </div>
            </div>
        </div>

        <div id="transactions" class="card bg-white p-6 rounded-xl shadow-2xl">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">
                <i class="fas fa-list-ul mr-2 text-teal-600"></i> Transactions
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-emerald-50 p-4 rounded-lg shadow-md">
                    <h4 class="text-lg font-semibold text-emerald-700 mb-3">
                        <i class="fas fa-arrow-up mr-2 text-emerald-600"></i> Income
                    </h4>
                    @forelse ($transactions->where('type', 'income') as $transaction)
                        <div class="transaction-row bg-white p-3 mb-2 rounded-md shadow-sm hover:bg-emerald-100 transition-colors duration-200">
                            <p><strong>Amount:</strong> ${{ number_format($transaction->amount, 2) }}</p>
                            <p><strong>Category:</strong> {{ $transaction->category ?? '-' }}</p>
                            <p><strong>Payment:</strong> {{ ucfirst($transaction->payment_type) }}</p>
                            <p><strong>Date:</strong> {{ $transaction->date }}</p>
                            <button class="delete-btn text-red-600 hover:text-red-800 mt-2 flex items-center"
                                    data-id="{{ $transaction->id }}">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </div>
                    @empty
                        <p class="text-gray-500">No income transactions yet.</p>
                    @endforelse
                </div>
                <div class="bg-red-50 p-4 rounded-lg shadow-md">
                    <h4 class="text-lg font-semibold text-red-700 mb-3">
                        <i class="fas fa-arrow-down mr-2 text-red-600"></i> Expense
                    </h4>
                    @forelse ($transactions->where('type', 'expense') as $transaction)
                        <div class="transaction-row bg-white p-3 mb-2 rounded-md shadow-sm hover:bg-red-100 transition-colors duration-200">
                            <p><strong>Amount:</strong> ${{ number_format($transaction->amount, 2) }}</p>
                            <p><strong>Category:</strong> {{ $transaction->category ?? '-' }}</p>
                            <p><strong>Payment:</strong> {{ ucfirst($transaction->payment_type) }}</p>
                            <p><strong>Date:</strong> {{ $transaction->date }}</p>
                            <button class="delete-btn text-red-600 hover:text-red-800 mt-2 flex items-center"
                                    data-id="{{ $transaction->id }}">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </div>
                    @empty
                        <p class="text-gray-500">No expense transactions yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-teal-800 text-white py-4 mt-auto">
        <div class="container mx-auto px-6 text-center">
            <p class="text-sm">
                © 2025 CodeByAbduqodir. All rights reserved.
                <a href="https://github.com/CodeByAbduqodir/expense-tracker" target="_blank" class="hover:text-teal-300 transition-colors duration-200 ml-2">
                    <i class="fab fa-github mr-1"></i> GitHub
                </a>
            </p>
        </div>
    </footer>

    <script>
        document.getElementById('transaction-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to add transaction'));
                }
            })
            .catch(error => {
                alert('Error submitting transaction: ' + error.message);
                console.error(error);
            });
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this transaction?')) {
                    fetch(`/api/transactions/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.closest('.transaction-row').remove();
                            fetch('/api/balance')
                                .then(res => res.json())
                                .then(data => {
                                    document.querySelector('.text-emerald-600.font-bold').textContent = `$${data.balance.toFixed(2)}`;
                                });
                            alert('Transaction deleted successfully!');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Error deleting transaction: ' + error.message);
                        console.error(error);
                    });
                }
            });
        });
    </script>
</body>
</html>