<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Shartnoma — {{ $contract->customer->full_name }}</h2>
            <a href="{{ route('contracts.pdf', $contract) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                PDF yuklab olish
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 grid grid-cols-2 gap-4">
                <div><span class="text-gray-500 text-sm">Mijoz:</span> <a href="{{ route('customers.show', $contract->customer) }}" class="hover:underline">{{ $contract->customer->full_name }}</a></div>
                <div><span class="text-gray-500 text-sm">Obyekt:</span> <a href="{{ route('properties.show', $contract->property) }}" class="hover:underline">{{ $contract->property->type }} — {{ $contract->property->project->name }}</a></div>
                <div><span class="text-gray-500 text-sm">Umumiy narx:</span> {{ number_format($contract->total_price, 0, '.', ' ') }}</div>
                <div><span class="text-gray-500 text-sm">To'lov turi:</span> {{ $contract->payment_type }}</div>
                <div><span class="text-gray-500 text-sm">Sana:</span> {{ optional($contract->signed_date)->format('Y-m-d') }}</div>
                <div><span class="text-gray-500 text-sm">Holat:</span> {{ $contract->status }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b font-medium text-gray-700 flex justify-between items-center">
                    To'lov jadvali
                    <a href="{{ route('payments.create') }}" class="text-sm text-indigo-600 hover:underline">+ To'lov qo'shish</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Summa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Muddat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">To'langan sana</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Holat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($contract->payments as $payment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($payment->amount, 0, '.', ' ') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ optional($payment->due_date)->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ optional($payment->paid_date)->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $payment->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">To'lovlar yo'q.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
