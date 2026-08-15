<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Obyektlar</h2>
            @if (Auth::user()->hasAnyRole(['admin', 'manager']))
                <a href="{{ route('properties.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    + Obyekt qo'shish
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loyiha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Turi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Maydon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qavat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Narx</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Holat</th>
                            @if (Auth::user()->hasAnyRole(['admin', 'manager']))
                                <th class="px-6 py-3"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($properties as $property)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('properties.show', $property) }}" class="text-gray-900 font-medium hover:underline">{{ $property->project->name }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $property->type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $property->area }} m²</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $property->floor }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($property->price, 0, '.', ' ') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span @class([
                                        'px-2 py-1 text-xs rounded-full',
                                        'bg-green-100 text-green-800' => $property->status === 'available',
                                        'bg-amber-100 text-amber-800' => $property->status === 'reserved',
                                        'bg-gray-200 text-gray-800' => $property->status === 'sold',
                                    ])>{{ $property->status }}</span>
                                </td>
                                @if (Auth::user()->hasAnyRole(['admin', 'manager']))
                                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                        <a href="{{ route('properties.edit', $property) }}" class="text-indigo-600 hover:underline">Tahrirlash</a>
                                        <form method="POST" action="{{ route('properties.destroy', $property) }}" class="inline" onsubmit="return confirm('O\'chirishni tasdiqlaysizmi?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">O'chirish</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Obyektlar topilmadi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
            <div class="mt-4">{{ $properties->links() }}</div>
        </div>
    </div>
</x-app-layout>
