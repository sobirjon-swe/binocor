<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">To'lovlar</h2>
            <a href="{{ route('payments.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                + To'lov qo'shish
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mijoz</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Obyekt</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Summa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Muddat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Holat</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($payments as $payment)
                            @php $isOverdue = $payment->status === 'pending' && $payment->due_date && $payment->due_date->isPast(); @endphp
                            <tr @class(['bg-red-50' => $isOverdue])>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $payment->contract->customer->full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $payment->contract->property->type }} ({{ $payment->contract->property->project->name }})</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($payment->amount, 0, '.', ' ') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ optional($payment->due_date)->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span @class([
                                        'px-2 py-1 text-xs rounded-full',
                                        'bg-amber-100 text-amber-800' => $payment->status === 'pending' && ! $isOverdue,
                                        'bg-red-100 text-red-800' => $isOverdue || $payment->status === 'overdue',
                                        'bg-green-100 text-green-800' => $payment->status === 'paid',
                                    ])>{{ $isOverdue ? 'kechikkan' : $payment->status }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                    @if ($payment->status !== 'paid')
                                        <a href="{{ URL::signedRoute('payments.pay', ['payment' => $payment, 'provider' => 'payme']) }}" target="_blank" class="text-teal-600 hover:underline">Payme havolasi</a>
                                        <a href="{{ URL::signedRoute('payments.pay', ['payment' => $payment, 'provider' => 'click']) }}" target="_blank" class="text-sky-600 hover:underline">Click havolasi</a>
                                    @endif
                                    <a href="{{ route('payments.edit', $payment) }}" class="text-indigo-600 hover:underline">Tahrirlash</a>
                                    <form method="POST" action="{{ route('payments.destroy', $payment) }}" class="inline" onsubmit="return confirm('O\'chirishni tasdiqlaysizmi?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">O'chirish</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">To'lovlar topilmadi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
            <div class="mt-4">{{ $payments->links() }}</div>
        </div>
    </div>
</x-app-layout>
