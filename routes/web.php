<?php

use App\Http\Controllers\VolunteeringController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\CauseController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserDonationController;
use App\Http\Controllers\AdminDonationController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Public Routes (Accessible to Everyone)
|--------------------------------------------------------------------------
*/
Route::get('/', function () { return view('welcome'); })->name('home');
Route::get('/about', function () { return view('about'); })->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/faq', function () { return view('FAQ'); })->name('FAQ');
Route::get('/privacy-policy', function () { return view('privacy-policy'); })->name('privacy.policy');

// Cause Routes
Route::get('/cause', [CauseController::class, 'publicIndex'])->name('cause');

// Improved Donation Routes
Route::get('/donate/{id}', [UserDonationController::class, 'showForm'])->name('donation.form');
Route::post('/donate', [UserDonationController::class, 'store'])->name('donate');
Route::get('/donation/thank-you/{id}', [UserDonationController::class, 'thankYou'])->name('donation.thank-you');
Route::post('/donation/callback', [UserDonationController::class, 'handlePaymentCallback'])->name('donation.callback');

// Volunteer Routes
Route::get('/volunteer', [VolunteeringController::class, 'index'])->name('volunteer');
Route::post('/volunteer', [VolunteeringController::class, 'store'])->middleware('auth')->name('volunteer.store');

