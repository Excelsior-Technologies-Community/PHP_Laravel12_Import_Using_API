@extends('students.layout')

@section('title', 'Edit Student')

@section('content')
    <main class="container">
        <h2>Edit Student</h2>
        @if($errors->any()) <div class="error-message">{{ $errors->first() }}</div> @endif
        <form method="POST" action="{{ route('students.update', $student) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('students._form')
            <button class="blue" type="submit">Save Changes</button>
            <a class="gray btn" href="{{ route('students.show', $student) }}">Cancel</a>
        </form>
    </main>
@endsection