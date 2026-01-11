<?php

namespace App\Http\Controllers;

use App\Models\Perizinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerizinanController extends Controller
{
    public function index(Request $request)
    {
        $query = Perizinan::with('santri')->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_mulai', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('santri', function($q) use ($search) {
                $q->where('nama_santri', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $perizinan = $query->paginate(10);
        return view('sekretaris.perizinan.index', compact('perizinan'));
    }

    public function approval(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak'
        ]);

        $perizinan = Perizinan::findOrFail($id);
        $perizinan->update([
            'status' => $request->status,
            'approved_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Status perizinan berhasil diperbarui.');
    }

    public function create()
    {
        $santri = \App\Models\Santri::where('is_active', true)->get();
        return view('sekretaris.perizinan.create', compact('santri'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|exists:santri,id',
            'jenis' => 'required|in:Sakit,Izin,Pulang',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',
            'alasan' => 'required|string',
            'bukti_foto' => 'nullable|string' // Assuming this is a URL or handle file upload if needed later
        ]);

        Perizinan::create([
            'santri_id' => $request->santri_id,
            'jenis' => $request->jenis,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'alasan' => $request->alasan,
            'status' => 'Pending', // Default status for manual input
            'bukti_foto' => $request->bukti_foto,
        ]);

        return redirect()->route('sekretaris.perizinan.index')->with('success', 'Data perizinan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $perizinan = Perizinan::findOrFail($id);
        $santri = \App\Models\Santri::where('is_active', true)->get();
        return view('sekretaris.perizinan.edit', compact('perizinan', 'santri'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'santri_id' => 'required|exists:santri,id',
            'jenis' => 'required|in:Sakit,Izin,Pulang',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',
            'alasan' => 'required|string',
            'status' => 'required|in:Pending,Disetujui,Ditolak',
        ]);

        $perizinan = Perizinan::findOrFail($id);
        $perizinan->update([
            'santri_id' => $request->santri_id,
            'jenis' => $request->jenis,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'alasan' => $request->alasan,
            'status' => $request->status,
        ]);

        return redirect()->route('sekretaris.perizinan.index')->with('success', 'Data perizinan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $perizinan = Perizinan::findOrFail($id);
        $perizinan->delete();
        return redirect()->back()->with('success', 'Data perizinan berhasil dihapus.');
    }
}
