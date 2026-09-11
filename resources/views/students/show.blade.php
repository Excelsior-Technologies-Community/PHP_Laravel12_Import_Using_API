@extends('students.layout')

@section('title', $student->name)

@section('content')
    <main class="container">
        @if(session('success')) <div class="success-message">{{ session('success') }}</div> @endif
        <h2>Student Details</h2>
        @if($student->profile_photo)
            <img class="profile-photo" src="{{ asset('storage/' . $student->profile_photo) }}" alt="{{ $student->name }}">
        @endif
        <dl class="details">
            <dt>Name</dt><dd>{{ $student->name }}</dd>
            <dt>Email</dt><dd>{{ $student->email }}</dd>
            <dt>Phone</dt><dd>{{ $student->phone ?: '-' }}</dd>
            <dt>Gender</dt><dd>{{ $student->gender ?: '-' }}</dd>
            <dt>Date of birth</dt><dd>{{ $student->date_of_birth?->format('d M Y') ?: '-' }}</dd>
            <dt>Course</dt><dd>{{ $student->course ?: '-' }}</dd>
            <dt>Class</dt><dd>{{ $student->class_name ?: '-' }}</dd>
            <dt>Department</dt><dd>{{ $student->department ?: '-' }}</dd>
            <dt>Status</dt><dd>{{ $student->status }}</dd>
            <dt>Address</dt><dd>{{ $student->address ?: '-' }}</dd>
        </dl>
        <a class="blue btn" href="{{ route('students.edit', $student) }}">Edit</a>
        <a class="gray btn" href="{{ route('students.index') }}">Back</a>
    </main>
@endsection