/*
|--------------------------------------------------------------------------
| Authentication & User Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }

    $user = Auth::user();
    if (DB::table('admins')->where('user_id', $user->id)->exists()) {
        return redirect()->route('admin.dashboard');
    }

    return view('welcome');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin Routes (Only Accessible by Admins)
|--------------------------------------------------------------------------
*/
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('updateSettings');

    // Causes Management
    Route::get('/causes', [CauseController::class, 'index'])->name('causes.index');
    Route::get('/causes/create', [CauseController::class, 'create'])->name('causes.create');
    Route::post('/causes', [CauseController::class, 'store'])->name('causes.store');
    Route::get('/causes/{id}/edit', [CauseController::class, 'edit'])->name('causes.edit');
    Route::put('/causes/{id}', [CauseController::class, 'update'])->name('causes.update');
    Route::delete('/causes/{id}', [CauseController::class, 'destroy'])->name('causes.destroy');
    
    // Recent Campaigns Management
    Route::post('/causes/store-recent-donation', [CauseController::class, 'storeRecentDonation'])->name('causes.store-recent-donation');

    // Donation Dashboard
    Route::get('/donations', [AdminController::class, 'donations'])->name('donations');

    // Improved Donation Management Routes
    Route::get('/donation-details', [AdminDonationController::class, 'index'])->name('donations.index');
    Route::get('/donation-details/export', [AdminDonationController::class, 'export'])->name('donations.export');
    Route::get('/donation-details/{id}', [AdminDonationController::class, 'show'])->name('donations.show');
    Route::put('/donation-details/{id}', [AdminDonationController::class, 'update'])->name('donations.update');
    Route::post('/donation-details/thank-you/{id}', [AdminDonationController::class, 'sendThankYou'])->name('donations.thank-you');

    // Contact Messages
    Route::get('/messages', [ContactController::class, 'adminIndex'])->name('messages.index');
    Route::post('/messages/{id}/read', [ContactController::class, 'markAsRead'])->name('messages.mark-read');

    // Volunteer Project Management
    Route::get('/projects', [VolunteeringController::class, 'adminIndex'])->name('projects.index');
    Route::get('/projects/create', [VolunteeringController::class, 'create'])->name('projects.create');
    Route::post('/projects', [VolunteeringController::class, 'storeProject'])->name('projects.store');
    Route::get('/projects/{id}/edit', [VolunteeringController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{id}', [VolunteeringController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{id}', [VolunteeringController::class, 'destroy'])->name('projects.destroy');
    
    // Volunteer Application Management
    Route::post('/volunteers/{id}/approve', [VolunteeringController::class, 'approveVolunteer'])->name('volunteers.approve');
    Route::post('/volunteers/{id}/reject', [VolunteeringController::class, 'rejectVolunteer'])->name('volunteers.reject');
    Route::post('/volunteers/{id}/reset', [VolunteeringController::class, 'resetVolunteer'])->name('volunteers.reset');

    // Admin Logout
    Route::post('/logout', function () {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

/*
|--------------------------------------------------------------------------
| Profile Routes (Authenticated Users Only)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'dashboard'])->name('dashboard');
    Route::get('/information', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/information', [ProfileController::class, 'update'])->name('update');
    Route::post('/update-image', [ProfileController::class, 'updateProfileImage'])->name('update-image');
    Route::delete('/remove-image', [ProfileController::class, 'removeProfileImage'])->name('remove-image');
    Route::get('/password', [ProfileController::class, 'editPassword'])->name('password.edit');
    Route::get('/donations', [ProfileController::class, 'donations'])->name('donations');
    Route::get('/volunteer', [ProfileController::class, 'volunteer'])->name('volunteer');
    Route::get('/delete', [ProfileController::class, 'deleteAccount'])->name('delete');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    Route::get('/notifications/mark-all-read', [ProfileController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-read');
});

// Test Email Route with editable email (Remove after testing)
Route::get('/mail-test/{email}', function ($email) {
    try {
        \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\TestEmail());
        return 'Test email sent to ' . $email . '. Check your Mailtrap inbox.';
    } catch (\Exception $e) {
        return 'Error sending email: ' . $e->getMessage();
    }
});

// Mail configuration check (Remove after testing)
Route::get('/mail-config', function () {
    $config = [
        'driver' => config('mail.default'),
        'host' => config('mail.mailers.smtp.host'),
        'port' => config('mail.mailers.smtp.port'),
        'from_address' => config('mail.from.address'),
        'from_name' => config('mail.from.name'),
        'encryption' => config('mail.mailers.smtp.encryption'),
    ];
    
    return response()->json($config);
});

// Add a temporary route to create a super admin (REMOVE AFTER TESTING)
Route::get('/create-admin', function () {
    try {
        // First, make sure the tables exist
        if (!Schema::hasTable('admin_users')) {
            return 'Error: admin_users table does not exist!';
        }
        
        if (!Schema::hasTable('admin_roles')) {
            return 'Error: admin_roles table does not exist!';
        }
        
        if (!Schema::hasTable('admin_role_user')) {
            return 'Error: admin_role_user table does not exist!';
        }
        
        // Create or update admin user
        $adminEmail = 'admin@test.com';
        $adminPassword = 'admin123';
        $adminName = 'Super Admin';
        
        $existingAdmin = DB::table('admin_users')->where('email', $adminEmail)->first();
        
        if ($existingAdmin) {
            // Update existing admin
            DB::table('admin_users')
                ->where('email', $adminEmail)
                ->update([
                    'name' => $adminName,
                    'password' => Hash::make($adminPassword),
                    'updated_at' => now(),
                ]);
            $adminId = $existingAdmin->id;
            $message = 'Admin user updated successfully!';
        } else {
            // Create new admin
            $adminId = DB::table('admin_users')->insertGetId([
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $message = 'Admin user created successfully!';
        }

        // Create super_admin role if it doesn't exist
        $roleId = null;
        $role = DB::table('admin_roles')->where('name', 'super_admin')->first();
        
        if (!$role) {
            $roleId = DB::table('admin_roles')->insertGetId([
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Full access to all features',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $message .= ' Super admin role created.';
        } else {
            $roleId = $role->id;
        }

        // Assign role to admin
        $roleAssignment = DB::table('admin_role_user')
            ->where('admin_user_id', $adminId)
            ->where('admin_role_id', $roleId)
            ->first();
            
        if (!$roleAssignment) {
            DB::table('admin_role_user')->insert([
                'admin_user_id' => $adminId,
                'admin_role_id' => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $message .= ' Role assigned to admin.';
        }

        // Try logging in with the admin to verify
        $admin = \App\Models\Admin::where('email', $adminEmail)->first();
        
        if ($admin) {
            Auth::guard('admin')->login($admin);
            if (Auth::guard('admin')->check()) {
                $message .= ' Admin login test successful!';
            } else {
                $message .= ' Warning: Admin login test failed.';
            }
        }

        return $message . '<br><br>Admin Details:<br>Email: ' . $adminEmail . '<br>Password: ' . $adminPassword . 
               '<br><br>You can now try logging in at <a href="/admin/login">Admin Login</a>';
    } catch (\Exception $e) {
        return 'Error creating Super Admin: ' . $e->getMessage() . '<br>Trace: ' . $e->getTraceAsString();
    }
});

// Debug route to check admin users in the database (REMOVE AFTER TESTING)
Route::get('/check-admin-users', function () {
    $adminUsers = DB::table('admin_users')->get();
    $adminRoles = DB::table('admin_roles')->get();
    $adminRoleUser = DB::table('admin_role_user')->get();
    
    return [
        'admin_users' => $adminUsers,
        'admin_roles' => $adminRoles,
        'admin_role_user' => $adminRoleUser
    ];
});

// Breeze Authentication Routes
require __DIR__.'/auth.php';

// Regular user logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');