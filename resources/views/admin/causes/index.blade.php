@extends('admin.layout')

@section('title', 'Manage Causes')

@section('content')
<div class="bg-gray-900 min-h-screen text-white p-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Manage Causes</h1>
        <a href="{{ route('admin.causes.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
            + Add New Cause
        </a>
    </div>

    <div class="bg-gray-800 shadow-md rounded-lg p-6">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-600">
                    <th class="p-2">Image</th>
                    <th class="p-2">Title</th>
                    <th class="p-2">Goal Amount</th>
                    <th class="p-2">Raised</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($causes as $cause)
                    <tr class="border-b border-gray-600">
                        <td class="p-2">
                            <img src="{{ asset('storage/' . $cause->image) }}" alt="Cause Image" class="w-16 h-16 rounded-md object-cover">
                        </td>
                        <td class="p-2">{{ $cause->title }}</td>
                        <td class="p-2 text-orange-400">${{ number_format($cause->goal, 2) }}</td>
                        <td class="p-2 text-yellow-400">${{ number_format($cause->raised, 2) }}</td>
                        <td class="p-2">
                            @if($cause->is_recent)
                                <span class="inline-flex items-center bg-blue-500 text-white px-2 py-1 rounded-full text-xs mr-1">
                                    Recent
                                </span>
                            @endif
                            
                            @if($cause->is_urgent)
                                <span class="inline-flex items-center bg-red-500 text-white px-2 py-1 rounded-full text-xs">
                                    Urgent
                                </span>
                            @endif
                            
                            @if(!$cause->is_recent && !$cause->is_urgent)
                                <span class="inline-flex items-center bg-gray-600 text-white px-2 py-1 rounded-full text-xs">
                                    General
                                </span>
                            @endif
                        </td>
                        <td class="p-2 flex space-x-2">
                            <a href="{{ route('admin.causes.edit', $cause->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 transition">Edit</a>
                            <form action="{{ route('admin.causes.destroy', $cause->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this cause?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($causes->isEmpty())
            <p class="text-center text-gray-400 mt-4">No causes found.</p>
        @endif
    </div>
</div>
@endsection