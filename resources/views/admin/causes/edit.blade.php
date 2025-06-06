@extends('admin.layout')

@section('title', 'Edit Cause')

@section('content')
<div class="bg-gray-900 min-h-screen text-white p-8">
    <div class="md:flex md:items-center">
        <div>
            <h2 class="text-2xl font-medium leading-6 text-white">Edit Cause</h2>
            <p class="mt-1 text-sm text-gray-500">Update this cause's details and settings. Urgent and active campaigns will appear in the Recent Campaigns section.</p>
        </div>
        <div class="ml-auto mt-4 md:mt-0">
            <form action="{{ route('admin.causes.update', $cause->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-gray-300">Title</label>
                    <input type="text" name="title" value="{{ old('title', $cause->title) }}" class="w-full p-2 rounded-md bg-gray-700 text-white border border-gray-600 focus:outline-none focus:border-orange-400">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-300">Description</label>
                    <textarea name="description" rows="4" class="w-full p-2 rounded-md bg-gray-700 text-white border border-gray-600 focus:outline-none focus:border-orange-400">{{ old('description', $cause->description) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-300">Goal Amount ($)</label>
                    <input type="number" name="goal" value="{{ old('goal', $cause->goal) }}" step="0.01" class="w-full p-2 rounded-md bg-gray-700 text-white border border-gray-600 focus:outline-none focus:border-orange-400">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-300">Cause Image</label>
                    <input type="file" name="image" class="w-full p-2 rounded-md bg-gray-700 text-white border border-gray-600 focus:outline-none focus:border-orange-400">
                    <p class="text-gray-400 text-sm mt-2">Current image:</p>
                    <img src="{{ asset('storage/' . $cause->image) }}" alt="Cause Image" class="w-32 h-32 rounded-md mt-2 object-cover">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-300 mb-2">Cause Type</label>
                    <div class="flex flex-col space-y-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="cause_type" value="general" {{ $cause->is_recent ? '' : 'checked' }} class="text-yellow-500 bg-gray-700 border-gray-600 focus:ring-yellow-500" id="general_cause">
                            <span class="ml-2 text-gray-300">Donation Field Item</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="cause_type" value="recent" {{ $cause->is_recent ? 'checked' : '' }} class="text-yellow-500 bg-gray-700 border-gray-600 focus:ring-yellow-500" id="recent_cause">
                            <span class="ml-2 text-gray-300">Recent Campaign</span>
                        </label>
                        <p class="text-gray-400 text-sm mt-1 ml-6">Select "Recent Campaign" for time-sensitive campaigns. These will appear in the Recent Campaigns section.</p>
                    </div>
                </div>

                <div class="mb-6 ml-6" id="urgent_option" style="{{ $cause->is_recent ? '' : 'display: none;' }}">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_urgent" {{ $cause->is_urgent ? 'checked' : '' }} class="mr-2 rounded bg-gray-700 border-gray-600 text-red-400 focus:ring-red-500">
                        <span class="text-gray-300">Mark as Urgent</span>
                    </label>
                    <p class="text-gray-400 text-sm mt-1 ml-6">Select this option if this donation requires immediate attention. An "Urgent" badge will be displayed.</p>
                </div>

                <!-- Receipt Expiration Days -->
                <div class="mb-4">
                    <label for="receipt_expiry_days" class="block text-gray-300 text-sm font-bold mb-2">Receipt Expiration (Days)</label>
                    <div class="flex items-center">
                        <input type="number" name="receipt_expiry_days" id="receipt_expiry_days" 
                               value="{{ old('receipt_expiry_days', $cause->receipt_expiry_days ?? 7) }}" 
                               class="w-24 p-3 bg-gray-700 text-white border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500"
                               required min="1" max="90">
                        <p class="ml-3 text-gray-400 text-sm">Number of days the donation receipt remains valid</p>
                    </div>
                    @error('receipt_expiry_days')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 mt-4 rounded-md hover:bg-blue-600 transition">
                    Update Cause
                </button>

                @if ($errors->any())
                    <div class="bg-red-500 text-white p-3 rounded-md mt-6">
                        <strong>Errors:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const generalCauseRadio = document.getElementById('general_cause');
        const recentCauseRadio = document.getElementById('recent_cause');
        const urgentOption = document.getElementById('urgent_option');
        
        // Function to toggle urgent option visibility
        function toggleUrgentOption() {
            if (recentCauseRadio.checked) {
                urgentOption.style.display = 'block';
            } else {
                urgentOption.style.display = 'none';
                // Also uncheck the urgent checkbox if not a recent cause
                document.querySelector('input[name="is_urgent"]').checked = false;
            }
        }
        
        // Add event listeners
        generalCauseRadio.addEventListener('change', toggleUrgentOption);
        recentCauseRadio.addEventListener('change', toggleUrgentOption);
        
        // Initialize on page load
        toggleUrgentOption();
    });
</script>
@endsection