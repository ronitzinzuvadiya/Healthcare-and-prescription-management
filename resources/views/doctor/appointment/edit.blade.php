@extends('layouts.app')

@section('title', 'Edit Appointment')

@section('content')
<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('doctor.appointment.update', $appointment->id) }}">
        @csrf
        <!-- Patient Name -->
        <div>
            <x-input-label for="name" :value="__('Patient Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="$appointment->patient->name" disabled />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Appointment Date -->
        <div class="mt-4">
            <x-input-label for="appointment_date" :value="__('Appointment Date')" />
            <x-text-input id="appointment_date" class="block mt-1 w-full" type="date" name="appointment_date" :value="$appointment->appointment_date" required />
            <x-input-error :messages="$errors->get('appointment_date')" class="mt-2" />
        </div>

        <!-- Start Time -->
        <div class="mt-4">
            <x-input-label for="start_time" :value="__('Start Time')" />
            <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" :value="$appointment->start_time" required />
            <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
        </div>

        <!-- End Time -->
        <div class="mt-4">
            <x-input-label for="end_time" :value="__('End Time')" />
            <x-text-input id="end_time" class="block mt-1 w-full" type="time" name="end_time" :value="$appointment->end_time" required />
            <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
        </div>

        <!-- Problem -->
        <div class="mt-4">
            <x-input-label for="problems" :value="__('Problem')" />
            <x-text-input id="problems" class="block mt-1 w-full" name="problems" :value="$appointment->problems" disabled />
            <x-input-error :messages="$errors->get('problems')" class="mt-2" />
        </div>

        <!-- Status -->
        <div class="mt-4">
            <x-input-label for="status" :value="__('Status')" />
            <select id="status" class="block mt-1 w-full" name="status">
                <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                <option value="approved" {{ $appointment->status == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                <option value="rejected" {{ $appointment->status == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                <option value="rescheduled" {{ $appointment->status == 'rescheduled' ? 'selected' : '' }}>{{ __('Rescheduled') }}</option>
                <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3">
                {{ __('Update Appointment') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
@endsection