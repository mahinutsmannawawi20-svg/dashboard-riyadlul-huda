<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Syahriah;
use App\Models\Santri;
use App\Models\BankAccount;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BendaharaController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();
        
        $totalPemasukan = Pemasukan::sum('jumlah') + Syahriah::where('is_lunas', true)->sum('nominal');
        $totalPengeluaran = Pengeluaran::sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $pemasukanHariIni = Pemasukan::whereDate('tanggal', $today)->sum('jumlah') + 
                            Syahriah::where('is_lunas', true)->whereDate('tanggal_bayar', $today)->sum('nominal');
        
        $pengeluaranHariIni = Pengeluaran::whereDate('tanggal', $today)->sum('jumlah');

        // Split syahriah into manual and gateway
        $syahriahManual = Syahriah::where('is_lunas', true)
            ->where(function($q) {
                $q->whereNull('keterangan')
                  ->orWhere('keterangan', 'not like', '%Midtrans%');
            })->sum('nominal');

        $syahriahGateway = Syahriah::where('is_lunas', true)
            ->where('keterangan', 'like', '%Midtrans%')
            ->sum('nominal');

        return response()->json([
            'status' => 'success',
            'data' => [
                'saldo_total' => $saldo,
                'arus_kas_hari_ini' => [
                    'masuk' => $pemasukanHariIni,
                    'keluar' => $pengeluaranHariIni
                ],
                'syahriah_summary' => [
                    'manual' => $syahriahManual,
                    'gateway' => $syahriahGateway
                ]
            ]
        ]);
    }

    public function kas(Request $request)
    {
        $type = $request->query('type', 'pemasukan'); // pemasukan or pengeluaran
        
        if ($type == 'pemasukan') {
            $data = Pemasukan::orderBy('tanggal', 'desc')->take(50)->get();
        } else {
            $data = Pengeluaran::orderBy('tanggal', 'desc')->take(50)->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $data->map(function($item) {
                return [
                    'id' => $item->id,
                    'keterangan' => $item->keterangan,
                    'jumlah' => $item->jumlah,
                    'tanggal' => $item->tanggal,
                    'kategori' => $item->kategori ?? 'Umum',
                ];
            })
        ]);
    }

    public function cekTunggakan(Request $request)
    {
        $query = Santri::query();
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_santri', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
        }

        $santri = $query->with(['kelas'])->take(20)->get();

        return response()->json([
            'status' => 'success',
            'data' => $santri
        ]);
    }

    public function storeKas(Request $request)
    {
        $request->validate([
            'type' => 'required|in:pemasukan,pengeluaran',
            'jumlah' => 'required|numeric',
            'keterangan' => 'required|string',
            'tanggal' => 'required|date',
            'kategori' => 'nullable|string',
        ]);

        if ($request->type == 'pemasukan') {
            $record = Pemasukan::create([
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan,
                'tanggal' => $request->tanggal,
                'kategori' => $request->kategori ?? 'Umum',
            ]);
        } else {
            $record = Pengeluaran::create([
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan,
                'tanggal' => $request->tanggal,
                'kategori' => $request->kategori ?? 'Umum',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Catatan keuangan berhasil disimpan',
            'data' => $record
        ]);
    }

    // BANK ACCOUNTS
    public function getBankAccounts()
    {
        $accounts = BankAccount::where('is_active', true)->get();
        return response()->json(['status' => 'success', 'data' => $accounts]);
    }

    public function storeBankAccount(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_holder' => 'required|string',
        ]);

        $account = BankAccount::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Rekening berhasil ditambahkan',
            'data' => $account
        ]);
    }

    // WITHDRAWALS
    public function getWithdrawals()
    {
        $withdrawals = Withdrawal::with(['bankAccount'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json(['status' => 'success', 'data' => $withdrawals]);
    }

    public function requestWithdrawal(Request $request)
    {
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);

        // Optional: Check if balance enough (based on saldo currently)
        // But requested as just a "request" for admin to verify

        $withdrawal = Withdrawal::create([
            'user_id' => Auth::id(),
            'bank_account_id' => $request->bank_account_id,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan penarikan berhasil dikirim',
            'data' => $withdrawal
        ]);
    }
}


