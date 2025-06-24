@extends('admin.layout')

@section('title', 'Create Admin User')

@section('content')
<div class="py-12 bg-gray-900 min-h-screen">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 md:p-8 text-white">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Create New Admin User</h1>
                    <a href="{{ route('admin.admins.index') }}" class="text-yellow-500 hover:text-yellow-300 text-sm">
                        &larr; Back to Admin List
                    </a>
                </div>

                @if(session('error'))
                    <div class="bg-red-900 text-red-200 p-4 mb-6 rounded-md shadow-md flex items-center">
                         <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                 <form action="{{ route('admin.admins.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full p-3 bg-gray-700 text-white border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="w-full p-3 bg-gray-700 text-white border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('email') border-red-500 @enderror">
                         @error('email')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                        <input type="password" name="password" id="password" required
                               class="w-full p-3 bg-gray-700 text-white border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('password') border-red-500 @enderror">
                         @error('password')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full p-3 bg-gray-700 text-white border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </div>

                     <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Assign Roles</label>
                        <div class="space-y-2 max-h-48 overflow-y-auto bg-gray-700 border border-gray-600 rounded-md p-3">
                            @forelse ($roles as $role)
                                <label class="flex items-center">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                           class="rounded bg-gray-800 border-gray-600 text-yellow-500 focus:ring-yellow-500 mr-2"
                                           {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                    <span class="text-sm text-white">{{ $role->display_name }}</span>
                                    <span class="text-xs text-gray-400 ml-2">({{ $role->name }})</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-400 italic">No roles available.</p>
                            @endforelse
                        </div>
                         @error('roles')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                         @error('roles.*')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                            Create Admin User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection