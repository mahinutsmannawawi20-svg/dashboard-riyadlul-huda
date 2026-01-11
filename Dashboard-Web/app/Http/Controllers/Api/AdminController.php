<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function trackingWithdrawals()
    {
        $withdrawals = Withdrawal::with(['user', 'bankAccount'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $withdrawals
        ]);
    }

    public function approveWithdrawal(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string',
            'proof_of_transfer' => 'nullable|file|image|max:5120', // Allow image, max 5MB
        ]);

        $withdrawal = Withdrawal::findOrFail($id);
        
        $proofUrl = null;
        if ($request->hasFile('proof_of_transfer')) {
            $file = $request->file('proof_of_transfer');
            
            try {
                // Initialize UploadThing Client
                $token = env('UPLOADTHING_TOKEN');
                if (!$token) {
                    throw new \Exception('UploadThing Token is not configured.');
                }
                
                $config = \UploadThing\Config::create()->withApiKey($token);
                $client = \UploadThing\Client::create($config);

                // Upload using UploadHelper
                // uploadFile returns UploadThing\Models\File
                $uploadedFile = $client->uploadHelper()->uploadFile(
                    $file->getPathname(), 
                    $file->getClientOriginalName()
                );
                
                $proofUrl = $uploadedFile->url;

            } catch (\Exception $e) {
                \Log::error('UploadThing Upload Failed: ' . $e->getMessage());
                 return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mengupload bukti transfer ke cloud: ' . $e->getMessage(),
                ], 500);
            }
        }

        $withdrawal->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'proof_of_transfer' => $proofUrl ?? $withdrawal->proof_of_transfer,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status penarikan berhasil diperbarui',
            'data' => $withdrawal
        ]);
    }
}
