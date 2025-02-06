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
                <th class="border px-2 py-2">Actions</th>
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
                <td class="border px-2 py-2">{{ ucfirst($appointment->status) }}</td>
                <td class="border px-2 py-2">
                    <a href="{{route('doctor.appointment.edit', $appointment->id)}}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{route('doctor.appointment.delete', $appointment->id)}}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this appointment?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection