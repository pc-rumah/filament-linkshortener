<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $transaction->order_id }}</title>
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
        }

        .header {
            margin-bottom: 20px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background: #f5f5f5;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="title">INVOICE</div>
        <p>
            <strong>Invoice ID:</strong> {{ $transaction->order_id }}<br>
            <strong>Tanggal:</strong> {{ $transaction->created_at->format('d M Y') }}
        </p>
    </div>

    <p>
        <strong>Kepada:</strong><br>
        {{ $transaction->user->name }}<br>
        {{ $transaction->user->email }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Langganan Plan {{ ucfirst($transaction->plan) }} (1 Bulan)</td>
                <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top:20px;">
        <strong>Total:</strong> Rp {{ number_format($transaction->amount, 0, ',', '.') }}
    </p>

    <p>Status: <strong>PAID</strong></p>

</body>

</html>
