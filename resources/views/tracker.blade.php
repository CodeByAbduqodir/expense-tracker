```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: auto; }
        .balance { font-size: 24px; margin-bottom: 20px; }
        .form-container { margin-bottom: 20px; }
        .stats { margin-top: 20px; }
        .transactions { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Expense Tracker</h1>
        
        <div class="balance">Баланс: ${{ number_format($balance, 2) }}</div>

        <div class="form-container">
            <form action="{{ route('tracker.store') }}" method="POST">
                @csrf
                <label>Сумма:</label>
                <input type="number" name="amount" step="0.01" required>
                
                <label>Тип:</label>
                <select name="type" required>
                    <option value="income">+</option>
                    <option value="expense">-</option>
                </select>
                
                <label>Категория:</label>
                <input type="text" name="category" placeholder="Например, Молоко">
                
                <label>Тип оплаты:</label>
                <input type="text" name="payment_type" placeholder="Например, Наличные">
                
                <label>Дата:</label>
                <input type="date" name="date" required value="{{ date('Y-m-d') }}">
                
                <button type="submit">Добавить</button>
            </form>
            @if (session('success'))
                <div class="success">{{ session('success') }}</div>
            @endif
        </div>

        <div class="stats">
            <h3>Статистика</h3>
            <p>Ежедневно: Доходы ${{ number_format($daily['income'], 2) }} | Расходы ${{ number_format($daily['expense'], 2) }}</p>
            <p>Еженедельно: Доходы ${{ number_format($weekly['income'], 2) }} | Расходы ${{ number_format($weekly['expense'], 2) }}</p>
            <p>Ежемесячно: Доходы ${{ number_format($monthly['income'], 2) }} | Расходы ${{ number_format($monthly['expense'], 2) }}</p>
        </div>

        <div class="transactions">
            <h3>Транзакции</h3>
            <table>
                <tr>
                    <th>Сумма</th>
                    <th>Тип</th>
                    <th>Категория</th>
                    <th>Тип оплаты</th>
                    <th>Дата</th>
                </tr>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>${{ number_format($transaction->amount, 2) }}</td>
                        <td>{{ $transaction->type == 'income' ? '+' : '-' }}</td>
                        <td>{{ $transaction->category ?? '-' }}</td>
                        <td>{{ $transaction->payment_type ?? '-' }}</td>
                        <td>{{ $transaction->date }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</body>
</html>
```