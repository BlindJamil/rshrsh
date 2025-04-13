@extends('layouts.app')

@section('title', 'login')

@section('content')
<div class="container mx-auto max-w-md mt-10">
    <h2 class="text-2xl font-bold text-center mb-6">Admin Login</h2>
    <form action="{{ route('admin.login') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" class="w-full px-4 py-2 border rounded-md" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Password</label>
            <input type="password" name="password" class="w-full px-4 py-2 border rounded-md" required>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md">Login</button>
    </form>
</div>
@endsection
