@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Doctors List</h1>

        <a href="{{ route('doctor.register') }}" class="btn btn-primary">Add Doctor</a>
        
        <!-- Table for displaying doctors -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Age</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($doctors as $doctor)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $doctor->name }}</td>
                        <td>{{ $doctor->email }}</td>
                        <td>{{ $doctor->age }}</td>
                        <td>
                            <a href="" class="btn btn-info btn-sm">View</a>
                            <a href="" class="btn btn-warning btn-sm">Edit</a>
                            <form action="" method="POST" style="display:inline;">
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
