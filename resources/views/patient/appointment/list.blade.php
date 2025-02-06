@extends('layouts.app')

@section('title', 'Appointments')

@section('content')
<div class="container">
    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif

    <!-- Table for displaying appointments -->
    <table class="w-full whitespace-no-wrapw-full whitespace-no-wrap">
        <thead>
            <tr class="text-center font-bold">
                <th class="border px-2 py-2">Sr. no</th>
                <th class="border px-2 py-2">Patient Name</th>
                <th class="border px-2 py-2">Doctor Name</th>
                <th class="border px-2 py-2">Appointment Date</th>
                <th class="border px-2 py-2">Start Time</th>
                <th class="border px-2 py-2">End Time</th>
                <th class="border px-2 py-2">Problems</th>
                <th class="border px-2 py-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @if(count($appointments) === 0)
            <tr>
                <td colspan="9" class="border px-2 py-2 text-center">No appointments found.</td>
            </tr>
            @else
                @foreach ($appointments as $appointment)
                <tr>
                    <td class="border px-2 py-2">{{ $loop->iteration }}</td>
                    <td class="border px-2 py-2">{{ $appointment->patient->name }}</td>
                    <td class="border px-2 py-2">{{ $appointment->doctor->name }}</td>
                    <td class="border px-2 py-2">{{ $appointment->appointment_date }}</td>
                    <td class="border px-2 py-2">{{ $appointment->start_time }}</td>
                    <td class="border px-2 py-2">{{ $appointment->end_time }}</td>
                    <td class="border px-2 py-2">{{ $appointment->problems }}</td>
                    <td class="border px-2 py-2">{{ $appointment->status }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection