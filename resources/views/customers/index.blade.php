<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mijozlar</h2>
            <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                + Mijoz qo'shish
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ism</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bosqich</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('customers.show', $customer) }}" class="text-gray-900 font-medium hover:underline">{{ $customer->full_name }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $customer->phone }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $customer->lead_status }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                    @can('update', $customer)
                                        <a href="{{ route('customers.edit', $customer) }}" class="text-indigo-600 hover:underline">Tahrirlash</a>
                                    @endcan
                                    @can('delete', $customer)
                                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline" onsubmit="return confirm('O\'chirishni tasdiqlaysizmi?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">O'chirish</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Mijozlar topilmadi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
            <div class="mt-4">{{ $customers->links() }}</div>
        </div>
    </div>
</x-app-layout>
