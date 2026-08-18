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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-700 mb-4">Rasmlar</h3>

                @if ($property->photos->isEmpty())
                    <p class="text-sm text-gray-500 mb-4">Hozircha rasm yo'q.</p>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                        @foreach ($property->photos as $photo)
                            <div class="relative group">
                                <a href="{{ $photo->url }}" target="_blank">
                                    <img src="{{ $photo->url }}" alt="Obyekt rasmi" class="w-full h-32 object-cover rounded-md border border-gray-200">
                                </a>
                                @if ($photo->is_primary)
                                    <span class="absolute top-1 left-1 px-2 py-0.5 text-xs rounded-full bg-indigo-600 text-white">Asosiy</span>
                                @endif
                                @if (Auth::user()->hasAnyRole(['admin', 'manager']))
                                    <form method="POST" action="{{ route('properties.photos.destroy', [$property, $photo]) }}" onsubmit="return confirm('O\'chirishni tasdiqlaysizmi?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="mt-1 text-xs text-red-600 hover:underline">O'chirish</button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (Auth::user()->hasAnyRole(['admin', 'manager']))
                    <form method="POST" action="{{ route('properties.photos.store', $property) }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-4">
                        @csrf
                        <div>
                            <x-input-label for="photo" value="Yangi rasm" />
                            <input id="photo" name="photo" type="file" accept="image/*" class="mt-1 block text-sm text-gray-700" required />
                        </div>
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="is_primary" value="1" class="rounded border-gray-300">
                            Asosiy rasm sifatida belgilash
                        </label>
                        <x-primary-button>Yuklash</x-primary-button>
                    </form>
                    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                @endif
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
