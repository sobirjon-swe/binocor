<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $property->project->name }} — {{ $property->type }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 grid grid-cols-2 gap-4">
                <div><span class="text-gray-500 text-sm">Maydon:</span> {{ $property->area }} m²</div>
                <div><span class="text-gray-500 text-sm">Qavat:</span> {{ $property->floor }}</div>
                <div><span class="text-gray-500 text-sm">Xonalar soni:</span> {{ $property->rooms_count }}</div>
                <div><span class="text-gray-500 text-sm">Narx:</span> {{ number_format($property->price, 0, '.', ' ') }}</div>
                <div><span class="text-gray-500 text-sm">Holat:</span> {{ $property->status }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b font-medium text-gray-700">Shartnomalar</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mijoz</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Narx</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Holat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($property->contracts as $contract)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $contract->customer->full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($contract->total_price, 0, '.', ' ') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $contract->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">Shartnomalar yo'q.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
