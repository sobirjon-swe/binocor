<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Shartnomalar</h2>
            <a href="{{ route('contracts.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                + Shartnoma qo'shish
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Narx</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">To'lov turi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Holat</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($contracts as $contract)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('contracts.show', $contract) }}" class="text-gray-900 font-medium hover:underline">{{ $contract->customer->full_name }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $contract->property->type }} ({{ $contract->property->project->name }})</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($contract->total_price, 0, '.', ' ') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $contract->payment_type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $contract->status }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                    @can('update', $contract)
                                        <a href="{{ route('contracts.edit', $contract) }}" class="text-indigo-600 hover:underline">Tahrirlash</a>
                                    @endcan
                                    @can('delete', $contract)
                                        <form method="POST" action="{{ route('contracts.destroy', $contract) }}" class="inline" onsubmit="return confirm('O\'chirishni tasdiqlaysizmi?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">O'chirish</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Shartnomalar topilmadi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
            <div class="mt-4">{{ $contracts->links() }}</div>
        </div>
    </div>
</x-app-layout>
