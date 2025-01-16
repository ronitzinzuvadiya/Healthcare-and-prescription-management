@extends('layouts.app')

@section('title', 'Patients')

@section('content')

@if(session()->has('message'))
<div class="alert alert-success">
    {{ session()->get('message') }}
</div>
@endif

<div class="container">
    <a href="{{ route('patient.register') }}" class="btn btn-primary">
        <x-primary-button class="ms-3">
            Add Patient
        </x-primary-button>
    </a>

    <!-- Table for displaying doctors -->
    <table class="w-full whitespace-no-wrapw-full whitespace-no-wrap">
        <thead>
            <tr class="text-center font-bold">
                <th class="border px-2 py-2">Sr. no</th>
                <th class="border px-2 py-2">Name</th>
                <th class="border px-2 py-2">Email</th>
                <th class="border px-2 py-2">Age</th>
                <th class="border px-2 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @if(count($patients) === 0)
            <tr>
                <td colspan="5" class="border px-2 py-2 text-center">No patients found.</td>
            </tr>
            @else
                @foreach ($patients as $patient)
                <tr>
                    <td class="border px-2 py-2">{{ $loop->iteration }}</td>
                    <td class="border px-2 py-2">{{ $patient->name }}</td>
                    <td class="border px-2 py-2">{{ $patient->email }}</td>
                    <td class="border px-2 py-2">{{ $patient->age }}</td>
                    <td class="border px-2 py-2">
                        <a href="{{route('admin.patient.edit', $patient->id)}}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{route('admin.patient.delete', $patient->id)}}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this patient?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection