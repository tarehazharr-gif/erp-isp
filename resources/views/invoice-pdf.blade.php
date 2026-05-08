<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 20px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .total { font-weight: bold; background: #eee; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE TAGIHAN INTERNET</h1>
    </div>

    <div class="info">
        <p>Nomor Invoice: {{ $record->invoice_number }}</p>
        <p>Nama Pelanggan: {{ $record->customer->name }}</p>
        <p>Jatuh Tempo: {{ $record->due_date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Keterangan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Layanan Internet ISP</td>
                <td>Rp {{ number_format($record->amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td>Total Bayar</td>
                <td>Rp {{ number_format($record->amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <p>Status: <strong>{{ strtoupper($record->status) }}</strong></p>
</body>
</html>