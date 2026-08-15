<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Hisobotlar</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <h3 class="font-medium text-gray-700 mb-4">Oylik sotuv dinamikasi (so'nggi 12 oy)</h3>
                    <canvas id="salesChart" height="220" role="img" aria-label="Oylik sotuv dinamikasi grafigi"></canvas>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <h3 class="font-medium text-gray-700 mb-4">Yig'ilgan to'lovlar (so'nggi 12 oy)</h3>
                    <canvas id="collectedChart" height="220" role="img" aria-label="Yig'ilgan to'lovlar grafigi"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <h3 class="font-medium text-gray-700 mb-4">Obyektlar holati</h3>
                    <canvas id="statusChart" height="220" role="img" aria-label="Obyektlar holati taqsimoti grafigi"></canvas>
                    <ul class="mt-4 space-y-1 text-sm">
                        <li class="flex justify-between"><span><span class="inline-block w-2.5 h-2.5 rounded-full bg-[#16a34a] mr-2"></span>Bo'sh</span><span class="font-medium">{{ $propertyStatus['available'] }}</span></li>
                        <li class="flex justify-between"><span><span class="inline-block w-2.5 h-2.5 rounded-full bg-[#d97706] mr-2"></span>Band</span><span class="font-medium">{{ $propertyStatus['reserved'] }}</span></li>
                        <li class="flex justify-between"><span><span class="inline-block w-2.5 h-2.5 rounded-full bg-[#6b7280] mr-2"></span>Sotilgan</span><span class="font-medium">{{ $propertyStatus['sold'] }}</span></li>
                    </ul>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 lg:col-span-2">
                    <h3 class="font-medium text-gray-700 mb-4">Loyihalar bo'yicha eng ko'p sotuv</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Loyiha</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Shartnomalar</th>
                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase">Jami summa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($topProjects as $project)
                                <tr>
                                    <td class="px-2 py-2 text-sm text-gray-900">{{ $project['name'] }}</td>
                                    <td class="px-2 py-2 text-sm text-gray-600">{{ $project['contracts_count'] }}</td>
                                    <td class="px-2 py-2 text-sm text-gray-900 text-right">{{ number_format($project['total'], 0, '.', ' ') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-2 py-4 text-center text-gray-500 text-sm">Ma'lumot yo'q.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="font-medium text-gray-700 mb-4">To'lovlar holati bo'yicha taqsimot</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="border border-amber-200 bg-amber-50 rounded-lg p-4">
                        <div class="text-sm text-amber-800">Kutilmoqda</div>
                        <div class="text-xl font-semibold text-amber-900">{{ $paymentStatus['pending']['count'] }} ta</div>
                        <div class="text-sm text-amber-700">{{ number_format($paymentStatus['pending']['total'], 0, '.', ' ') }} so'm</div>
                    </div>
                    <div class="border border-red-200 bg-red-50 rounded-lg p-4">
                        <div class="text-sm text-red-800">Kechikkan</div>
                        <div class="text-xl font-semibold text-red-900">{{ $paymentStatus['overdue']['count'] }} ta</div>
                        <div class="text-sm text-red-700">{{ number_format($paymentStatus['overdue']['total'], 0, '.', ' ') }} so'm</div>
                    </div>
                    <div class="border border-green-200 bg-green-50 rounded-lg p-4">
                        <div class="text-sm text-green-800">To'langan</div>
                        <div class="text-xl font-semibold text-green-900">{{ $paymentStatus['paid']['count'] }} ta</div>
                        <div class="text-sm text-green-700">{{ number_format($paymentStatus['paid']['total'], 0, '.', ' ') }} so'm</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const salesData = @json($monthlySales);
            const collectedData = @json($monthlyCollected);
            const statusData = @json($propertyStatus);

            const gridColor = '#e1e0d9';
            const tickColor = '#898781';

            new Chart(document.getElementById('salesChart'), {
                type: 'bar',
                data: {
                    labels: salesData.map(d => d.label),
                    datasets: [{
                        label: "Sotuv summasi (so'm)",
                        data: salesData.map(d => d.total),
                        backgroundColor: '#2a78d6',
                        borderRadius: 4,
                        maxBarThickness: 28,
                    }],
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: tickColor } },
                        y: { grid: { color: gridColor }, ticks: { color: tickColor, callback: v => new Intl.NumberFormat('uz-UZ').format(v) } },
                    },
                },
            });

            new Chart(document.getElementById('collectedChart'), {
                type: 'bar',
                data: {
                    labels: collectedData.map(d => d.label),
                    datasets: [{
                        label: "Yig'ilgan summa (so'm)",
                        data: collectedData.map(d => d.total),
                        backgroundColor: '#1baf7a',
                        borderRadius: 4,
                        maxBarThickness: 28,
                    }],
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: tickColor } },
                        y: { grid: { color: gridColor }, ticks: { color: tickColor, callback: v => new Intl.NumberFormat('uz-UZ').format(v) } },
                    },
                },
            });

            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: ["Bo'sh", 'Band', 'Sotilgan'],
                    datasets: [{
                        data: [statusData.available, statusData.reserved, statusData.sold],
                        backgroundColor: ['#16a34a', '#d97706', '#6b7280'],
                        borderColor: '#fcfcfb',
                        borderWidth: 2,
                    }],
                },
                options: {
                    plugins: { legend: { display: false } },
                },
            });
        });
    </script>
    @endpush
</x-app-layout>
