@extends('students.layout')

@section('title', 'Add Student')

@section('content')
<main class="container">
    <header class="page-header">
        <div>
            <h2>Add Student</h2>
            <p>Create a complete student profile.</p>
        </div>
        <a class="gray btn" href="{{ route('students.index') }}">Back to Students</a>
    </header>

    @if($errors->any())
        <div class="error-message">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data">
        @csrf
        @include('students._form')
        <div class="form-actions">
            <button class="blue" type="submit">Add Student</button>
            <a class="gray btn" href="{{ route('students.index') }}">Cancel</a>
        </div>
    </form>
</main>
@endsection