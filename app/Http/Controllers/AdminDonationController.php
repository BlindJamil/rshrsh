<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use App\Models\Cause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\DonationThankYouMail;

class AdminDonationController extends Controller
{
    /**
     * Display a listing of the donations.
     */
    public function index(Request $request)
    {
        // Get all causes for the filter dropdown
        $causes = Cause::all();
        
        // Build donation query with joins
        $query = DB::table('donations')
            ->leftJoin('causes', 'donations.cause_id', '=', 'causes.id')
            ->select(
                'donations.*',
                'causes.title as cause_title'
            )
            ->orderBy('donations.created_at', 'desc');
            
        // Apply filters if provided
        if ($request->filled('date_range')) {
            // Apply date range filters
            $dateRange = $request->input('date_range');
            
            if ($dateRange === 'today') {
                $query->whereDate('donations.created_at', today());
            } elseif ($dateRange === 'week') {
                $query->whereBetween('donations.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($dateRange === 'month') {
                $query->whereMonth('donations.created_at', now()->month)
                      ->whereYear('donations.created_at', now()->year);
            } elseif ($dateRange === 'year') {
                $query->whereYear('donations.created_at', now()->year);
            }
        }
        
        if ($request->filled('payment_method')) {
            $query->where('donations.payment_method', $request->input('payment_method'));
        }
        
        if ($request->filled('cause_id')) {
            $query->where('donations.cause_id', $request->input('cause_id'));
        }
        
        if ($request->filled('min_amount')) {
            $query->where('donations.amount', '>=', $request->input('min_amount'));
        }
        
        if ($request->filled('max_amount')) {
            $query->where('donations.amount', '<=', $request->input('max_amount'));
        }
        
        // Get paginated results
        $donations = $query->paginate(10);
        
        // Calculate statistics
        $totalDonations = DB::table('donations')->count();
        $totalAmount = DB::table('donations')->sum('amount');
        $averageDonation = $totalDonations > 0 ? $totalAmount / $totalDonations : 0;
        $latestDonation = DB::table('donations')->latest('created_at')->first();
        
        return view('admin.donations.index', compact(
            'donations', 
            'causes', 
            'totalDonations', 
            'totalAmount', 
            'averageDonation',
            'latestDonation'
        ));
    }
    
    /**
     * Display the specified donation.
     */
    public function show($id)
    {
        $donation = DB::table('donations')
            ->leftJoin('causes', 'donations.cause_id', '=', 'causes.id')
            ->select(
                'donations.*',
                'causes.title as cause_title'
            )
            ->where('donations.id', $id)
            ->first();
            
        if (!$donation) {
            return redirect()->route('admin.donations.index')
                ->with('error', 'Donation not found');
        }
        
        // Get donor history if not anonymous
        $donorHistory = collect([]);
        $donorTotal = 0;
        
        if ($donation->email && !$donation->anonymous) {
            $donorHistory = DB::table('donations')
                ->where('email', $donation->email)
                ->where('id', '!=', $donation->id)
                ->latest()
                ->limit(5)
                ->get();
                
            $donorTotal = DB::table('donations')
                ->where('email', $donation->email)
                ->sum('amount');
        }
        
        return view('admin.donations.show', compact('donation', 'donorHistory', 'donorTotal'));
    }
    
    /**
     * Update the specified donation.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,completed,cancelled,expired',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $donation = Donation::findOrFail($id);
        $oldStatus = $donation->status;
        
        // If changing from pending to completed, update cause raised amount
        if ($oldStatus === 'pending' && $request->input('status') === 'completed') {
            $cause = Cause::find($donation->cause_id);
            if ($cause) {
                $cause->raised = $cause->raised + $donation->amount;
                $cause->save();
            }
        }
        
        // If changing from completed to cancelled, subtract from cause raised amount
        if ($oldStatus === 'completed' && $request->input('status') === 'cancelled') {
            $cause = Cause::find($donation->cause_id);
            if ($cause) {
                $cause->raised = max(0, $cause->raised - $donation->amount);
                $cause->save();
            }
        }
        
        $donation->status = $request->input('status');
        $donation->admin_notes = $request->input('admin_notes');
        
        // If status is changing to completed, record completion date
        if ($oldStatus !== 'completed' && $request->input('status') === 'completed') {
            $donation->completed_at = now();
        }
        
        $donation->save();
        
        return redirect()->route('admin.donations.show', $donation->id)
            ->with('success', 'Donation status updated successfully.');
    }
    
    /**
     * Send a thank you email to the donor.
     */
    public function sendThankYou($id)
    {
        $donation = Donation::find($id);
        
        if (!$donation || !$donation->email) {
            return response()->json([
                'success' => false,
                'message' => 'Donation not found or donor has no email'
            ]);
        }
        
        // Get the cause information
        $cause = Cause::find($donation->cause_id);
        
        if (!$cause) {
            $cause = new \stdClass();
            $cause->title = 'General Donation';
        }
        
        try {
            // Send thank you email
            Mail::to($donation->email)
                ->send(new DonationThankYouMail($donation, $cause));
                
            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Thank you email sent successfully'
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('Failed to send donation thank you email: ' . $e->getMessage());
            
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Export donations as CSV.
     */
    public function export(Request $request)
    {
        $donations = DB::table('donations')
            ->leftJoin('causes', 'donations.cause_id', '=', 'causes.id')
            ->select(
                'donations.id',
                'donations.name',
                'donations.email',
                'donations.phone',
                'donations.amount',
                'donations.payment_method',
                'donations.status',
                'donations.created_at',
                'causes.title as cause_title'
            )
            ->orderBy('donations.created_at', 'desc')
            ->get();
            
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="donations.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($donations) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'ID', 
                'Donor Name', 
                'Email', 
                'Phone', 
                'Amount', 
                'Payment Method', 
                'Status', 
                'Date', 
                'Cause'
            ]);
            
            // Add rows
            foreach ($donations as $donation) {
                fputcsv($file, [
                    $donation->id,
                    $donation->name ?? 'Anonymous',
                    $donation->email ?? 'No email',
                    $donation->phone ?? 'No phone',
                    $donation->amount,
                    $donation->payment_method,
                    $donation->status,
                    date('Y-m-d H:i:s', strtotime($donation->created_at)),
                    $donation->cause_title ?? 'Unknown Cause'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}