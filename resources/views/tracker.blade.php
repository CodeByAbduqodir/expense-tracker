<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-6 max-w-4xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Expense Tracker</h1>

        <div class="card bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-2xl font-semibold text-gray-700">Баланс: <span class="text-emerald-600">${{ number_format($balance, 2) }}</span></h2>
        </div>

        <div class="card bg-white p-6 rounded-lg shadow-lg mb-6">
            <form id="transaction-form" action="{{ route('tracker.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700">Сумма</label>
                        <input type="number" name="amount" step="0.01" required
                               class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-gray-700">Тип</label>
                        <select name="type" id="type" required
                                class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="income">+</option>
                            <option value="expense">-</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700">Категория</label>
                        <input type="text" name="category" placeholder="Например, Покупки"
                               class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-gray-700">Тип оплаты</label>
                        <input type="text" name="payment_type" placeholder="Например, Наличные"
                               class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-gray-700">Дата</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                               class="w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
                <div id="sub-transactions" class="sub-transaction mt-4">
                    <h4 class="text-gray-700 mb-2">Разбить расход</h4>
                    <div id="sub-transaction-fields">
                        <div class="grid grid-cols-2 gap-4 mb-2">
                            <input type="number" name="sub_amounts[]" step="0.01" placeholder="Сумма"
                                   class="p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <input type="text" name="sub_categories[]" placeholder="Категория, например, Молоко"
                                   class="p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>
                    <button type="button" id="add-sub-transaction"
                            class="text-emerald-600 hover:text-emerald-800">+ Добавить подкатегорию</button>
                </div>
                <div class="mt-4 flex space-x-4">
                    <button type="submit" name="type" value="income"
                            class="btn btn-income text-white px-4 py-2 rounded-md">Добавить доход (+)</button>
                    <button type="submit" name="type" value="expense"
                            class="btn btn-expense text-white px-4 py-2 rounded-md">Добавить расход (-)</button>
                </div>
            </form>
            @if (session('success'))
                <div class="mt-4 text-emerald-600 font-semibold">{{ session('success') }}</div>
            @endif
        </div>

        <div class="card bg-white p-6 rounded-lg shadow-lg mb-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Статистика</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-emerald-100 rounded-md">
                    <p class="text-gray-700">Ежедневно</p>
                    <p>Доходы: <span class="font-semibold text-emerald-600">${{ number_format($daily['income'], 2) }}</span></p>
                    <p>Расходы: <span class="font-semibold text-red-600">${{ number_format($daily['expense'], 2) }}</span></p>
                </div>
                <div class="p-4 bg-emerald-100 rounded-md">
                    <p class="text-gray-700">Еженедельно</p>
                    <p>Доходы: <span class="font-semibold text-emerald-600">${{ number_format($weekly['income'], 2) }}</span></p>
                    <p>Расходы: <span class="font-semibold text-red-600">${{ number_format($weekly['expense'], 2) }}</span></p>
                </div>
                <div class="p-4 bg-emerald-100 rounded-md">
                    <p class="text-gray-700">Ежемесячно</p>
                    <p>Доходы: <span class="font-semibold text-emerald-600">${{ number_format($monthly['income'], 2) }}</span></p>
                    <p>Расходы: <span class="font-semibold text-red-600">${{ number_format($monthly['expense'], 2) }}</span></p>
                </div>
            </div>
        </div>

        <!-- Список транзакций -->
        <div class="card bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Транзакции</h3>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-3 text-left">Сумма</th>
                        <th class="p-3 text-left">Тип</th>
                        <th class="p-3 text-left">Категория</th>
                        <th class="p-3 text-left">Тип оплаты</th>
                        <th class="p-3 text-left">Дата</th>
                        <th class="p-3 text-left">Подкатегории</th>
                    </tr>
                </thead>
                <tbody id="transactions-table">
                    @foreach ($transactions as $transaction)
                        <tr class="transaction-row">
                            <td class="p-3">${{ number_format($transaction->amount, 2) }}</td>
                            <td class="p-3">{{ $transaction->type == 'income' ? '+' : '-' }}</td>
                            <td class="p-3">{{ $transaction->category ?? '-' }}</td>
                            <td class="p-3">{{ $transaction->payment_type ?? '-' }}</td>
                            <td class="p-3">{{ $transaction->date }}</td>
                            <td class="p-3">
                                @if ($transaction->subTransactions)
                                    <ul>
                                        @foreach ($transaction->subTransactions as $sub)
                                            <li>{{ $sub->category }}: ${{ number_format($sub->amount, 2) }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('transaction-form').addEventListener('submit', function (e) {
            const buttons = document.querySelectorAll('button[type="submit"]');
            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    document.querySelector('select[name="type"]').value = this.value;
                });
            });

            setTimeout(() => {
                const table = document.getElementById('transactions-table');
                const newRow = table.querySelector('tr:first-child');
                if (newRow) {
                    newRow.classList.add('transaction-row');
                }
            }, 1000);
        });

        const typeSelect = document.getElementById('type');
        const subTransactions = document.getElementById('sub-transactions');
        typeSelect.addEventListener('change', function () {
            subTransactions.classList.toggle('active', this.value === 'expense');
        });

        document.getElementById('add-sub-transaction').addEventListener('click', function () {
            const container = document.getElementById('sub-transaction-fields');
            const newFields = document.createElement('div');
            newFields.className = 'grid grid-cols-2 gap-4 mb-2';
            newFields.innerHTML = `
                <input type="number" name="sub_amounts[]" step="0.01" placeholder="Сумма"
                       class="p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <input type="text" name="sub_categories[]" placeholder="Категория, например, Молоко"
                       class="p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
            `;
            container.appendChild(newFields);
        });
    </script>
</body>
</html>
```