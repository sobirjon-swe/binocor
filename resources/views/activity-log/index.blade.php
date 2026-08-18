<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Faoliyat tarixi</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vaqt</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kim</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nima</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">O'zgarish</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($activities as $activity)
                                @php
                                    $labels = ['App\\Models\\Contract' => 'Shartnoma', 'App\\Models\\Payment' => 'To\'lov', 'App\\Models\\Property' => 'Obyekt'];
                                    $subjectLabel = $labels[$activity->subject_type] ?? $activity->subject_type;
                                    $old = $activity->attribute_changes['old'] ?? [];
                                    $new = $activity->attribute_changes['attributes'] ?? [];
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $activity->causer?->name ?? 'Tizim' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $subjectLabel }} @if ($activity->subject) #{{ $activity->subject->id }} @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        @forelse ($new as $field => $value)
                                            <div>
                                                <span class="text-gray-500">{{ $field }}:</span>
                                                @if (array_key_exists($field, $old))
                                                    {{ $old[$field] }} → {{ $value }}
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </div>
                                        @empty
                                            <span class="text-gray-400">—</span>
                                        @endforelse
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Hozircha yozuv yo'q.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">{{ $activities->links() }}</div>
        </div>
    </div>
</x-app-layout>
