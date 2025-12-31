<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Santri;
use App\Models\Syahriah;
use App\Models\ActivityLog;

class BackupController extends Controller
{
    /**
     * Download the database backup (SQL dump for MySQL/MariaDB).
     */
    public function download()
    {
        $connection = config('database.default');
        
        if ($connection === 'sqlite') {
            return $this->downloadSqlite();
        }
        
        return $this->downloadMysql();
    }

    /**
     * Download SQLite database file
     */
    private function downloadSqlite()
    {
        $dbPath = database_path('database.sqlite');

        if (!File::exists($dbPath)) {
            return back()->with('error', 'Database file not found.');
        }

        $filename = 'backup_' . date('Y-m-d_H-i-s') . '_riyadlul_huda.sqlite';

        return Response::download($dbPath, $filename, [
            'Content-Type' => 'application/x-sqlite3',
        ]);
    }

    /**
     * Download MySQL/MariaDB database dump
     */
    private function downloadMysql()
    {
        $host = config('database.connections.mysql.host');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $filename = 'backup_' . date('Y-m-d_H-i-s') . '_riyadlul_huda.sql';
        $tempPath = storage_path('app/' . $filename);

        // Build mysqldump command
        $command = sprintf(
            'mysqldump --host=%s --user=%s --password=%s %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($tempPath)
        );

        // Execute mysqldump
        exec($command, $output, $returnVar);

        if ($returnVar !== 0 || !File::exists($tempPath)) {
            return back()->with('error', 'Gagal membuat backup database. Pastikan mysqldump tersedia di server.');
        }

        // Download and delete temp file
        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Admin Dashboard - Overview of all system data
     */
    public function dashboard()
    {
        // User Statistics
        $userStats = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'sekretaris' => User::where('role', 'sekretaris')->count(),
            'bendahara' => User::where('role', 'bendahara')->count(),
            'pendidikan' => User::where('role', 'pendidikan')->count(),
        ];

        // Santri Statistics
        $santriStats = [
            'total' => Santri::count(),
            'aktif' => Santri::where('is_active', true)->count(),
            'nonaktif' => Santri::where('is_active', false)->count(),
            'putra' => Santri::where('gender', 'putra')->where('is_active', true)->count(),
            'putri' => Santri::where('gender', 'putri')->where('is_active', true)->count(),
        ];

        // Financial Statistics
        $currentYear = date('Y');
        $currentMonth = date('n');
        $financialStats = [
            'lunas_bulan_ini' => Syahriah::where('tahun', $currentYear)
                ->where('bulan', $currentMonth)
                ->where('is_lunas', true)
                ->count(),
            'tunggakan_bulan_ini' => Syahriah::where('tahun', $currentYear)
                ->where('bulan', $currentMonth)
                ->where('is_lunas', false)
                ->count(),
            'total_tunggakan' => Syahriah::where('is_lunas', false)->count(),
        ];

        // Recent Activities
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Database Info
        $dbInfo = [
            'connection' => config('database.default'),
            'database' => config('database.connections.' . config('database.default') . '.database'),
        ];

        // Get all users for management
        $users = User::orderBy('role')->orderBy('name')->get();

        return view('admin.dashboard', compact(
            'userStats',
            'santriStats',
            'financialStats',
            'recentActivities',
            'dbInfo',
            'users'
        ));
    }

    /**
     * Create a new user
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,sekretaris,bendahara,pendidikan',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return back()->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Update a user
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,sekretaris,bendahara,pendidikan',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return back()->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Delete a user
     */
    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus!');
    }
}
