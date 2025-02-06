@extends('layouts.app')

@section('title', 'Prescriptions')

@section('content')
<div class="container">
    <!-- Table for displaying doctors -->
    <table class="w-full whitespace-no-wrapw-full whitespace-no-wrap">
        <thead>
            <tr class="text-center font-bold">
                <th class="border px-2 py-2">Sr. no</th>
                <th class="border px-2 py-2">Patient Name</th>
                <th class="border px-2 py-2">Doctor Name</th>
                <th class="border px-2 py-2">Medicine Name</th>
                <th class="border px-2 py-2">Dosage</th>
                <th class="border px-2 py-2">Start Date</th>
                <th class="border px-2 py-2">End Date</th>
                <th class="border px-2 py-2">Special Note</th>
                <th class="border px-2 py-2">Created On</th>
                <th class="border px-2 py-2">Last Updated On</th>
                <th class="border px-2 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prescriptions as $prescription)
            <tr>
                <td class="border px-2 py-2">{{ $loop->iteration }}</td>
                <td class="border px-2 py-2">{{$prescription->patient->name }}</td>
                <td class="border px-2 py-2">{{$prescription->doctor->name }}</td>
                <td class="border px-2 py-2">{{$prescription->medicine_name }}</td>
                <td class="border px-2 py-2">{{$prescription->dosage }}</td>
                <td class="border px-2 py-2">{{$prescription->start_date }}</td>
                <td class="border px-2 py-2">{{$prescription->end_date }}</td>
                <td class="border px-2 py-2">{{$prescription->special_notes }}</td>
                <td class="border px-2 py-2">{{$prescription->created_at->format('Y-m-d H:i:s') }}</td>
                <td class="border px-2 py-2">{{$prescription->updated_at->format('Y-m-d H:i:s') }}</td>
                <td class="border px-2 py-2">
                    <form action="{{route('admin.prescription.delete', $prescription->id)}}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this prescription?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection