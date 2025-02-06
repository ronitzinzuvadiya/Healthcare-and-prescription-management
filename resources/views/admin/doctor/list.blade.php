@extends('layouts.app')

@section('title', 'Doctors')

@section('content')
<div class="container">
    <a href="{{ route('doctor.register') }}" class="btn btn-primary">
        <x-primary-button class="ms-3">
            Add Doctor
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
            @foreach ($doctors as $doctor)
            <tr>
                <td class="border px-2 py-2">{{ $loop->iteration }}</td>
                <td class="border px-2 py-2">{{ $doctor->name }}</td>
                <td class="border px-2 py-2">{{ $doctor->email }}</td>
                <td class="border px-2 py-2">{{ $doctor->age }}</td>
                <td class="border px-2 py-2">
                    <a href="{{route('admin.doctor.edit', $doctor->id)}}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{route('admin.doctor.delete', $doctor->id)}}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this doctor?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection