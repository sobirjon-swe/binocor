<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Qurilish jarayoni</h2>
            <a href="{{ route('construction-stages.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                + Bosqich qo'shish
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loyiha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bosqich</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reja sanasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bajarilgan sana</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($stages as $stage)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $stage->project->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('construction-stages.show', $stage) }}" class="text-gray-900 font-medium hover:underline">{{ $stage->name }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap w-48">
                                    <div class="flex items-center gap-2">
                                        <div class="w-32 bg-gray-200 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $stage->progress_percent }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-600">{{ $stage->progress_percent }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ optional($stage->planned_date)->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ optional($stage->actual_date)->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                    <a href="{{ route('construction-stages.edit', $stage) }}" class="text-indigo-600 hover:underline">Tahrirlash</a>
                                    <form method="POST" action="{{ route('construction-stages.destroy', $stage) }}" class="inline" onsubmit="return confirm('O\'chirishni tasdiqlaysizmi?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">O'chirish</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Qurilish bosqichlari topilmadi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
            <div class="mt-4">{{ $stages->links() }}</div>
        </div>
    </div>
</x-app-layout>
