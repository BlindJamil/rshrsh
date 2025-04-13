@extends('admin.layout')

@section('title', 'Manage Donations')

@section('content')
<div class="py-12 bg-gray-900">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-white">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Manage Cash Donations</h1>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.donations.export') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Export CSV
                            </span>
                        </a>
                        <a href="#" id="toggleFilters" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filters
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Success message -->
                @if(session('success'))
                <div class="bg-green-900 text-green-200 p-4 mb-6 rounded-md shadow-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Filters Panel (hidden by default) -->
                <div id="filtersPanel" class="bg-gray-700 rounded-lg p-4 mb-6 hidden">
                    <form action="{{ route('admin.donations.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Date Range</label>
                            <select name="date_range" class="w-full p-2 rounded-md bg-gray-800 text-white border border-gray-600 focus:outline-none focus:border-yellow-500">
                                <option value="">All Time</option>
                                <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                                <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                                <option value="year" {{ request('date_range') == 'year' ? 'selected' : '' }}>This Year</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                            <select name="status" class="w-full p-2 rounded-md bg-gray-800 text-white border border-gray-600 focus:outline-none focus:border-yellow-500">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Cause</label>
                            <select name="cause_id" class="w-full p-2 rounded-md bg-gray-800 text-white border border-gray-600 focus:outline-none focus:border-yellow-500">
                                <option value="">All Causes</option>
                                @foreach($causes as $cause)
                                    <option value="{{ $cause->id }}" {{ request('cause_id') == $cause->id ? 'selected' : '' }}>{{ $cause->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Amount</label>
                            <div class="flex space-x-2">
                                <input type="number" name="min_amount" placeholder="Min" value="{{ request('min_amount') }}" class="w-full p-2 rounded-md bg-gray-800 text-white border border-gray-600 focus:outline-none focus:border-yellow-500">
                                <input type="number" name="max_amount" placeholder="Max" value="{{ request('max_amount') }}" class="w-full p-2 rounded-md bg-gray-800 text-white border border-gray-600 focus:outline-none focus:border-yellow-500">
                            </div>
                        </div>

                        <div class="md:col-span-4 flex justify-end space-x-3">
                            <button type="reset" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-500">Clear</button>
                            <button type="submit" class="px-4 py-2 bg-yellow-500 text-black rounded-md hover:bg-yellow-600">Apply Filters</button>
                        </div>
                    </form>
                </div>

                <!-- Donation Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-gray-700 rounded-lg p-4 shadow-md border border-gray-600">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 bg-opacity-20 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Total Donations</p>
                                <p class="text-xl font-bold text-white">{{ $totalDonations }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-700 rounded-lg p-4 shadow-md border border-gray-600">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 bg-opacity-20 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Total Amount</p>
                                <p class="text-xl font-bold text-white">${{ number_format($totalAmount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-700 rounded-lg p-4 shadow-md border border-gray-600">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-500 bg-opacity-20 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Average Donation</p>
                                <p class="text-xl font-bold text-white">${{ number_format($averageDonation, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-700 rounded-lg p-4 shadow-md border border-gray-600">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-500 bg-opacity-20 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Latest Donation</p>
                                {{ $latestDonation ? \Carbon\Carbon::parse($latestDonation->created_at)->diffForHumans() : 'No donations yet' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-700 rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-600">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Donor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Cause</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Receipt #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-700 divide-y divide-gray-600">
                            @forelse($donations as $donation)
                                <tr class="hover:bg-gray-650 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-white">{{ $donation->name ?? 'Anonymous' }}</div>
                                        <div class="text-sm text-gray-400">{{ $donation->email ?? 'No email provided' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-white">
                                            {{ $donation->cause_title ?? 'Unknown Cause' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-green-400">${{ number_format($donation->amount, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-white">{{ $donation->transaction_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $donation->status == 'completed' ? 'bg-green-900 text-green-200' : 
                                              ($donation->status == 'pending' ? 'bg-yellow-900 text-yellow-200' : 'bg-red-900 text-red-200') }}">
                                            {{ ucfirst($donation->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-white">{{ date('M d, Y', strtotime($donation->created_at)) }}</div>
                                        <div class="text-sm text-gray-400">{{ date('h:i A', strtotime($donation->created_at)) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.donations.show', $donation->id) }}" class="text-blue-400 hover:text-blue-300">
                                                <span class="sr-only">View</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            
                                            @if($donation->status == 'pending')
                                            <form action="{{ route('admin.donations.update', $donation->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="text-green-400 hover:text-green-300" onclick="return confirm('Mark this donation as completed?')">
                                                    <span class="sr-only">Complete</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </form>
                                            @endif
                                            
                                            @if($donation->email)
                                            <button type="button" onclick="sendThankYou('{{ $donation->id }}')" class="text-yellow-400 hover:text-yellow-300">
                                                <span class="sr-only">Thank You Email</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-400">
                                        No donations found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $donations->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle filters panel
    document.getElementById('toggleFilters').addEventListener('click', function(e) {
        e.preventDefault();
        const filtersPanel = document.getElementById('filtersPanel');
        filtersPanel.classList.toggle('hidden');
    });

    // Function to send thank you email
    function sendThankYou(donationId) {
        if(confirm('Send a thank you email to this donor?')) {
            fetch("{{ url('admin/donations/thank-you') }}/" + donationId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Thank you email sent successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while sending the thank you email.');
            });
        }
    }
</script>
@endsection