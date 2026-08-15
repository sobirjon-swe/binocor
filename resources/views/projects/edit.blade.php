<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Loyihani tahrirlash</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('projects.partials.form')
                    <div class="flex justify-end">
                        <x-primary-button>Yangilash</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
