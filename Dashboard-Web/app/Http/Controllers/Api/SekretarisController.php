<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Perizinan;

class SekretarisController extends Controller
{
    public function dashboard()
    {
        $totalSantri = Santri::where('is_active', true)->count();
        $putra = Santri::where('is_active', true)->where('gender', 'putra')->count();
        $putri = Santri::where('is_active', true)->where('gender', 'putri')->count();
        $kelas = \App\Models\Kelas::count();
        $asrama = \App\Models\Asrama::count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_santri' => $totalSantri,
                'putra' => $putra,
                'putri' => $putri,
                'total_kelas' => $kelas,
                'total_asrama' => $asrama,
            ]
        ]);
    }

    public function getFilters()
    {
        $kelas = \App\Models\Kelas::select('id', 'nama_kelas')->get();
        $asrama = \App\Models\Asrama::select('id', 'nama_asrama')->get();
        $kobong = \App\Models\Kobong::select('id', 'asrama_id', 'nomor_kobong')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'kelas' => $kelas,
                'asrama' => $asrama,
                'kobong' => $kobong,
                'gender' => [
                    ['id' => 'putra', 'label' => 'Putra'],
                    ['id' => 'putri', 'label' => 'Putri'],
                ]
            ]
        ]);
    }

    public function index(Request $request)

    {
        $query = Santri::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_santri', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($request->has('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->has('asrama_id')) {
            $query->where('asrama_id', $request->asrama_id);
        }

        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }

        $santri = $query->with(['kelas'])->take(50)->get();

        return response()->json([
            'status' => 'success',
            'data' => $santri->map(function($s) {
                return [
                    'id' => $s->id,
                    'nama' => $s->nama_santri,
                    'nis' => $s->nis,
                    'kelas' => $s->kelas->nama_kelas ?? '-',
                    'kamar' => $s->kamar ?? '-',
                ];
            })
        ]);
    }


    public function perizinan()
    {
        $perizinan = Perizinan::with(['santri'])
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $perizinan->map(function($p) {
                return [
                    'id' => $p->id,
                    'nama_santri' => $p->santri->nama_santri ?? 'N/A',
                    'alasan' => $p->alasan,
                    'tgl_pulang' => $p->tgl_pulang,
                    'tgl_kembali' => $p->tgl_kembali,
                    'status' => $p->status,
                ];
            })
        ]);
    }

    public function storePerizinan(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|exists:santri,id',
            'alasan' => 'required|string',
            'tgl_pulang' => 'required|date',
            'tgl_kembali' => 'required|date',
        ]);

        $perizinan = Perizinan::create([
            'santri_id' => $request->santri_id,
            'alasan' => $request->alasan,
            'tgl_pulang' => $request->tgl_pulang,
            'tgl_kembali' => $request->tgl_kembali,
            'status' => 'aktif',
        ]);

        // Create In-App Notification
        $santri = Santri::find($request->santri_id);
        \App\Models\Notification::create([
            'type' => 'perizinan',
            'title' => 'Perizinan Baru',
            'message' => "Izin baru diajukan untuk {$santri->nama_santri}: {$request->alasan}",
            'icon' => 'clock',
            'color' => '#f59e0b',
            'role' => 'sekretaris',
            'data' => [
                'perizinan_id' => $perizinan->id,
                'santri_id' => $santri->id,
            ],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data perizinan berhasil disimpan',
            'data' => $perizinan
        ]);
    }
    public function storeSantri(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|unique:santri,nis',
            'nama_santri' => 'required|string|max:255',
            'negara' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kota_kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'desa_kampung' => 'required|string|max:255',
            'rt_rw' => 'required|string|max:50',
            'nama_ortu_wali' => 'required|string|max:255',
            'no_hp_ortu_wali' => 'required|string|max:20',
            'asrama_id' => 'required|exists:asrama,id',
            'kobong_id' => 'required|exists:kobong,id',
            'kelas_id' => 'required|exists:kelas,id',
            'gender' => 'required|in:putra,putri',
        ]);

        $santri = Santri::create($validated);

        // Create mutasi record
        \App\Models\MutasiSantri::create([
            'santri_id' => $santri->id,
            'jenis_mutasi' => 'masuk',
            'tanggal_mutasi' => now(),
            'keterangan' => 'Pendaftaran via Mobile',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data santri berhasil ditambahkan',
            'data' => $santri
        ]);
    }

    public function updateSantri(Request $request, $id)
    {
        $santri = Santri::findOrFail($id);

        $validated = $request->validate([
            'nis' => 'required|unique:santri,nis,' . $id,
            'nama_santri' => 'required|string|max:255',
            'negara' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kota_kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'desa_kampung' => 'required|string|max:255',
            'rt_rw' => 'required|string|max:50',
            'nama_ortu_wali' => 'required|string|max:255',
            'no_hp_ortu_wali' => 'required|string|max:20',
            'asrama_id' => 'required|exists:asrama,id',
            'kobong_id' => 'required|exists:kobong,id',
            'kelas_id' => 'required|exists:kelas,id',
            'gender' => 'required|in:putra,putri',
        ]);

        $santri->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data santri berhasil diperbarui',
            'data' => $santri
        ]);
    }

    public function deactivateSantri($id)
    {
        $santri = Santri::findOrFail($id);
        $santri->update(['is_active' => false]);

        // Create mutasi record
        \App\Models\MutasiSantri::create([
            'santri_id' => $id,
            'jenis_mutasi' => 'keluar',
            'tanggal_mutasi' => now(),
            'keterangan' => 'Dinonaktifkan via Mobile',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Santri berhasil dinonaktifkan'
        ]);
    }

    public function approvePerizinan(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak'
        ]);

        $perizinan = Perizinan::findOrFail($id);
        $perizinan->update([
            'status' => $request->status,
            'approved_by' => \Illuminate\Support\Facades\Auth::id()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Perizinan berhasil " . strtolower($request->status)
        ]);
    }
}

