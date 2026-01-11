<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

use App\Models\BankAccount;
use App\Models\Withdrawal;
use App\Models\Santri;
use App\Models\Syahriah;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Pegawai;
use App\Models\GajiPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class BendaharaController extends Controller
{
    // Dashboard
    public function dashboard(Request $request)
    {
        // Get filter values
        $tahun = $request->filled('tahun') ? $request->tahun : now()->year;
        $bulan = $request->filled('bulan') ? $request->bulan : null;
        $kelasId = $request->filled('kelas_id') ? $request->kelas_id : null;
        $asramaId = $request->filled('asrama_id') ? $request->asrama_id : null;
        $kobongId = $request->filled('kobong_id') ? $request->kobong_id : null;
        $gender = $request->filled('gender') ? $request->gender : null;
        $statusLunas = $request->filled('status_lunas') ? $request->status_lunas : null;
        
        // Create cache key based on filters
        $cacheKey = "bendahara_dashboard_{$tahun}_{$bulan}_{$kelasId}_{$asramaId}_{$kobongId}_{$gender}_{$statusLunas}";
        
        // Cache KPI queries (5 minutes)
        $totalPemasukan = Cache::remember("{$cacheKey}_pemasukan", 300, function() use ($tahun, $bulan) {
            $query = Pemasukan::whereYear('tanggal', $tahun);
            if ($bulan) $query->whereMonth('tanggal', $bulan);
            return $query->sum('nominal');
        });
        
        $totalPengeluaran = Cache::remember("{$cacheKey}_pengeluaran", 300, function() use ($tahun, $bulan) {
            $query = Pengeluaran::whereYear('tanggal', $tahun);
            if ($bulan) $query->whereMonth('tanggal', $bulan);
            return $query->sum('nominal');
        });
        
        $syriahManual = Cache::remember("{$cacheKey}_syahriah_manual", 300, function() use ($tahun, $bulan) {
            $query = Syahriah::where('tahun', $tahun)->where('is_lunas', true)
                ->where(function($q) {
                    $q->whereNull('keterangan')
                      ->orWhere('keterangan', 'not like', '%Midtrans%');
                });
            if ($bulan) $query->where('bulan', $bulan);
            return $query->sum('nominal');
        });

        $syahriahGateway = Cache::remember("{$cacheKey}_syahriah_gateway", 300, function() use ($tahun, $bulan) {
            $query = Syahriah::where('tahun', $tahun)->where('is_lunas', true)
                ->where('keterangan', 'like', '%Midtrans%');
            if ($bulan) $query->where('bulan', $bulan);
            return $query->sum('nominal');
        });

        $syahriahData = Cache::remember("{$cacheKey}_syahriah", 300, function() use ($tahun, $bulan) {
            $query = Syahriah::where('tahun', $tahun);
            if ($bulan) $query->where('bulan', $bulan);
            return [
                'total' => $query->sum('nominal'),
                'lunas' => (clone $query)->where('is_lunas', true)->sum('nominal')
            ];
        });
        
        $totalSyahriah = $syahriahData['total'];
        $syahriahLunas = $syahriahData['lunas'];
        $saldoDana = ($totalPemasukan + $syahriahLunas) - $totalPengeluaran;
        
        // Santri counts (cached)
        $santriCounts = Cache::remember("{$cacheKey}_santri_counts", 300, function() use ($kelasId, $asramaId, $kobongId, $gender) {
            $query = Santri::where('is_active', true);
            if ($kelasId) $query->where('kelas_id', $kelasId);
            if ($asramaId) $query->where('asrama_id', $asramaId);
            if ($kobongId) $query->where('kobong_id', $kobongId);
            if ($gender) $query->where('gender', $gender);
            
            return [
                'total' => $query->count(),
                'putra' => (clone $query)->where('gender', 'putra')->count(),
                'putri' => (clone $query)->where('gender', 'putri')->count()
            ];
        });
        
        $totalSantriAktif = $santriCounts['total'];
        $totalSantriPutra = $santriCounts['putra'];
        $totalSantriPutri = $santriCounts['putri'];
        
        // Tunggakan calculation (cached for 5 minutes - this is expensive!)
        $tunggakanData = Cache::remember("{$cacheKey}_tunggakan", 300, function() {
            $biayaBulanan = 500000;
            $endDate = now();
            $totalTunggakan = 0;
            $totalSantriMenunggak = 0;
            
            $allSantri = Santri::where('is_active', true)->get();
            foreach ($allSantri as $santri) {
                $startDate = $santri->tanggal_masuk ?? $santri->created_at;
                $allMonths = [];
                $current = $startDate->copy()->startOfMonth();
                while ($current <= $endDate) {
                    $allMonths[] = $current->month . '-' . $current->year;
                    $current->addMonth();
                }

                $paidMonths = Syahriah::where('santri_id', $santri->id)
                    ->where('is_lunas', true)
                    ->get()
                    ->map(fn($item) => $item->bulan . '-' . $item->tahun)
                    ->toArray();

                $unpaidCount = 0;
                foreach ($allMonths as $monthKey) {
                    if (!in_array($monthKey, $paidMonths)) {
                        $unpaidCount++;
                    }
                }

                if ($unpaidCount > 0) {
                    $totalTunggakan += $unpaidCount * $biayaBulanan;
                    $totalSantriMenunggak++;
                }
            }
            
            return [
                'total' => $totalTunggakan,
                'count' => $totalSantriMenunggak
            ];
        });
        
        $totalTunggakan = $tunggakanData['total'];
        $totalSantriMenunggak = $tunggakanData['count'];
        
        // Santri lunas (cached)
        $santriLunas = Cache::remember("{$cacheKey}_santri_lunas", 300, function() use ($tahun, $bulan) {
            $query = Syahriah::where('tahun', $tahun);
            if ($bulan) $query->where('bulan', $bulan);
            $santriIdsLunas = $query->where('is_lunas', true)->pluck('santri_id')->unique();
            
            return [
                'putra' => Santri::whereIn('id', $santriIdsLunas)->where('gender', 'putra')->where('is_active', true)->count(),
                'putri' => Santri::whereIn('id', $santriIdsLunas)->where('gender', 'putri')->where('is_active', true)->count()
            ];
        });
        
        $totalSantriPutraLunas = $santriLunas['putra'];
        $totalSantriPutriLunas = $santriLunas['putri'];
        
        // Gaji data (cached)
        $gajiData = Cache::remember("{$cacheKey}_gaji", 300, function() use ($tahun, $bulan) {
            $query = GajiPegawai::where('tahun', $tahun);
            if ($bulan) $query->where('bulan', $bulan);
            
            return [
                'bulan_ini' => (clone $query)->where('bulan', now()->month)->sum('nominal'),
                'tertunda' => (clone $query)->where('is_dibayar', false)->sum('nominal')
            ];
        });
        
        $totalGajiBulanIni = $gajiData['bulan_ini'];
        $totalGajiTertunda = $gajiData['tertunda'];
        
        // Chart Data (cached for 10 minutes)
        $chartPemasukanPengeluaran = Cache::remember("chart_pemasukan_pengeluaran_{$tahun}", 600, function() use ($tahun) {
            return $this->getChartPemasukanPengeluaran($tahun);
        });
        
        $chartPerAsrama = Cache::remember('chart_per_asrama', 600, function() {
            return $this->getChartPerAsrama();
        });
        
        $chartPerKelas = Cache::remember('chart_per_kelas', 600, function() {
            return $this->getChartPerKelas();
        });
        
        $chartDistribusiSantri = ['putra' => $totalSantriPutra, 'putri' => $totalSantriPutri];
        $chartLunasMenunggak = ['lunas' => $syahriahLunas, 'menunggak' => $totalTunggakan];
        
        // Lists - Santri Menunggak (cached for 2 minutes for fresher data)
        $santriMenunggak = Cache::remember("{$cacheKey}_santri_menunggak", 120, function() use ($tahun, $bulan) {
            $query = Syahriah::where('tahun', $tahun);
            if ($bulan) $query->where('bulan', $bulan);
            $santriIdsMenunggak = $query->where('is_lunas', false)->pluck('santri_id')->unique();
            
            return [
                'putra' => Santri::whereIn('id', $santriIdsMenunggak)
                    ->where('gender', 'putra')
                    ->where('is_active', true)
                    ->with(['kelas', 'asrama'])
                    ->limit(10)
                    ->get(),
                'putri' => Santri::whereIn('id', $santriIdsMenunggak)
                    ->where('gender', 'putri')
                    ->where('is_active', true)
                    ->with(['kelas', 'asrama'])
                    ->limit(10)
                    ->get()
            ];
        });
        
        $santriPutraMenunggak = $santriMenunggak['putra'];
        $santriPutriMenunggak = $santriMenunggak['putri'];
        
        // Recent Transactions (cached for 1 minute)
        $recentSyahriah = Cache::remember('recent_syahriah', 60, function() {
            return Syahriah::with('santri:id,nama_santri')
                ->select('id', 'santri_id', 'bulan', 'tahun', 'nominal', 'is_lunas', 'created_at')
                ->latest()
                ->limit(10)
                ->get();
        });
        
        $recentPemasukan = Cache::remember('recent_pemasukan', 60, function() {
            return Pemasukan::select('id', 'tanggal', 'kategori', 'nominal', 'keterangan', 'created_at')
                ->latest()
                ->limit(5)
                ->get();
        });
        
        $recentPengeluaran = Cache::remember('recent_pengeluaran', 60, function() {
            return Pengeluaran::select('id', 'tanggal', 'jenis_pengeluaran', 'nominal', 'keterangan', 'created_at')
                ->latest()
                ->limit(5)
                ->get();
        });
        
        $recentGaji = Cache::remember('recent_gaji', 60, function() {
            return GajiPegawai::with('pegawai:id,nama_pegawai')
                ->select('id', 'pegawai_id', 'bulan', 'tahun', 'nominal', 'is_dibayar', 'created_at')
                ->latest()
                ->limit(5)
                ->get();
        });
        
        // Module Summaries (cached)
        $totalPegawai = Cache::remember('total_pegawai', 300, function() {
            return Pegawai::where('is_active', true)->count();
        });
        
        $gajiTertundaCount = Cache::remember('gaji_tertunda_count', 300, function() {
            return GajiPegawai::where('is_dibayar', false)->count();
        });
        
        // This Month Summaries (cached for 2 minutes)
        $bulanIniData = Cache::remember('bulan_ini_data', 120, function() {
            return [
                'syahriah' => Syahriah::where('tahun', now()->year)->where('bulan', now()->month)->sum('nominal'),
                'pemasukan' => Pemasukan::whereYear('tanggal', now()->year)->whereMonth('tanggal', now()->month)->sum('nominal'),
                'pengeluaran' => Pengeluaran::whereYear('tanggal', now()->year)->whereMonth('tanggal', now()->month)->sum('nominal')
            ];
        });
        
        $syahriahBulanIni = $bulanIniData['syahriah'];
        $pemasukanBulanIni = $bulanIniData['pemasukan'];
        $pengeluaranBulanIni = $bulanIniData['pengeluaran'];
        
        // Filter Options (cached for 15 minutes)
        $kelasList = Cache::remember('bendahara_kelas_list', 900, function() {
            return \App\Models\Kelas::all();
        });
        
        $asramaList = Cache::remember('bendahara_asrama_list', 900, function() {
            return \App\Models\Asrama::all();
        });
        
        $kobongList = Cache::remember('bendahara_kobong_list', 900, function() {
            return \App\Models\Kobong::all();
        });
        
        return view('bendahara.dashboard', compact(
            'syriahManual', 'syahriahGateway',
            'saldoDana', 'totalPemasukan', 'totalPengeluaran',
            'totalSantriAktif', 'totalSantriPutra', 'totalSantriPutri',
            'totalSantriPutraLunas', 'totalSantriPutriLunas',
            'totalSyahriah', 'totalTunggakan',
            'totalGajiBulanIni', 'totalGajiTertunda',
            'chartPemasukanPengeluaran', 'chartPerAsrama', 'chartPerKelas',
            'chartDistribusiSantri', 'chartLunasMenunggak',
            'santriPutraMenunggak', 'santriPutriMenunggak',
            'recentSyahriah', 'recentPemasukan', 'recentPengeluaran', 'recentGaji',
            'totalPegawai', 'gajiTertundaCount',
            'syahriahBulanIni', 'pemasukanBulanIni', 'pengeluaranBulanIni',
            'kelasList', 'asramaList', 'kobongList',
            'tahun', 'bulan', 'kelasId', 'asramaId', 'kobongId', 'gender', 'statusLunas'
        ));
    }

    // Bank Account Methods
    public function bankAccounts()
    {
        $accounts = BankAccount::latest()->get();
        return view('bendahara.bank-accounts.index', compact('accounts'));
    }

    public function storeBankAccount(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
        ]);

        BankAccount::create($validated);
        return redirect()->back()->with('success', 'Rekening bank berhasil ditambahkan');
    }

    public function updateBankAccount(Request $request, $id)
    {
        $account = BankAccount::findOrFail($id);
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $account->update($validated);
        return redirect()->back()->with('success', 'Rekening bank berhasil diperbarui');
    }

    public function destroyBankAccount($id)
    {
        BankAccount::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Rekening bank berhasil dihapus');
    }

    // Withdrawal Methods
    public function withdrawals()
    {
        $withdrawals = Withdrawal::where('user_id', Auth::id())->with('bankAccount')->latest()->paginate(15);
        $bankAccounts = BankAccount::where('is_active', true)->get();
        return view('bendahara.withdrawals.index', compact('withdrawals', 'bankAccounts'));
    }

    public function storeWithdrawal(Request $request)
    {
        if ($request->filled('amount')) {
            $request->merge(['amount' => str_replace('.', '', $request->amount)]);
        }

        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:1000',
            'notes' => 'nullable|string',
        ]);

        Withdrawal::create([
            'user_id' => Auth::id(),
            'bank_account_id' => $validated['bank_account_id'],
            'amount' => $validated['amount'],
            'notes' => $validated['notes'],
            'status' => 'pending',
        ]);

        return redirect()->route('bendahara.withdrawals')->with('success', 'Pengajuan penarikan berhasil dikirim');
    }
    
    private function getChartPemasukanPengeluaran($tahun)
    {
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data['pemasukan'][] = Pemasukan::whereYear('tanggal', $tahun)->whereMonth('tanggal', $i)->sum('nominal');
            $data['pengeluaran'][] = Pengeluaran::whereYear('tanggal', $tahun)->whereMonth('tanggal', $i)->sum('nominal');
        }
        return $data;
    }
    
    private function getChartPerAsrama()
    {
        return \App\Models\Asrama::withCount('santri')->get()->mapWithKeys(function($asrama) {
            return [$asrama->nama_asrama => $asrama->santri_count];
        })->toArray();
    }
    
    private function getChartPerKelas()
    {
        return \App\Models\Kelas::withCount('santri')->get()->mapWithKeys(function($kelas) {
            return [$kelas->nama_kelas => $kelas->santri_count];
        })->toArray();
    }
    
    // Data Santri (Read-only from master)
    public function dataSantri(Request $request)
    {
        $query = Santri::with(['kelas', 'asrama'])->where('is_active', true);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhere('nama_santri', 'like', "%{$search}%");
            });
        }
        
        $santri = $query->latest()->paginate(35);
        
        return view('bendahara.data-santri', compact('santri'));
    }
    
    // Syahriah - Index
    public function syahriah(Request $request)
    {
        $query = Syahriah::with('santri');
        
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('is_lunas')) {
            $query->where('is_lunas', $request->is_lunas);
        }
        
        $syahriah = $query->latest()->paginate(15);
        $santriList = Santri::where('is_active', true)->with(['asrama', 'kobong'])->get();
        $asramaList = \App\Models\Asrama::all();
        $kobongList = \App\Models\Kobong::with('asrama')->get();
        
        return view('bendahara.syahriah.index', compact('syahriah', 'santriList', 'asramaList', 'kobongList'));
    }
    
    // Syahriah - Store
    public function storeSyahriah(Request $request)
    {
        if ($request->filled('nominal')) {
            $request->merge(['nominal' => str_replace('.', '', $request->nominal)]);
        }

        $validated = $request->validate([
            'santri_id' => 'required|exists:santri,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer',
            'nominal' => 'required|numeric|min:0',
            'is_lunas' => 'required|boolean',
            'tanggal_bayar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);
        
        // Check for duplicate entry
        $existingSyahriah = Syahriah::where('santri_id', $validated['santri_id'])
            ->where('bulan', $validated['bulan'])
            ->where('tahun', $validated['tahun'])
            ->first();
        
        if ($existingSyahriah) {
            $santri = Santri::find($validated['santri_id']);
            $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                          'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $statusLunas = $existingSyahriah->is_lunas ? 'LUNAS' : 'BELUM LUNAS';
            
            return redirect()->route('bendahara.syahriah')
                ->with('warning', "Pembayaran untuk {$santri->nama_santri} bulan {$bulanNama[$validated['bulan']]} {$validated['tahun']} sudah tercatat sebelumnya dengan status: {$statusLunas}. Silakan edit data yang sudah ada jika ingin mengubah.");
        }
        
        $syahriah = Syahriah::create($validated);
        
        // Send Telegram notification for payment
        if ($validated['is_lunas']) {
            try {
                $telegram = new \App\Services\TelegramService();
                $santri = Santri::with(['kelas', 'asrama'])->find($validated['santri_id']);
                $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                
                // Calculate remaining arrears (sisa tunggakan)
                $biayaBulanan = 500000;
                $startDate = $santri->tanggal_masuk ?? $santri->created_at;
                $endDate = now();
                $allMonths = [];
                $current = $startDate->copy()->startOfMonth();
                while ($current <= $endDate) {
                    $allMonths[] = $current->month . '-' . $current->year;
                    $current->addMonth();
                }
                $paidMonths = Syahriah::where('santri_id', $santri->id)
                    ->where('is_lunas', true)
                    ->get()
                    ->map(fn($item) => $item->bulan . '-' . $item->tahun)
                    ->toArray();
                $unpaidCount = 0;
                foreach ($allMonths as $monthKey) {
                    if (!in_array($monthKey, $paidMonths)) {
                        $unpaidCount++;
                    }
                }
                $sisaTunggakan = $unpaidCount * $biayaBulanan;
                
                $telegram->notifyPaymentReceived([
                    'nama_santri' => $santri->nama_santri ?? '-',
                    'gender' => ucfirst($santri->gender ?? '-'),
                    'kelas' => $santri->kelas->nama_kelas ?? '-',
                    'asrama' => $santri->asrama->nama_asrama ?? '-',
                    'jumlah' => $validated['nominal'],
                    'keterangan' => "SPP {$bulanNama[$validated['bulan']]} {$validated['tahun']}",
                    'sisa_tunggakan' => $sisaTunggakan,
                ]);
            } catch (\Exception $e) {
                Log::warning('Telegram notification failed: ' . $e->getMessage());
            }
        }
        
        return redirect()->route('bendahara.syahriah')
            ->with('success', 'Data syahriah berhasil ditambahkan');
    }
    
    // Syahriah - Update
    public function updateSyahriah(Request $request, $id)
    {
        $syahriah = Syahriah::findOrFail($id);
        
        $validated = $request->validate([
            'is_lunas' => 'required|boolean',
            'tanggal_bayar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);
        
        $syahriah->update($validated);
        
        return redirect()->route('bendahara.syahriah')
            ->with('success', 'Data syahriah berhasil diperbarui');
    }
    
    // Syahriah - Delete
    public function destroySyahriah($id)
    {
        $syahriah = Syahriah::findOrFail($id);
        $syahriah->delete();
        
        return redirect()->route('bendahara.syahriah')
            ->with('success', 'Data syahriah berhasil dihapus');
    }
    
    // Pemasukan - Index
    public function pemasukan(Request $request)
    {
        $query = Pemasukan::query();
        
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->where('tanggal', '<=', $request->tanggal_selesai);
        }
        
        $pemasukan = $query->latest('tanggal')->paginate(15);
        
        return view('bendahara.pemasukan.index', compact('pemasukan'));
    }
    
    // Pemasukan - Store
    public function storePemasukan(Request $request)
    {
        if ($request->filled('nominal')) {
            $request->merge(['nominal' => str_replace('.', '', $request->nominal)]);
        }

        $validated = $request->validate([
            'sumber_pemasukan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'kategori' => 'required|string',
        ]);
        
        $pemasukan = Pemasukan::create($validated);
        
        // Send Telegram notification
        try {
            $telegram = new \App\Services\TelegramService();
            $telegram->notify(
                'PEMASUKAN BARU',
                "💵 Sumber: {$validated['sumber_pemasukan']}\n" .
                "💰 Nominal: Rp " . number_format($validated['nominal'], 0, ',', '.') . "\n" .
                "📝 Kategori: {$validated['kategori']}\n" .
                "📅 Tanggal: " . date('d M Y', strtotime($validated['tanggal'])),
                '📥'
            );
        } catch (\Exception $e) {
            Log::warning('Telegram notification failed: ' . $e->getMessage());
        }

        // WA NOTIFICATION (Admin Group)
        try {
            $adminGroupId = env('FONNTE_ADMIN_GROUP_ID');
            if ($adminGroupId) {
                $fonnteService = app(\App\Services\FonnteService::class);
                $fonnteService->notifyIncome(
                    $adminGroupId,
                    $validated['sumber_pemasukan'],
                    $request->kategori_lain ?? $validated['kategori'],
                    str_replace('.', '', $request->nominal), // Raw nominal
                    $validated['tanggal'],
                    $validated['keterangan'] ?? '-',
                    Auth::user()->name ?? 'Admin'
                );
            }
        } catch (\Exception $e) {
            Log::warning('WA Notification failed: ' . $e->getMessage());
        }
        
        return redirect()->route('bendahara.pemasukan')
            ->with('success', 'Data pemasukan berhasil ditambahkan');
    }
    
    // Pemasukan - Update
    public function updatePemasukan(Request $request, $id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        
        if ($request->filled('nominal')) {
            $request->merge(['nominal' => str_replace('.', '', $request->nominal)]);
        }

        $validated = $request->validate([
            'sumber_pemasukan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'kategori' => 'required|string',
        ]);
        
        $pemasukan->update($validated);
        
        return redirect()->route('bendahara.pemasukan')
            ->with('success', 'Data pemasukan berhasil diperbarui');
    }
    
    // Pemasukan - Delete
    public function destroyPemasukan($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->delete();
        
        return redirect()->route('bendahara.pemasukan')
            ->with('success', 'Data pemasukan berhasil dihapus');
    }
    
    // Pengeluaran - Index
    public function pengeluaran(Request $request)
    {
        $query = Pengeluaran::query();
        
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->where('tanggal', '<=', $request->tanggal_selesai);
        }
        
        $pengeluaran = $query->latest('tanggal')->paginate(15);
        
        return view('bendahara.pengeluaran.index', compact('pengeluaran'));
    }
    
    // Pengeluaran - Store
    public function storePengeluaran(Request $request)
    {
        if ($request->filled('nominal')) {
            $request->merge(['nominal' => str_replace('.', '', $request->nominal)]);
        }

        $validated = $request->validate([
            'jenis_pengeluaran' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'kategori' => 'required|string',
        ]);
        
        $pengeluaran = Pengeluaran::create($validated);
        
        // Send Telegram notification
        try {
            $telegram = new \App\Services\TelegramService();
            $telegram->notify(
                'PENGELUARAN BARU',
                "🏷️ Jenis: {$validated['jenis_pengeluaran']}\n" .
                "💸 Nominal: Rp " . number_format($validated['nominal'], 0, ',', '.') . "\n" .
                "📝 Kategori: {$validated['kategori']}\n" .
                "📅 Tanggal: " . date('d M Y', strtotime($validated['tanggal'])),
                '📤'
            );
        } catch (\Exception $e) {
            Log::warning('Telegram notification failed: ' . $e->getMessage());
        }

        // WA NOTIFICATION (Admin Group)
        try {
            $adminGroupId = env('FONNTE_ADMIN_GROUP_ID');
            if ($adminGroupId) {
                $fonnteService = app(\App\Services\FonnteService::class);
                $fonnteService->notifyExpense(
                    $adminGroupId,
                    $validated['jenis_pengeluaran'],
                    $request->kategori_lain ?? $validated['kategori'],
                    str_replace('.', '', $request->nominal), // Raw nominal
                    $validated['tanggal'],
                    $validated['keterangan'] ?? '-',
                    Auth::user()->name ?? 'Admin'
                );
            }
        } catch (\Exception $e) {
            Log::warning('WA Notification failed: ' . $e->getMessage());
        }
        
        return redirect()->route('bendahara.pengeluaran')
            ->with('success', 'Data pengeluaran berhasil ditambahkan');
    }
    
    // Pengeluaran - Update
    public function updatePengeluaran(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        
        if ($request->filled('nominal')) {
            $request->merge(['nominal' => str_replace('.', '', $request->nominal)]);
        }

        $validated = $request->validate([
            'jenis_pengeluaran' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'kategori' => 'required|string',
        ]);
        
        $pengeluaran->update($validated);
        
        return redirect()->route('bendahara.pengeluaran')
            ->with('success', 'Data pengeluaran berhasil diperbarui');
    }
    
    // Pengeluaran - Delete
    public function destroyPengeluaran($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->delete();
        
        return redirect()->route('bendahara.pengeluaran')
            ->with('success', 'Data pengeluaran berhasil dihapus');
    }
    
    // Pegawai - Index
    public function pegawai()
    {
        $pegawai = Pegawai::latest()->paginate(15);
        
        return view('bendahara.pegawai.index', compact('pegawai'));
    }
    
    // Pegawai - Store
    public function storePegawai(Request $request)
    {
        $validated = $request->validate([
            'nama_pegawai' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);
        
        Pegawai::create($validated);
        
        return redirect()->route('bendahara.pegawai')
            ->with('success', 'Data pegawai berhasil ditambahkan');
    }
    
    // Pegawai - Update
    public function updatePegawai(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        
        $validated = $request->validate([
            'nama_pegawai' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'departemen' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);
        
        $pegawai->update($validated);
        
        return redirect()->route('bendahara.pegawai')
            ->with('success', 'Data pegawai berhasil diperbarui');
    }
    
    // Pegawai - Delete
    public function destroyPegawai($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();
        
        return redirect()->route('bendahara.pegawai')
            ->with('success', 'Data pegawai berhasil dihapus');
    }
    
    // Gaji - Index
    public function gaji(Request $request)
    {
        $query = GajiPegawai::with('pegawai');
        
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        
        $gaji = $query->latest()->paginate(15);
        $pegawaiList = Pegawai::where('is_active', true)->get();
        
        return view('bendahara.gaji.index', compact('gaji', 'pegawaiList'));
    }
    
    // Gaji - Store
    public function storeGaji(Request $request)
    {
        if ($request->filled('nominal')) {
            $request->merge(['nominal' => str_replace('.', '', $request->nominal)]);
        }

        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer',
            'nominal' => 'required|numeric|min:0',
            'is_dibayar' => 'required|boolean',
            'tanggal_bayar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);
        
        $gaji = GajiPegawai::create($validated);
        
        // Send Telegram notification for salary payment
        if ($validated['is_dibayar']) {
            try {
                $telegram = new \App\Services\TelegramService();
                $pegawai = Pegawai::find($validated['pegawai_id']);
                $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                
                $telegram->notify(
                    'GAJI DIBAYAR',
                    "👤 Pegawai: {$pegawai->nama_pegawai}\n" .
                    "💼 Jabatan: {$pegawai->jabatan}\n" .
                    "💰 Nominal: Rp " . number_format($validated['nominal'], 0, ',', '.') . "\n" .
                    "📅 Periode: {$bulanNama[$validated['bulan']]} {$validated['tahun']}",
                    '💵'
                );
            } catch (\Exception $e) {
                Log::warning('Telegram notification failed: ' . $e->getMessage());
            }
        }
        
        return redirect()->route('bendahara.gaji')
            ->with('success', 'Data gaji berhasil ditambahkan');
    }
    
    // Gaji - Update
    public function updateGaji(Request $request, $id)
    {
        $gaji = GajiPegawai::findOrFail($id);
        
        $validated = $request->validate([
            'is_dibayar' => 'required|boolean',
            'tanggal_bayar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);
        
        $gaji->update($validated);
        
        return redirect()->route('bendahara.gaji')
            ->with('success', 'Data gaji berhasil diperbarui');
    }
    
    // Gaji - Delete
    public function destroyGaji($id)
    {
        $gaji = GajiPegawai::findOrFail($id);
        $gaji->delete();
        
        return redirect()->route('bendahara.gaji')
            ->with('success', 'Data gaji berhasil dihapus');
    }
    
    // Laporan
    public function laporan()
    {
        return view('bendahara.laporan');
    }
    
    // Export Laporan Syahriah
    public function exportLaporanSyahriah(Request $request)
    {
        $query = Syahriah::with('santri');
        
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        
        $syahriah = $query->latest()->get();
        $totalNominal = $syahriah->sum('nominal');
        $totalLunas = $syahriah->where('is_lunas', true)->sum('nominal');
        $totalBelumLunas = $totalNominal - $totalLunas;
        
        return response()->view('bendahara.exports.laporan-syahriah', compact('syahriah', 'totalNominal', 'totalLunas', 'totalBelumLunas', 'request'))
            ->header('Content-Type', 'text/html');
    }
    
    // Export Laporan Pemasukan
    public function exportLaporanPemasukan(Request $request)
    {
        $query = Pemasukan::query();
        
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->where('tanggal', '<=', $request->tanggal_selesai);
        }
        
        $pemasukan = $query->latest('tanggal')->get();
        $totalPemasukan = $pemasukan->sum('nominal');
        
        return response()->view('bendahara.exports.laporan-pemasukan', compact('pemasukan', 'totalPemasukan', 'request'))
            ->header('Content-Type', 'text/html');
    }
    
    // Export Laporan Pengeluaran
    public function exportLaporanPengeluaran(Request $request)
    {
        $query = Pengeluaran::query();
        
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->where('tanggal', '<=', $request->tanggal_selesai);
        }
        
        $pengeluaran = $query->latest('tanggal')->get();
        $totalPengeluaran = $pengeluaran->sum('nominal');
        
        return response()->view('bendahara.exports.laporan-pengeluaran', compact('pengeluaran', 'totalPengeluaran', 'request'))
            ->header('Content-Type', 'text/html');
    }
    
    // Export Laporan Kas
    public function exportLaporanKas(Request $request)
    {
        $queryPemasukan = Pemasukan::query();
        $queryPengeluaran = Pengeluaran::query();
        
        if ($request->filled('tanggal_mulai')) {
            $queryPemasukan->where('tanggal', '>=', $request->tanggal_mulai);
            $queryPengeluaran->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $queryPemasukan->where('tanggal', '<=', $request->tanggal_selesai);
            $queryPengeluaran->where('tanggal', '<=', $request->tanggal_selesai);
        }
        
        $pemasukan = $queryPemasukan->latest('tanggal')->get();
        $pengeluaran = $queryPengeluaran->latest('tanggal')->get();
        
        $totalPemasukan = $pemasukan->sum('nominal');
        $totalPengeluaran = $pengeluaran->sum('nominal');
        $saldoKas = $totalPemasukan - $totalPengeluaran;
        
        return response()->view('bendahara.exports.laporan-kas', compact('pemasukan', 'pengeluaran', 'totalPemasukan', 'totalPengeluaran', 'saldoKas', 'request'))
            ->header('Content-Type', 'text/html');
    }
    
    // Export Laporan Gaji
    public function exportLaporanGaji(Request $request)
    {
        $query = GajiPegawai::with('pegawai');
        
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        
        $gaji = $query->latest()->get();
        $totalGaji = $gaji->sum('nominal');
        $totalDibayar = $gaji->where('is_dibayar', true)->sum('nominal');
        $totalBelumDibayar = $totalGaji - $totalDibayar;
        
        return response()->view('bendahara.exports.laporan-gaji', compact('gaji', 'totalGaji', 'totalDibayar', 'totalBelumDibayar', 'request'))
            ->header('Content-Type', 'text/html');
    }
    
    // Export Laporan Keuangan Lengkap
    public function exportLaporanKeuanganLengkap(Request $request)
    {
        $querySyahriah = Syahriah::with('santri');
        $queryPemasukan = Pemasukan::query();
        $queryPengeluaran = Pengeluaran::query();
        $queryGaji = GajiPegawai::with('pegawai');
        
        if ($request->filled('tahun')) {
            $querySyahriah->where('tahun', $request->tahun);
            $queryGaji->where('tahun', $request->tahun);
            $queryPemasukan->whereYear('tanggal', $request->tahun);
            $queryPengeluaran->whereYear('tanggal', $request->tahun);
        }
        
        $syahriah = $querySyahriah->latest()->get();
        $pemasukan = $queryPemasukan->latest('tanggal')->get();
        $pengeluaran = $queryPengeluaran->latest('tanggal')->get();
        $gaji = $queryGaji->latest()->get();
        
        $totalSyahriah = $syahriah->sum('nominal');
        $totalPemasukan = $pemasukan->sum('nominal');
        $totalPengeluaran = $pengeluaran->sum('nominal');
        $totalGaji = $gaji->sum('nominal');
        $saldoKas = $totalPemasukan - $totalPengeluaran;
        
        return response()->view('bendahara.exports.laporan-keuangan-lengkap', compact(
            'syahriah', 'pemasukan', 'pengeluaran', 'gaji',
            'totalSyahriah', 'totalPemasukan', 'totalPengeluaran', 'totalGaji', 'saldoKas',
            'request'
        ))->header('Content-Type', 'text/html');
    }

    // Cek Tunggakan (New Feature)
    // Cek Tunggakan (New Feature)
    public function cekTunggakan(Request $request)
    {
        $query = Santri::where('is_active', true)->with(['kelas', 'asrama', 'kobong']);

        // Apply Filters
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('asrama_id')) {
            $query->where('asrama_id', $request->asrama_id);
        }
        if ($request->filled('kobong_id')) {
            $query->where('kobong_id', $request->kobong_id);
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $santriList = $query->orderBy('nama_santri')->get();
        
        // Data for Filters
        $kelasList = \App\Models\Kelas::all();
        $asramaList = \App\Models\Asrama::all();
        $kobongList = \App\Models\Kobong::all();

        // Calculate Arrears for All Santri
        $biayaBulanan = 500000; // Default Rp 500.000
        $endDate = now();
        $santriWithArrears = [];

        foreach ($santriList as $santri) {
            $startDate = $santri->tanggal_masuk ?? $santri->created_at;
            
            // Generate all months from start to now
            $allMonths = [];
            $current = $startDate->copy()->startOfMonth();
            while ($current <= $endDate) {
                $allMonths[] = $current->month . '-' . $current->year;
                $current->addMonth();
            }

            // Get paid months
            $paidMonths = Syahriah::where('santri_id', $santri->id)
                ->where('is_lunas', true)
                ->get()
                ->map(fn($item) => $item->bulan . '-' . $item->tahun)
                ->toArray();

            // Count unpaid months
            $unpaidCount = 0;
            foreach ($allMonths as $monthKey) {
                if (!in_array($monthKey, $paidMonths)) {
                    $unpaidCount++;
                }
            }

            // Only include santri with arrears
            if ($unpaidCount > 0) {
                $santriWithArrears[] = [
                    'santri' => $santri,
                    'unpaid_months' => $unpaidCount,
                    'total_arrears' => $unpaidCount * $biayaBulanan,
                ];
            }
        }

        // Calculate Summary Stats (before pagination)
        $totalSantriMenunggak = count($santriWithArrears);
        $grandTotalBulan = array_sum(array_column($santriWithArrears, 'unpaid_months'));
        $grandTotalRupiah = array_sum(array_column($santriWithArrears, 'total_arrears'));

        // Pagination (manual for array)
        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedItems = array_slice($santriWithArrears, $offset, $perPage);
        $santriWithArrearsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $totalSantriMenunggak,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('bendahara.cek-tunggakan.index', compact(
            'santriList', 'kelasList', 'asramaList', 'kobongList', 
            'santriWithArrearsPaginated', 'biayaBulanan',
            'totalSantriMenunggak', 'grandTotalBulan', 'grandTotalRupiah'
        ));
    }

    public function prosesCekTunggakan(Request $request)
    {
        // Sanitize input (remove dots, 'Rp', spaces, etc. - keep only digits)
        if ($request->has('biaya_bulanan')) {
            $request->merge([
                'biaya_bulanan' => preg_replace('/[^0-9]/', '', $request->biaya_bulanan)
            ]);
        }

        $request->validate([
            'santri_id' => 'required|exists:santri,id',
            'biaya_bulanan' => 'required|numeric|min:0',
        ]);

        $santri = Santri::with(['kelas', 'asrama'])->findOrFail($request->santri_id);
        
        // Determine Start Date (Support Legacy Data)
        // Logic: 
        // 1. If tanggal_masuk exists, use it.
        // 2. If not, use created_at.
        $startDate = $santri->tanggal_masuk ?? $santri->created_at;
        $endDate = now();

        $biayaBulanan = str_replace('.', '', $request->biaya_bulanan);
        
        // Generate List of All Months from Start to End
        $allMonths = [];
        $current = $startDate->copy()->startOfMonth();
        
        while ($current <= $endDate) {
            $allMonths[] = [
                'bulan' => $current->month,
                'tahun' => $current->year,
                'label' => $current->format('F Y'),
                'date_obj' => $current->copy()
            ];
            $current->addMonth();
        }

        // Get All Paid Months for this Santri
        $paidMonths = Syahriah::where('santri_id', $santri->id)
            ->where('is_lunas', true)
            ->get()
            ->map(function ($item) {
                return $item->bulan . '-' . $item->tahun;
            })
            ->toArray();

        // Calculate Arrears (Tunggakan)
        $tunggakanList = [];
        $totalTunggakan = 0;

        foreach ($allMonths as $month) {
            $key = $month['bulan'] . '-' . $month['tahun'];
            
            if (!in_array($key, $paidMonths)) {
                // Check if there is a partial payment or unpaid record
                $record = Syahriah::where('santri_id', $santri->id)
                    ->where('bulan', $month['bulan'])
                    ->where('tahun', $month['tahun'])
                    ->first();
                
                $status = 'Belum Bayar';
                $nominalBayar = 0;
                
                if ($record) {
                    $status = $record->is_lunas ? 'Lunas' : 'Belum Lunas'; // Should be covered by paidMonths check, but safe double check
                    $nominalBayar = $record->nominal;
                }

                $tunggakanList[] = [
                    'bulan' => $month['bulan'],
                    'tahun' => $month['tahun'],
                    'label' => $month['label'],
                    'status' => $status,
                    'tagihan' => $biayaBulanan
                ];
                
                $totalTunggakan += $biayaBulanan;
            }
        }

        return view('bendahara.cek-tunggakan.result', compact('santri', 'tunggakanList', 'totalTunggakan', 'biayaBulanan'));
    }

    public function exportLaporanTunggakan(Request $request)
    {
        $query = Santri::where('is_active', true)->with(['kelas', 'asrama', 'kobong']);

        // Apply Filters
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('asrama_id')) {
            $query->where('asrama_id', $request->asrama_id);
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $santriList = $query->orderBy('nama_santri')->get();
        
        // Data for Filters (for display)
        $kelasList = \App\Models\Kelas::all();
        $asramaList = \App\Models\Asrama::all();

        // Calculate Arrears for All Santri
        $biayaBulanan = 500000;
        $endDate = now();
        $santriWithArrears = [];

        foreach ($santriList as $santri) {
            $startDate = $santri->tanggal_masuk ?? $santri->created_at;
            
            $allMonths = [];
            $current = $startDate->copy()->startOfMonth();
            while ($current <= $endDate) {
                $allMonths[] = $current->month . '-' . $current->year;
                $current->addMonth();
            }

            $paidMonths = Syahriah::where('santri_id', $santri->id)
                ->where('is_lunas', true)
                ->get()
                ->map(fn($item) => $item->bulan . '-' . $item->tahun)
                ->toArray();

            $unpaidCount = 0;
            foreach ($allMonths as $monthKey) {
                if (!in_array($monthKey, $paidMonths)) {
                    $unpaidCount++;
                }
            }

            if ($unpaidCount > 0) {
                $santriWithArrears[] = [
                    'santri' => $santri,
                    'unpaid_months' => $unpaidCount,
                    'total_arrears' => $unpaidCount * $biayaBulanan,
                ];
            }
        }

        $totalSantriMenunggak = count($santriWithArrears);
        $grandTotalBulan = array_sum(array_column($santriWithArrears, 'unpaid_months'));
        $grandTotalRupiah = array_sum(array_column($santriWithArrears, 'total_arrears'));

        return response()->view('bendahara.exports.laporan-tunggakan', compact(
            'santriWithArrears', 'biayaBulanan', 'kelasList', 'asramaList',
            'totalSantriMenunggak', 'grandTotalBulan', 'grandTotalRupiah'
        ))->header('Content-Type', 'text/html');
    }
}
