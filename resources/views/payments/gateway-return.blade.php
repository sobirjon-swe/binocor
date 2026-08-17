<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>To'lov holati — Binocor</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-sm p-8 text-center">
        <h1 class="text-xl font-semibold text-gray-800 mb-2">Rahmat!</h1>
        @if ($payment->status === 'paid')
            <p class="text-green-700">To'lovingiz muvaffaqiyatli qabul qilindi.</p>
        @else
            <p class="text-gray-600">To'lovingiz ishlanmoqda. Bir necha daqiqadan so'ng holatni tekshirib ko'ring.</p>
        @endif
        <p class="mt-4 text-sm text-gray-400">Summa: {{ number_format($payment->amount, 0, '.', ' ') }} so'm</p>
    </div>
</body>
</html>
