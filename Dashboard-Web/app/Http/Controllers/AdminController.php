<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Display settings page
     */
    public function pengaturan()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        
        return view('admin.pengaturan', compact('users'));
    }

    /**
     * Update application settings
     */
    public function updateAppSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_name' => 'required|string|max:255',
            'app_contact' => 'nullable|string|max:255',
            'app_email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Store in session for now (can be moved to database later)
        session([
            'app_name' => $request->app_name,
            'app_contact' => $request->app_contact,
            'app_email' => $request->app_email,
        ]);

        return redirect()->route('admin.pengaturan')->with('success', 'Pengaturan aplikasi berhasil diperbarui!');
    }

    /**
     * Create new user
     */
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,sekretaris,bendahara,pendidikan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.pengaturan')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Update existing user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,sekretaris,bendahara,pendidikan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return redirect()->route('admin.pengaturan')->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        $user->delete();

        return redirect()->route('admin.pengaturan')->with('success', 'User berhasil dihapus!');
    }

    /**
     * Display all withdrawal requests
     */
    public function withdrawals()
    {
        $withdrawals = Withdrawal::with(['user', 'bankAccount'])->latest()->paginate(15);
        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    /**
     * Approve or reject a withdrawal request
     */
    public function approveWithdrawal(Request $request, $id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string',
            'proof_of_transfer' => 'nullable|image|max:2048',
        ]);

        $withdrawal->status = $validated['status'];
        $withdrawal->notes = $validated['notes'];
        $withdrawal->approved_by = Auth::id();
        $withdrawal->approved_at = now();

        if ($request->hasFile('proof_of_transfer')) {
            $path = $request->file('proof_of_transfer')->store('proofs', 'public');
            $withdrawal->proof_of_transfer = $path;
        }

        $withdrawal->save();

        return redirect()->route('admin.withdrawals')->with('success', 'Status penarikan berhasil diperbarui');
    }
}
