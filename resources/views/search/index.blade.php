<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Qidiruv natijalari — "{{ $query }}"</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if ($query === '')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-500">
                    Mijoz, shartnoma yoki obyektni ism, telefon yoki loyiha nomi bo'yicha qidirish uchun yuqoridagi maydonga yozing.
                </div>
            @elseif ($customers->isEmpty() && $contracts->isEmpty() && $properties->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-500">
                    Hech narsa topilmadi.
                </div>
            @else
                @if ($customers->isNotEmpty())
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b font-medium text-gray-700">Mijozlar</div>
                        <div class="divide-y divide-gray-200">
                            @foreach ($customers as $customer)
                                <a href="{{ route('customers.show', $customer) }}" class="block px-6 py-3 hover:bg-gray-50">
                                    <span class="font-medium text-gray-900">{{ $customer->full_name }}</span>
                                    <span class="text-sm text-gray-500 ms-2">{{ $customer->phone }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($contracts->isNotEmpty())
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b font-medium text-gray-700">Shartnomalar</div>
                        <div class="divide-y divide-gray-200">
                            @foreach ($contracts as $contract)
                                <a href="{{ route('contracts.show', $contract) }}" class="block px-6 py-3 hover:bg-gray-50">
                                    <span class="font-medium text-gray-900">{{ $contract->customer->full_name }}</span>
                                    <span class="text-sm text-gray-500 ms-2">{{ $contract->property->type }} — {{ $contract->property->project->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($properties->isNotEmpty())
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b font-medium text-gray-700">Obyektlar</div>
                        <div class="divide-y divide-gray-200">
                            @foreach ($properties as $property)
                                <a href="{{ route('properties.show', $property) }}" class="block px-6 py-3 hover:bg-gray-50">
                                    <span class="font-medium text-gray-900">{{ $property->project->name }}</span>
                                    <span class="text-sm text-gray-500 ms-2">{{ $property->type }}, {{ $property->area }} m²</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
