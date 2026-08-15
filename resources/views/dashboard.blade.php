<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">Loyihalar</div>
                    <div class="text-2xl font-semibold text-gray-900">{{ $stats['projects_count'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">Obyektlar (jami / sotilgan / bo'sh)</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ $stats['properties_count'] }} / {{ $stats['properties_sold'] }} / {{ $stats['properties_available'] }}
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">Mijozlar</div>
                    <div class="text-2xl font-semibold text-gray-900">{{ $stats['customers_count'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">Faol shartnomalar</div>
                    <div class="text-2xl font-semibold text-gray-900">{{ $stats['contracts_count'] }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">Umumiy sotuv</div>
                    <div class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_sales'], 0, '.', ' ') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">Tushgan pul</div>
                    <div class="text-2xl font-semibold text-green-600">{{ number_format($stats['collected'], 0, '.', ' ') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">Qolgan qarz</div>
                    <div class="text-2xl font-semibold text-amber-600">{{ number_format($stats['outstanding'], 0, '.', ' ') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">Kechikkan to'lovlar</div>
                    <div class="text-2xl font-semibold text-red-600">{{ $stats['overdue_count'] }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
