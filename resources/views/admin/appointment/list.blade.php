@extends('layouts.app')

@section('title', 'Appointments')

@section('content')
<div class="container">
    @if(session()->has('message'))
    <div class="alert alert-success bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-6">
        <strong class="font-semibold">{{ session()->get('message') }}</strong>
    </div>
    @endif

    <!-- Filter appointment based on Doctor, Patient, Status -->
    <div class="flex gap-4">
        <label for="doctor">Doctor:</label>
        <select id="doctor" name="doctor" class="form-select">
            <option value="">All</option>
            @foreach ($doctors as $doctor)
            <option value="{{$doctor->id }}">{{ $doctor->name }}</option>
            @endforeach
        </select>

        <label for="patient">Patient:</label>
        <select id="patient" name="patient" class="form-select">
            <option value="">All</option>
            @foreach ($patients as $patient)
            <option value="{{$patient->id }}">{{ $patient->name }}</option>
            @endforeach
        </select>

        <label for="status">Status:</label>
        <select id="status" name="status" class="form-select">
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="rescheduled">Rescheduled</option>
            <option value="completed">Completed</option>
        </select>
    </div>

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

@section('script')
<script>
    $(document).ready(function() {
        $('#doctor, #patient, #status').on('change', function() {
            alert('hi');
            // var doctor = $('#doctor').val();
            // var patient = $('#patient').val();
            // var status = $('#status').val();

            // $.ajax({
            //     url: "{{ route('appointments.list') }}",
            //     type: 'GET',
            //     data: { doctor: doctor, patient: patient, status: status },
            //     success: function(response) {
            //         alert('hi');
            //     }
            // });
        });
    });
</script>
@endsection