<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Debtors Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .header {
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .date {
            color: #666;
            margin-top: 5px;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Debtors Report</h1>
        <div class="date">Generated on {{ date('F d, Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Customer</th>
                <th>Phone</th>
                <th>Credit Limit</th>
                <th class="text-right">Outstanding Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($debtors as $debtor)
            <tr>
                <td>{{ $debtor->name }}<br><small>{{ $debtor->email }}</small></td>
                <td>{{ $debtor->phone ?? '-' }}</td>
                <td>{{ $debtor->credit_limit > 0 ? currency($debtor->credit_limit) : 'No Limit' }}</td>
                <td class="text-right">{{ currency($debtor->current_balance) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right">Total Outstanding:</td>
                <td class="text-right">{{ currency($debtors->sum('current_balance')) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
