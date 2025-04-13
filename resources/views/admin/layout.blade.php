<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-white">
    <!-- Navbar -->
    <nav class="bg-gray-800 p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#" class="text-xl font-bold text-white">Admin Panel</a>
            <ul class="flex space-x-6">
                <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-400">Dashboard</a></li>
                <li><a href="{{ route('admin.causes.index') }}" class="hover:text-gray-400">Causes</a></li>
                <li><a href="{{ route('admin.donations') }}" class="hover:text-gray-400">Donations</a></li>
                <li><a href="{{ route('admin.projects.index') }}" class="hover:text-gray-400">Volunteer</a></li>
                <li><a href="{{ route('admin.messages.index') }}" class="text-white hover:text-yellow-500 transition">
                    Contact Messages</a>
                </li>
                <li>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-400">
                            Log Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="container mx-auto mt-8 p-6">
        @yield('content')
    </div>

</body>
</html>
