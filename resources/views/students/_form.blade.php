<div class="student-form-grid">
    <label>Name <input name="name" value="{{ old('name', $student->name ?? '') }}" required></label>
    <label>Email <input name="email" type="email" value="{{ old('email', $student->email ?? '') }}" required></label>
    <label>Phone <input name="phone" value="{{ old('phone', $student->phone ?? '') }}"></label>
    <label>Gender
        <select name="gender">
            <option value="">Select gender</option>
            @foreach(['Male', 'Female', 'Other'] as $gender)
                <option value="{{ $gender }}" @selected(old('gender', $student->gender ?? '') === $gender)>{{ $gender }}</option>
            @endforeach
        </select>
    </label>
    <label>Date of birth <input name="date_of_birth" type="date" value="{{ old('date_of_birth', isset($student) && $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}"></label>
    <label>Course <input name="course" value="{{ old('course', $student->course ?? '') }}"></label>
    <label>Class <input name="class_name" value="{{ old('class_name', $student->class_name ?? '') }}"></label>
    <label>Department <input name="department" value="{{ old('department', $student->department ?? '') }}"></label>
    <label>Status
        <select name="status" required>
            @foreach(['Active', 'Inactive', 'Graduated'] as $status)
                <option value="{{ $status }}" @selected(old('status', $student->status ?? 'Active') === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </label>
    <label class="wide">Address <textarea name="address" rows="2">{{ old('address', $student->address ?? '') }}</textarea></label>
    <label class="wide">Profile photo <input name="profile_photo" type="file" accept="image/*"></label>
</div>