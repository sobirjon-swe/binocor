<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <title>Shartnoma №{{ $contract->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { font-size: 18px; text-align: center; margin-bottom: 4px; }
        .subtitle { text-align: center; color: #6b7280; margin-bottom: 24px; }
        h2 { font-size: 13px; margin-top: 20px; margin-bottom: 6px; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        td, th { padding: 5px 6px; text-align: left; vertical-align: top; }
        .info-table td:first-child { color: #6b7280; width: 40%; }
        .schedule-table th { background-color: #f3f4f6; border-bottom: 1px solid #d1d5db; }
        .schedule-table td { border-bottom: 1px solid #e5e7eb; }
        .signatures { margin-top: 50px; width: 100%; }
        .signatures td { width: 50%; padding-top: 40px; }
        .sig-line { border-top: 1px solid #1f2937; padding-top: 4px; width: 70%; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h1>SHARTNOMA №{{ $contract->id }}</h1>
    <div class="subtitle">Tuzilgan sana: {{ $contract->signed_date->format('d.m.Y') }}</div>

    <h2>1. TOMONLAR</h2>
    <table class="info-table">
        <tr><td>Sotuvchi</td><td>{{ config('app.name') }}</td></tr>
        <tr><td>Xaridor (F.I.Sh.)</td><td>{{ $contract->customer->full_name }}</td></tr>
        <tr><td>Pasport</td><td>{{ $contract->customer->passport_number ?: '—' }}</td></tr>
        <tr><td>Telefon</td><td>{{ $contract->customer->phone }}</td></tr>
        <tr><td>Manzil</td><td>{{ $contract->customer->address ?: '—' }}</td></tr>
    </table>

    <h2>2. SHARTNOMA PREDMETI</h2>
    <table class="info-table">
        <tr><td>Loyiha</td><td>{{ $contract->property->project->name }}</td></tr>
        <tr><td>Loyiha manzili</td><td>{{ $contract->property->project->address ?: '—' }}</td></tr>
        <tr><td>Obyekt turi</td><td>{{ ['apartment' => 'Kvartira', 'office' => 'Ofis', 'land' => 'Uchastka'][$contract->property->type] ?? $contract->property->type }}</td></tr>
        <tr><td>Maydon</td><td>{{ $contract->property->area }} m²</td></tr>
        <tr><td>Qavat</td><td>{{ $contract->property->floor ?? '—' }}</td></tr>
        <tr><td>Xonalar soni</td><td>{{ $contract->property->rooms_count ?? '—' }}</td></tr>
    </table>

    <h2>3. NARX VA TO'LOV SHARTLARI</h2>
    <table class="info-table">
        <tr><td>Umumiy narx</td><td>{{ number_format($contract->total_price, 0, '.', ' ') }} so'm</td></tr>
        <tr><td>To'lov turi</td><td>{{ $contract->payment_type === 'cash' ? 'Naqd' : 'Rassrochka' }}</td></tr>
        @if ($contract->payment_type === 'installment')
            <tr><td>Boshlang'ich to'lov</td><td>{{ $contract->down_payment ? number_format($contract->down_payment, 0, '.', ' ').' so\'m' : '—' }}</td></tr>
            <tr><td>Muddat</td><td>{{ $contract->installment_months }} oy</td></tr>
        @endif
    </table>

    <h2>4. TO'LOV JADVALI</h2>
    <table class="schedule-table">
        <thead>
            <tr>
                <th>№</th>
                <th>Muddat</th>
                <th class="text-right">Summa</th>
                <th>Holat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($contract->payments as $index => $payment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $payment->due_date->format('d.m.Y') }}</td>
                    <td class="text-right">{{ number_format($payment->amount, 0, '.', ' ') }} so'm</td>
                    <td>{{ ['pending' => 'Kutilmoqda', 'paid' => 'To\'langan', 'overdue' => 'Kechikkan'][$payment->status] ?? $payment->status }}</td>
                </tr>
            @empty
                <tr><td colspan="4">To'lov jadvali kiritilmagan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="signatures">
        <tr>
            <td>
                <div class="sig-line">Sotuvchi vakili: _________________________</div>
            </td>
            <td>
                <div class="sig-line">Xaridor: {{ $contract->customer->full_name }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
