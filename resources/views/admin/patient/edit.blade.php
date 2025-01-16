@extends('layouts.app')

@section('title', 'Edit Patient')

@section('content')
<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('admin.patient.update', $patient->id) }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ $patient->name }}" autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Age -->
        <div class="mt-4">
            <x-input-label for="age" :value="__('Age')" />
            <x-text-input id="age" class="block mt-1 w-full" type="text" name="age" value="{{ $patient->age }}" autocomplete="age" />
            <x-input-error :messages="$errors->get('age')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="text" name="email" value="{{ $patient->email }}" autocomplete="email" disabled />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Patient Contact Details -->
        <div class="mt-4">
            <x-input-label for="contact_details" :value="__('Contact Details')" />
            <x-text-input id="contact_details" class="block mt-1 w-full" type="text" name="contact_details" value="{{ $patient->patient->contact_details ?? '' }}" autocomplete="contact_details" />
            <x-input-error :messages="$errors->get('contact_details')" class="mt-2" />
        </div>

        <!-- Patient Medical History -->
        <div class="mt-4">
            <x-input-label for="medical_history" :value="__('Medical History')" />
            <x-text-input id="medical_history" class="block mt-1 w-full" type="text" name="medical_history" value="{{ $patient->patient->medical_history ?? '' }}" autocomplete="medical_history" />
            <x-input-error :messages="$errors->get('medical_history')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3">
                {{ __('Update Patient') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
@endsection