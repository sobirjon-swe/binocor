<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $stage->project->name }} — {{ $stage->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="flex-1 bg-gray-200 rounded-full h-3">
                        <div class="bg-indigo-600 h-3 rounded-full" style="width: {{ $stage->progress_percent }}%"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ $stage->progress_percent }}%</span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Reja qilingan sana:</span> {{ optional($stage->planned_date)->format('Y-m-d') ?? '—' }}</div>
                    <div><span class="text-gray-500">Bajarilgan sana:</span> {{ optional($stage->actual_date)->format('Y-m-d') ?? '—' }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-700 mb-4">Foto hisobot qo'shish</h3>
                <form method="POST" action="{{ route('construction-stages.photos.store', $stage) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="photo" value="Rasm" />
                        <input id="photo" name="photo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-700" required />
                        <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="note" value="Izoh (ixtiyoriy)" />
                        <x-text-input id="note" name="note" type="text" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('note')" class="mt-2" />
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Yuklash</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-700 mb-4">Foto hisobotlar tarixi</h3>
                @if ($stage->photos->isEmpty())
                    <p class="text-sm text-gray-500">Hozircha foto hisobot yo'q.</p>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach ($stage->photos as $photo)
                            <div class="relative group">
                                <a href="{{ $photo->url }}" target="_blank">
                                    <img src="{{ $photo->url }}" alt="{{ $photo->note }}" class="w-full h-40 object-cover rounded-md border border-gray-200">
                                </a>
                                <div class="mt-1 text-xs text-gray-500">
                                    {{ $photo->created_at->format('Y-m-d H:i') }}
                                    @if ($photo->user)
                                        — {{ $photo->user->name }}
                                    @endif
                                </div>
                                @if ($photo->note)
                                    <div class="text-xs text-gray-700">{{ $photo->note }}</div>
                                @endif
                                <form method="POST" action="{{ route('construction-stages.photos.destroy', [$stage, $photo]) }}" onsubmit="return confirm('O\'chirishni tasdiqlaysizmi?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="mt-1 text-xs text-red-600 hover:underline">O'chirish</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
