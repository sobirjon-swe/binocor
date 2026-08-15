<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $project->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 grid grid-cols-2 gap-4">
                <div><span class="text-gray-500 text-sm">Manzil:</span> {{ $project->address }}</div>
                <div><span class="text-gray-500 text-sm">Boshlanish sanasi:</span> {{ optional($project->start_date)->format('Y-m-d') }}</div>
                <div><span class="text-gray-500 text-sm">Holat:</span> {{ $project->status }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b font-medium text-gray-700">Obyektlar</div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Turi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Maydon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Narx</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Holat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($project->properties as $property)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $property->type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $property->area }} m²</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($property->price, 0, '.', ' ') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $property->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Obyektlar yo'q.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
