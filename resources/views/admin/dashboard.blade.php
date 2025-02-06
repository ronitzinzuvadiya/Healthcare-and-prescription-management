<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white white:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        {{ __("You're logged in as Admin!")}}
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6 p-6">
                        <div class="bg-blue-500 text-gray p-6 rounded-lg shadow-lg">
                            <a href="{{ route('doctor.list') }}" class="block">
                                <h3 class="text-2xl font-semibold">Total Doctors</h3>
                                <p class="text-4xl font-bold">{{ $doctorCount }}</p>
                            </a>
                        </div>

                        <div class="bg-green-500 text-gray p-6 rounded-lg shadow-lg">
                            <a href="{{ route('patient.list') }}" class="block">
                                <h3 class="text-2xl font-semibold">Total Patients</h3>
                                <p class="text-4xl font-bold">{{ $patientCount }}</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
</x-app-layout>