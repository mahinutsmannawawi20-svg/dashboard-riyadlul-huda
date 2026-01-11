<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\NilaiSantri;
use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;

class PendidikanController extends Controller
{
    // Dashboard
    public function dashboard(Request $request)
    {
        $tahunAjaran = $request->get('tahun_ajaran', '2024/2025');
        $semester = $request->get('semester', '1');
        $kelasId = $request->get('kelas_id');
        $mapelId = $request->get('mapel_id');
        $gender = $request->get('gender');
        
        // Create unique cache key based on filters
        $cacheKey = "pendidikan_dashboard_{$tahunAjaran}_{$semester}_{$kelasId}_{$mapelId}_{$gender}";
        
        // KPI Cards - Basic Counts (cached for 5 minutes)
        $totalSantri = Cache::remember("{$cacheKey}_total_santri", 300, function() {
            return Santri::where('is_active', true)->count();
        });
        
        $totalKelas = Cache::remember("{$cacheKey}_total_kelas", 300, function() {
            return Kelas::count();
        });
        
        $totalMapel = Cache::remember("{$cacheKey}_total_mapel", 300, function() {
            return MataPelajaran::where('is_active', true)->count();
        });
        
        // Average grade (cached)
        $rataRataNilai = Cache::remember("{$cacheKey}_rata_nilai", 300, function() use ($tahunAjaran, $semester) {
            return NilaiSantri::where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->avg('nilai_akhir') ?? 0;
        });
        
        // Top 10 students (cached)
        $santriBerprestasi = Cache::remember("{$cacheKey}_top_students", 300, function() use ($tahunAjaran, $semester) {
            return NilaiSantri::with('santri')
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->select('santri_id', DB::raw('AVG(nilai_akhir) as rata_rata'))
                ->groupBy('santri_id')
                ->orderByDesc('rata_rata')
                ->limit(10)
                ->get();
        });
        
        // Bottom 10 students (cached)
        $santriPerluBimbingan = Cache::remember("{$cacheKey}_bottom_students", 300, function() use ($tahunAjaran, $semester) {
            return NilaiSantri::with('santri')
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->select('santri_id', DB::raw('AVG(nilai_akhir) as rata_rata'))
                ->groupBy('santri_id')
                ->orderBy('rata_rata')
                ->limit(10)
                ->get();
        });
        
        // Attendance rate (cached)
        $tingkatKehadiran = Cache::remember("{$cacheKey}_attendance", 300, function() {
            $currentYear = now()->year;
            $totalAbsensiRecords = \App\Models\AbsensiSantri::where('tahun', $currentYear)->count();
            
            if ($totalAbsensiRecords > 0) {
                $avgAlfa = \App\Models\AbsensiSantri::where('tahun', $currentYear)
                    ->select(DB::raw('AVG(alfa_sorogan + alfa_menghafal_malam + alfa_menghafal_subuh + alfa_tahajud) as avg_alfa'))
                    ->value('avg_alfa') ?? 0;
                
                return max(0, (1 - ($avgAlfa / 14)) * 100);
            }
            return 0;
        });
        
        // Total teachers (cached)
        $totalGuru = Cache::remember("{$cacheKey}_total_guru", 300, function() {
            return MataPelajaran::whereNotNull('guru_pengampu')
                ->where('guru_pengampu', '!=', '')
                ->distinct('guru_pengampu')
                ->count('guru_pengampu');
        });
        
        // Chart data: Average grade per class (cached)
        $chartKelasData = Cache::remember("{$cacheKey}_chart_kelas", 300, function() use ($tahunAjaran, $semester) {
            $nilaiPerKelas = NilaiSantri::with('santri.kelas')
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->get()
                ->groupBy('santri.kelas.id')
                ->map(function($items) {
                    return [
                        'nama_kelas' => $items->first()->santri->kelas->nama_kelas ?? 'Unknown',
                        'rata_rata' => round($items->avg('nilai_akhir'), 2)
                    ];
                })
                ->sortBy('nama_kelas')
                ->values();
            
            return [
                'labels' => $nilaiPerKelas->pluck('nama_kelas')->toArray(),
                'data' => $nilaiPerKelas->pluck('rata_rata')->toArray()
            ];
        });
        
        $chartKelasLabels = $chartKelasData['labels'];
        $chartKelasData = $chartKelasData['data'];
        
        // Chart data: Grade distribution (cached)
        $gradeData = Cache::remember("{$cacheKey}_grade_dist", 300, function() use ($tahunAjaran, $semester) {
            $gradeDistribution = NilaiSantri::where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->select('grade', DB::raw('COUNT(*) as count'))
                ->groupBy('grade')
                ->orderByRaw("CASE grade 
                    WHEN 'A' THEN 1 
                    WHEN 'B' THEN 2 
                    WHEN 'C' THEN 3 
                    WHEN 'D' THEN 4 
                    WHEN 'E' THEN 5 
                    ELSE 6 END")
                ->get();
            
            return [
                'distribution' => $gradeDistribution,
                'labels' => $gradeDistribution->pluck('grade')->toArray(),
                'data' => $gradeDistribution->pluck('count')->toArray()
            ];
        });
        
        $gradeDistribution = $gradeData['distribution'];
        $chartGradeLabels = $gradeData['labels'];
        $chartGradeData = $gradeData['data'];
        
        // Additional data (cached)
        $totalJadwal = Cache::remember("{$cacheKey}_total_jadwal", 300, function() use ($tahunAjaran, $semester) {
            return JadwalPelajaran::where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->count();
        });
        
        $totalNilaiRecords = Cache::remember("{$cacheKey}_total_nilai", 300, function() use ($tahunAjaran, $semester) {
            return NilaiSantri::where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->count();
        });
        
        $totalAbsensiThisSemester = Cache::remember("{$cacheKey}_total_absensi", 300, function() {
            return \App\Models\AbsensiSantri::where('tahun', now()->year)->count();
        });
        
        // Recent activities (cached for 1 minute for fresher data)
        $recentNilai = Cache::remember("{$cacheKey}_recent_nilai", 60, function() use ($tahunAjaran, $semester) {
            return NilaiSantri::with(['santri', 'mataPelajaran'])
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->latest()
                ->limit(5)
                ->get();
        });
        
        $recentAbsensi = Cache::remember("{$cacheKey}_recent_absensi", 60, function() {
            return \App\Models\AbsensiSantri::with(['santri', 'kelas'])
                ->latest()
                ->limit(5)
                ->get();
        });
        
        // Filter Helper Lists (cached for 10 minutes)
        $kelasList = Cache::remember('kelas_list', 600, function() {
            return Kelas::orderBy('level')->orderBy('nama_kelas')->get();
        });
        
        $mapelList = Cache::remember('mapel_list', 600, function() {
            return MataPelajaran::orderBy('nama_mapel')->get();
        });
        
        $tahunAjaranList = Cache::remember('tahun_ajaran_list', 600, function() {
            return TahunAjaran::orderBy('nama', 'desc')->pluck('nama');
        });
        
        return view('pendidikan.dashboard.index', compact(
            'totalSantri', 'totalKelas', 'totalMapel', 'tingkatKehadiran', 'totalGuru',
            'rataRataNilai', 'santriBerprestasi', 'santriPerluBimbingan',
            'chartKelasLabels', 'chartKelasData', 'gradeDistribution',
            'chartGradeLabels', 'chartGradeData',
            'totalJadwal', 'totalNilaiRecords', 'totalAbsensiThisSemester',
            'recentNilai', 'recentAbsensi',
            'kelasList', 'mapelList', 'tahunAjaranList',
            'tahunAjaran', 'semester', 'kelasId', 'mapelId', 'gender'
        ));
    }
    
    // Nilai Santri - Index
    public function nilai(Request $request)
    {
        $query = NilaiSantri::with(['santri.kelas', 'mataPelajaran']);
        
        if ($request->filled('kelas_id')) {
            $query->whereHas('santri', function($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }
        
        if ($request->filled('jenis_kelamin')) {
            $query->whereHas('santri', function($q) use ($request) {
                $q->where('jenis_kelamin', $request->jenis_kelamin);
            });
        }
        
        if ($request->filled('mata_pelajaran_id')) {
            $query->where('mapel_id', $request->mata_pelajaran_id);
        }
        
        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }
        
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }
        
        $nilai = $query->latest()->paginate(20);
        $santriList = Santri::with('kelas')->get();
        $kelasList = Kelas::all();
        // Filter Mapel based on selected Class
        if ($request->filled('kelas_id')) {
            $mapelList = MataPelajaran::whereDoesntHave('kelas')
                ->orWhereHas('kelas', function($q) use ($request) {
                    $q->where('kelas.id', $request->kelas_id);
                })
                ->orderBy('nama_mapel')
                ->get();
        } else {
            $mapelList = MataPelajaran::orderBy('nama_mapel')->get();
        }
        $tahunAjaran = date('Y') . '/' . (date('Y') + 1);
        $tahunAjaranList = TahunAjaran::orderBy('nama', 'desc')->pluck('nama');
        
        // Fetch Ujian Mingguan Data for Smart Scoring (Real-time merge on frontend)
    $weeklyExamData = [];
    if($request->filled('kelas_id') && $request->filled('tahun_ajaran') && $request->filled('semester')) {
        $weeklyExams = \App\Models\UjianMingguan::where('tahun_ajaran', $request->tahun_ajaran)
            ->where('semester', $request->semester)
            ->whereHas('santri', function($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            })
            ->get();
            
        foreach($weeklyExams as $exam) {
            $weeklyExamData[$exam->santri_id][$exam->mapel_id] = [
                'score' => $exam->nilai_hasil_mingguan,
                'status' => $exam->status
            ];
        }
    }

    return view('pendidikan.nilai.index', compact('nilai', 'santriList', 'kelasList', 'mapelList', 'tahunAjaran', 'tahunAjaranList', 'weeklyExamData'));
    }
    
    // Nilai Santri - Store
    public function storeNilai(Request $request)
    {
        $validated = $request->validate([
            'santri_id' => 'required|exists:santri,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'semester' => 'required|in:1,2',
            'tahun_ajaran' => 'required|string',
            'nilai_uts' => 'nullable|numeric|min:0|max:100',
            'nilai_uas' => 'nullable|numeric|min:0|max:100',
            'nilai_tugas' => 'nullable|numeric|min:0|max:100',
            'nilai_praktik' => 'nullable|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);
        
        $nilai = NilaiSantri::create($validated);
        $nilai->calculateNilaiAkhir();
        $nilai->save();
        
        return redirect()->route('pendidikan.nilai')
            ->with('success', 'Data nilai berhasil ditambahkan');
    }
    
    // Nilai Santri - Bulk Store (Horizontal Format) with Merge Logic
    public function storeNilaiBulk(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|in:1,2',
            'santri' => 'required|array',
            'santri.*.id' => 'required|exists:santri,id',
            'santri.*.mapel' => 'required|array', // REQUIRED: All semester exams must be filled
            'santri.*.mapel.*' => 'required|numeric|min:0|max:100', // Each score is required
        ]);
        
        DB::beginTransaction();
        try {
            foreach ($validated['santri'] as $santriData) {
                $santriId = $santriData['id'];
                
                foreach ($santriData['mapel'] as $mapelId => $nilaiSemester) {
                    // Get subject info
                    $mapel = MataPelajaran::find($mapelId);
                    $hasWeeklyExam = $mapel->has_weekly_exam ?? false;
                    
                    // Initialize variables
                    $nilaiAsli = null;
                    $sourceType = 'semester';
                    $metadata = [
                        'semester_score' => $nilaiSemester,
                        'has_weekly_exam' => $hasWeeklyExam,
                    ];
                    
                    if (!$hasWeeklyExam) {
                        // NON-WEEKLY SUBJECT: Use semester score directly
                        $nilaiAsli = $nilaiSemester;
                        $sourceType = 'semester';
                        $metadata['reason'] = 'Subject does not have weekly exam';
                        
                    } else {
                        // WEEKLY SUBJECT: Check weekly exam status and compare
                        $ujianMingguan = \App\Models\UjianMingguan::where([
                            'santri_id' => $santriId,
                            'mapel_id' => $mapelId,
                            'tahun_ajaran' => $validated['tahun_ajaran'],
                            'semester' => $validated['semester'],
                        ])->first();
                        
                        if (!$ujianMingguan || $ujianMingguan->status !== 'SAH') {
                            // Weekly exam TIDAK SAH or doesn't exist: Use semester
                            $nilaiAsli = $nilaiSemester;
                            $sourceType = 'semester';
                            $metadata['weekly_status'] = $ujianMingguan ? $ujianMingguan->status : 'NOT_FOUND';
                            $metadata['reason'] = 'Weekly exam not valid (TIDAK SAH or not found)';
                            
                        } else {
                            // Weekly exam SAH: Compare and pick BEST
                            $nilaiMingguan = $ujianMingguan->nilai_hasil_mingguan;
                            $metadata['weekly_score'] = $nilaiMingguan;
                            $metadata['weekly_status'] = 'SAH';
                            $metadata['weekly_attendance'] = $ujianMingguan->jumlah_keikutsertaan;
                            
                            if ($nilaiMingguan >= $nilaiSemester) {
                                // Weekly score is better or equal
                                $nilaiAsli = $nilaiMingguan;
                                $sourceType = 'merged_weekly_better';
                                $metadata['reason'] = "Weekly score ({$nilaiMingguan}) >= Semester score ({$nilaiSemester})";
                            } else {
                                // Semester score is better
                                $nilaiAsli = $nilaiSemester;
                                $sourceType = 'merged_semester_better';
                                $metadata['reason'] = "Semester score ({$nilaiSemester}) > Weekly score ({$nilaiMingguan})";
                            }
                        }
                    }
                    
                    // Calculate nilai_rapor (minimum 5 rule for report cards)
                    $nilaiRapor = $nilaiAsli < 5 ? 5 : $nilaiAsli;
                    
                    // Save to database
                    $nilai = NilaiSantri::updateOrCreate(
                        [
                            'santri_id' => $santriId,
                            'mapel_id' => $mapelId,
                            'tahun_ajaran' => $validated['tahun_ajaran'],
                            'semester' => $validated['semester'],
                        ],
                        [
                            'kelas_id' => $validated['kelas_id'],
                            'nilai_ujian_semester' => $nilaiSemester,
                            'nilai_asli' => $nilaiAsli,
                            'nilai_rapor' => $nilaiRapor,
                            'nilai_akhir' => $nilaiAsli, // For compatibility with existing code
                            'source_type' => $sourceType,
                            'source_metadata' => $metadata,
                            'updated_at' => now(),
                        ]
                    );
                }
            }
            
            // APPLY GRADE COMPENSATION TO nilai_asli (NOT nilai_rapor)
            // This ensures fair ranking by redistributing points from higher grades
            foreach ($validated['santri'] as $santriData) {
                $this->applyGradeCompensation(
                    $santriData['id'],
                    $validated['tahun_ajaran'],
                    $validated['semester']
                );
            }
            
            DB::commit();
            return redirect()->route('pendidikan.nilai', $request->only(['kelas_id', 'tahun_ajaran', 'semester']))
                ->with('success', 'Nilai ujian semester berhasil disimpan dan digabungkan dengan ujian mingguan');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    
    /**
     * Apply Grade Compensation Algorithm
     * Ensures no grade is below minimum threshold (5) by redistributing points from higher grades
     * 
     * @param int $santriId
     * @param string $tahunAjaran
     * @param string $semester
     * @return void
     */
    private function applyGradeCompensation($santriId, $tahunAjaran, $semester)
    {
        $minThreshold = 5; // Minimum allowed grade
        $donorThreshold = 7; // Only grades >= 7 can donate
        
        // Get all grades for this student
        $grades = NilaiSantri::where('santri_id', $santriId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->get();
        
        if ($grades->isEmpty()) {
            return;
        }
        
        // Separate grades into recipients (< 5) and donors (>= 7)
        // IMPORTANT: Use nilai_asli for ranking, NOT nilai_akhir
        $recipients = $grades->filter(fn($g) => ($g->nilai_asli ?? $g->nilai_akhir) < $minThreshold);
        $donors = $grades->filter(fn($g) => ($g->nilai_asli ?? $g->nilai_akhir) >= $donorThreshold);
        
        if ($recipients->isEmpty() || $donors->isEmpty()) {
            // No compensation needed or not possible
            return;
        }
        
        // Calculate total compensation needed
        $totalNeeded = $recipients->sum(fn($g) => $minThreshold - ($g->nilai_asli ?? $g->nilai_akhir));
        
        // Calculate total available from donors (keep donors at minimum 6)
        $totalAvailable = $donors->sum(fn($g) => max(0, ($g->nilai_asli ?? $g->nilai_akhir) - 6));
        
        if ($totalAvailable < $totalNeeded) {
            // Not enough points to compensate fully, distribute what's available
            $compensationRatio = $totalAvailable / $totalNeeded;
        } else {
            $compensationRatio = 1.0;
        }
        
        // Apply compensation to recipients
        foreach ($recipients as $recipient) {
            $currentNilai = $recipient->nilai_asli ?? $recipient->nilai_akhir;
            $needed = $minThreshold - $currentNilai;
            $compensation = $needed * $compensationRatio;
            
            $recipient->nilai_original = $currentNilai;
            $recipient->nilai_kompensasi = $compensation;
            $recipient->nilai_asli = $currentNilai + $compensation;
            $recipient->nilai_akhir = $recipient->nilai_asli; // Keep in sync
            $recipient->is_compensated = true;
            
            // Recalculate nilai_rapor (minimum 5 rule)
            $recipient->calculateNilaiRapor();
            $recipient->save();
        }
        
        // Deduct from donors proportionally
        $donorTotal = $donors->sum(fn($g) => $g->nilai_asli ?? $g->nilai_akhir);
        $totalCompensationGiven = $recipients->sum('nilai_kompensasi');
        
        foreach ($donors as $donor) {
            $currentNilai = $donor->nilai_asli ?? $donor->nilai_akhir;
            // Proportional deduction based on donor's grade
            $donorProportion = $currentNilai / $donorTotal;
            $deduction = $totalCompensationGiven * $donorProportion;
            
            $donor->nilai_original = $currentNilai;
            $donor->nilai_kompensasi = -$deduction; // Negative = donated
            $donor->nilai_asli = $currentNilai - $deduction;
            $donor->nilai_akhir = $donor->nilai_asli; // Keep in sync
            $donor->is_compensated = true;
            
            // Store metadata about compensation
            $donor->compensation_metadata = json_encode([
                'type' => 'donor',
                'amount_donated' => round($deduction, 2),
                'recipients_count' => $recipients->count()
            ]);
            
            // Recalculate nilai_rapor (minimum 5 rule)
            $donor->calculateNilaiRapor();
            $donor->save();
        }
        
        // Update recipient metadata
        foreach ($recipients as $recipient) {
            $recipient->compensation_metadata = json_encode([
                'type' => 'recipient',
                'amount_received' => round((float)($recipient->nilai_kompensasi ?? 0), 2),
                'donors_count' => $donors->count(),
                'original_grade' => round((float)($recipient->nilai_original ?? 0), 2)
            ]);
            $recipient->save();
        }
    }
    
    // Nilai Santri - Update
    public function updateNilai(Request $request, $id)
    {
        $nilai = NilaiSantri::findOrFail($id);
        
        $validated = $request->validate([
            'nilai_uts' => 'nullable|numeric|min:0|max:100',
            'nilai_uas' => 'nullable|numeric|min:0|max:100',
            'nilai_tugas' => 'nullable|numeric|min:0|max:100',
            'nilai_praktik' => 'nullable|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);
        
        $nilai->update($validated);
        $nilai->calculateNilaiAkhir();
        $nilai->save();
        
        return redirect()->route('pendidikan.nilai')
            ->with('success', 'Data nilai berhasil diperbarui');
    }
    
    // Nilai Santri - Delete
    public function destroyNilai($id)
    {
        $nilai = NilaiSantri::findOrFail($id);
        $nilai->delete();
        
        return redirect()->route('pendidikan.nilai')
            ->with('success', 'Data nilai berhasil dihapus');
    }
    
    // Nilai Santri - Cetak PDF
    public function cetakNilai(Request $request)
    {
        $kelasId = $request->kelas_id;
        $tahunAjaran = $request->tahun_ajaran;
        $semester = $request->semester;
        $gender = $request->gender;
        
        if (!$kelasId || !$tahunAjaran || !$semester) {
            return redirect()->route('pendidikan.nilai')
                ->with('error', 'Silakan pilih kelas, tahun ajaran, dan semester terlebih dahulu');
        }
        
        $kelas = Kelas::findOrFail($kelasId);
        
        // Get all santri in this class
        $santriQuery = Santri::where('kelas_id', $kelasId)
            ->where('is_active', true);
        
        // Filter by gender if specified
        if ($gender && $gender !== 'all') {
            $santriQuery->where('gender', $gender);
        }
        
        $santriList = $santriQuery->orderBy('nama_santri')->get();
        
        // Get all unique subjects for this class and semester
        $mapelList = MataPelajaran::whereHas('nilaiSantri', function($q) use ($kelasId, $tahunAjaran, $semester) {
            $q->whereHas('santri', function($sq) use ($kelasId) {
                $sq->where('kelas_id', $kelasId);
            })
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester);
        })->orderBy('nama_mapel')->get();
        
        // If no subjects found, use default list
        if ($mapelList->isEmpty()) {
            $defaultMapel = [
                'Akidah', 'Akhlak', 'Fiqih', 'SKI', 'Qur\'an', 'Hadits', 
                'Bahasa Arab', 'Nahwu', 'Shorof', 'Tajwid', 'Tafsir', 'Ushul Fiqh'
            ];
            $mapelList = collect($defaultMapel)->map(function($name, $index) {
                return (object)['id' => $index, 'nama_mapel' => $name];
            });
        }
        
        // Calculate statistics using helper method
        $stats = $this->getNilaiStatistics($kelasId, $tahunAjaran, $semester, $santriList, $mapelList);
        extract($stats);
        
        return view('pendidikan.nilai.cetak-nilai', compact(
            'kelas',
            'tahunAjaran',
            'semester',
            'gender',
            'santriList',
            'mapelList',
            'nilaiData',
            'studentAverages',
            'studentRankings',
            'columnStats'
        ));
    }
    
    
    // Mata Pelajaran - Index
    public function mapel(Request $request)
    {
        $query = MataPelajaran::with('kelas'); // Eager load kelas relationship
        
        // Filter by kelas if provided
        if ($request->filled('kelas_id')) {
            $query->whereHas('kelas', function($q) use ($request) {
                $q->where('kelas.id', $request->kelas_id);
            });
        }
        
        // Filter by waktu
        if ($request->filled('waktu')) {
            $query->where('waktu_pelajaran', $request->waktu);
        }
        
        $mapel = $query->latest()->paginate(15);
        $kelasList = Kelas::all();
        $tahunAjaranList = TahunAjaran::orderBy('nama', 'desc')->pluck('nama');
        
        return view('pendidikan.mapel.index', compact('mapel', 'kelasList', 'tahunAjaranList'));
    }
    
    // Mata Pelajaran - Store
    public function storeMapel(Request $request)
    {
        $validated = $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:50|unique:mata_pelajaran,kode_mapel',
            'kategori' => 'required|in:Agama,Umum,Ekstrakurikuler',
            'guru_pengampu' => 'nullable|string|max:255',
            'waktu_pelajaran' => 'required|in:Subuh,Pagi,Siang,Sore,Malam',
            'deskripsi' => 'nullable|string',
            'kelas_umum' => 'nullable|boolean',
            'kelas_ids' => 'nullable|array',
            'kelas_ids.*' => 'exists:kelas,id',
        ]);
        
        // Remove kelas fields from validated data (not in mata_pelajaran table)
        $kelasUmum = $request->has('kelas_umum');
        $kelasIds = $request->input('kelas_ids', []);
        unset($validated['kelas_umum'], $validated['kelas_ids']);
        
        $mapel = MataPelajaran::create($validated);
        
        // Handle kelas relationship
        if ($kelasUmum || !empty($kelasIds)) {
            $syncData = [];
            
            // If kelas umum is checked, attach all kelas
            if ($kelasUmum) {
                $allKelasIds = Kelas::pluck('id')->toArray();
                foreach ($allKelasIds as $kelasId) {
                    $syncData[$kelasId] = ['is_kelas_umum' => true];
                }
            } else {
                // Only attach selected kelas
                foreach ($kelasIds as $kelasId) {
                    $syncData[$kelasId] = ['is_kelas_umum' => false];
                }
            }
            
            $mapel->kelas()->sync($syncData);
        }
        
        return redirect()->route('pendidikan.mapel')
            ->with('success', 'Mata pelajaran berhasil ditambahkan');
    }
    
    // Mata Pelajaran - Update
    public function updateMapel(Request $request, $id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        
        $validated = $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:50|unique:mata_pelajaran,kode_mapel,' . $id,
            'kategori' => 'required|in:Agama,Umum,Ekstrakurikuler',
            'guru_pengampu' => 'nullable|string|max:255',
            'guru_badal' => 'nullable|string|max:255',
            'waktu_pelajaran' => 'required|in:Subuh,Pagi,Siang,Sore,Malam',
            'deskripsi' => 'nullable|string',
            'is_active' => 'required|in:0,1',
            'kelas_umum' => 'nullable|boolean',
            'kelas_ids' => 'nullable|array',
            'kelas_ids.*' => 'exists:kelas,id',
        ]);
        
        // Convert is_active to boolean
        $validated['is_active'] = (bool) $validated['is_active'];
        
        // Remove kelas fields from validated data (not in mata_pelajaran table)
        $kelasUmum = $request->has('kelas_umum');
        $kelasIds = $request->input('kelas_ids', []);
        unset($validated['kelas_umum'], $validated['kelas_ids']);
        
        $mapel->update($validated);
        
        // Handle kelas relationship
        if ($kelasUmum || !empty($kelasIds)) {
            $syncData = [];
            
            // If kelas umum is checked, sync all kelas
            if ($kelasUmum) {
                $allKelasIds = Kelas::pluck('id')->toArray();
                foreach ($allKelasIds as $kelasId) {
                    $syncData[$kelasId] = ['is_kelas_umum' => true];
                }
            } else {
                // Only sync selected kelas
                foreach ($kelasIds as $kelasId) {
                    $syncData[$kelasId] = ['is_kelas_umum' => false];
                }
            }
            
            $mapel->kelas()->sync($syncData);
        } else {
            // If no kelas selected, detach all
            $mapel->kelas()->detach();
        }
        
        return redirect()->route('pendidikan.mapel')
            ->with('success', 'Mata pelajaran berhasil diperbarui');
    }
    
    // Mata Pelajaran - Delete
    public function destroyMapel($id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        $mapel->delete();
        
        return redirect()->route('pendidikan.mapel')
            ->with('success', 'Mata pelajaran berhasil dihapus');
    }

    // Mata Pelajaran - Update Guru Badal
    public function updateGuruBadal(Request $request, $id)
    {
        try {
            $mapel = MataPelajaran::findOrFail($id);
            $mapel->guru_badal = $request->guru_badal;
            $mapel->save();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Mata Pelajaran - Update Guru Pengampu
    public function updateGuruPengampu(Request $request, $id)
    {
        try {
            $mapel = MataPelajaran::findOrFail($id);
            $mapel->guru_pengampu = $request->guru_pengampu;
            $mapel->save();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    
    // Jadwal - Index
    public function jadwal(Request $request)
    {
        $query = JadwalPelajaran::with(['kelas', 'mapel']);
        
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }
        
        // Add Default Year Filter if not present to avoid clutter, or just filter if requested?
        // Usually Schedule is for a specific year.
        // Let's filter by requested year, or default to latest if not specified? 
        // User said "all use filter exactly like Laporan".
        // In Laporan, we usually filter.
        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }
        
        $jadwal = $query->orderBy('kelas_id')
                        ->orderByRaw("CASE 
                            WHEN hari = 'Senin' THEN 1 
                            WHEN hari = 'Selasa' THEN 2 
                            WHEN hari = 'Rabu' THEN 3 
                            WHEN hari = 'Kamis' THEN 4 
                            WHEN hari = 'Jumat' THEN 5 
                            WHEN hari = 'Sabtu' THEN 6 
                            WHEN hari = 'Minggu' THEN 7 
                            ELSE 8 END")
                        ->orderBy('jam_mulai')
                        ->get(); // Get all data for proper grouping (client-side or server-side grouping can be done, but get() is safer for grouping views)
        $kelasList = Kelas::all();
        $mapelList = MataPelajaran::where('is_active', true)->get();
        $tahunAjaranList = TahunAjaran::orderBy('nama', 'desc')->pluck('nama');
        
        return view('pendidikan.jadwal.index', compact('jadwal', 'kelasList', 'mapelList', 'tahunAjaranList'));
    }
    
    // Jadwal - Store
    public function storeJadwal(Request $request)
    {
        // Validasi format jam bisa H:i atau H:i:s, atau format jam santai seperti 7:00
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'jam_selesai' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'ruangan' => 'nullable|string|max:100',
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|in:1,2',
        ]);
        
        // Normalisasi jam
        $validated['jam_mulai'] = date('H:i', strtotime($validated['jam_mulai']));
        $validated['jam_selesai'] = date('H:i', strtotime($validated['jam_selesai']));
        
        JadwalPelajaran::create($validated);
        
        return redirect()->route('pendidikan.jadwal')
            ->with('success', 'Jadwal berhasil ditambahkan');
    }
    
    // Jadwal - Update
    public function updateJadwal(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        
        // Validasi format jam bisa H:i atau H:i:s, atau format jam santai seperti 7:00
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'jam_selesai' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'ruangan' => 'nullable|string|max:100',
            'guru_badal' => 'nullable|string|max:255',
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|in:1,2',
        ]);
        
        // Normalisasi jam (tambah leading zero jika 7:00 -> 07:00)
        // Dan pastikan format time H:i untuk database (atau H:i:s)
        $validated['jam_mulai'] = date('H:i', strtotime($validated['jam_mulai']));
        $validated['jam_selesai'] = date('H:i', strtotime($validated['jam_selesai']));

        $jadwal->update($validated);
        
        return redirect()->route('pendidikan.jadwal')
            ->with('success', 'Jadwal berhasil diperbarui');
    }
    
    // Jadwal - Delete
    public function destroyJadwal($id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        $jadwal->delete();
        
        return redirect()->route('pendidikan.jadwal')
            ->with('success', 'Jadwal berhasil dihapus');
    }
    
    // Jadwal Pelajaran - Update Kitab Talaran
    public function updateKitabTalaran(Request $request)
    {
        try {
            $validated = $request->validate([
                'kelas_id' => 'required|integer',
                'semester' => 'required|in:1,2',
                'nama_kitab' => 'required|string|max:255',
            ]);
            
            \App\Models\KitabTalaran::updateOrCreate(
                [
                    'kelas_id' => $validated['kelas_id'],
                    'semester' => $validated['semester'],
                ],
                [
                    'nama_kitab' => $validated['nama_kitab'],
                ]
            );
            
            // For AJAX requests, return JSON
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson() || $request->header('Content-Type') === 'application/json') {
                return response()->json(['success' => true]);
            }
            
            return redirect()->route('pendidikan.jadwal')
                ->with('success', 'Kitab talaran berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson() || $request->header('Content-Type') === 'application/json') {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    // Update Kitab Talaran - GLOBAL per Semester (berlaku untuk semua kelas)
    public function updateKitabTalaranGlobal(Request $request, $semester)
    {
        $validated = $request->validate([
            'nama_kitab' => 'required|string|max:255',
        ]);
        
        // Create or update global entry (kelas_id = null)
        \App\Models\KitabTalaran::updateOrCreate(
            [
                'kelas_id' => null,
                'semester' => $semester,
            ],
            [
                'nama_kitab' => $validated['nama_kitab'],
            ]
        );
        
        return redirect()->route('pendidikan.jadwal')
            ->with('success', 'Kitab talaran semester ' . $semester . ' berhasil diperbarui untuk semua kelas');
    }

    // Delete Kitab Talaran by Kelas (both semesters)
    public function deleteKitabByKelas($kelasId)
    {
        try {
            \App\Models\KitabTalaran::where('kelas_id', $kelasId)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    // Jadwal Pelajaran - Update Guru Badal
    public function updateGuruBadalJadwal(Request $request, $id)
    {
        try {
            $jadwal = JadwalPelajaran::findOrFail($id);
            $jadwal->guru_badal = $request->guru_badal;
            $jadwal->save();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Kelas - Update Wali Kelas (Single)
    public function updateWaliKelas(Request $request, $id)
    {
        try {
            $kelas = Kelas::findOrFail($id);
            $kelas->wali_kelas = $request->wali_kelas;
            $kelas->save();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Kelas - Update Wali Kelas DUAL (Putra & Putri)
    public function updateWaliKelasDual(Request $request, $id)
    {
        try {
            $kelas = Kelas::findOrFail($id);
            $kelas->wali_kelas_putra = $request->wali_kelas_putra;
            $kelas->wali_kelas_putri = $request->wali_kelas_putri;
            $kelas->tipe_wali_kelas = 'dual'; // Ensure it's set to dual
            $kelas->save();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    
    // Jadwal Pelajaran - Destroy
    // Laporan
    public function laporan()
    {
        $santriList = Santri::where('is_active', true)->orderBy('nama_santri')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        
        // Ambil daftar tahun ajaran dari Database
        $tahunAjaranObjects = TahunAjaran::orderBy('nama', 'desc')->get();
        $tahunAjaranList = $tahunAjaranObjects->pluck('nama');

        // Prepare data for JS Filter
        $santriDataForFilter = $santriList->map(function($s) {
            return [
                'value' => $s->id,
                'text' => $s->nis . ' - ' . $s->nama_santri,
                'kelas' => $s->kelas_id,
                'gender' => $s->gender
            ];
        });

        return view('pendidikan.laporan.index', compact('santriList', 'kelasList', 'tahunAjaranList', 'tahunAjaranObjects', 'santriDataForFilter'));
    }
    
    // Helper to Get Rapor Data (Single or Bulk)
    private function getRaporData($santriId, $tahunAjaran, $semester)
    {
        $santri = Santri::with(['kelas', 'kobong'])->findOrFail($santriId);
        
        // 1. Fetch Grades
        $grades = NilaiSantri::where('santri_id', $santriId)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->get()
            ->keyBy('mapel_id'); 

        // 2. Calculate Ranking
        $classmates = Santri::where('kelas_id', $santri->kelas_id)->where('is_active', true)->pluck('id');
        $studentAverages = NilaiSantri::whereIn('santri_id', $classmates)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->select('santri_id', DB::raw('SUM(nilai_akhir) as total_score')) 
            ->groupBy('santri_id')
            ->orderByDesc('total_score')
            ->get();
        
        $ranking = $studentAverages->search(function($item) use ($santriId) {
            return $item->santri_id == $santriId;
        });
        $ranking = ($ranking !== false) ? $ranking + 1 : '-';
        $totalSiswa = $classmates->count();

        // 3. Attendance
        $absensi = \App\Models\AbsensiSantri::where('santri_id', $santriId)
                    ->get();

        // Calculate scaled alfa: 25 points = 1 day
        $totalAlfaPoints = $absensi->sum('alfa_sorogan') + $absensi->sum('alfa_menghafal_malam');
        $alfaDays = floor($totalAlfaPoints / 25);
                    
        $kehadiran = [
            'sakit' => 0, 
            'izin' => 0,
            'alfa' => $alfaDays
        ];
        
        // 4. E-Rapor Number
        $kodeTahun = str_replace('/', '', $tahunAjaran);
        $noRapor = "ER-{$kodeTahun}-{$semester}-{$santri->nis}-" . strtoupper(substr(md5($santri->id . $tahunAjaran . $semester), 0, 4));

        return [
            'santri' => $santri,
            'grades' => $grades,
            'kehadiran' => $kehadiran,
            'ranking' => $ranking,
            'totalSiswa' => $totalSiswa,
            'noRapor' => $noRapor,
            'catatan_wali_kelas' => null
        ];
    }

    // Export Rapor
    public function exportRapor(Request $request)
    {
        $santriId = $request->santri_id;
        $tahunAjaran = $request->tahun_ajaran;
        $semester = $request->semester;
        
        // Get Student first to know their class
        $santri = Santri::findOrFail($santriId);
        $kelasId = $santri->kelas_id;

        $settings = \App\Models\ReportSettings::firstOrCreate([], [
            'nama_yayasan' => 'YAYASAN PONDOK PESANTREN',
            'nama_pondok' => 'RIYADLUL HUDA',
            'kota_terbit' => 'Tasikmalaya',
        ]);
        
        // Fetch Mapel specific to student's class
        $allMapel = MataPelajaran::where('is_active', true)
            ->where(function($query) use ($kelasId) {
                $query->whereDoesntHave('kelas')
                      ->orWhereHas('kelas', function($q) use ($kelasId) {
                          $q->where('kelas.id', $kelasId);
                      });
            })
            ->get();

        $categoryOrder = ['Umum', 'Wajib', 'Agama', 'Diniyah', 'Mulok', 'Ekstrakurikuler'];
        $allMapel = $allMapel->sortBy(function($mapel) use ($categoryOrder) {
            $order = array_search($mapel->kategori, $categoryOrder);
            return $order === false ? 99 : $order;
        })->sortBy('nama_mapel');

        $mapel_wajib = $allMapel->filter(fn($m) => in_array($m->kategori, ['Umum', 'Wajib']));
        $mapel_diniyah = $allMapel->filter(fn($m) => in_array($m->kategori, ['Agama', 'Diniyah', 'Mulok']));
        $mapel_ekstra = $allMapel->filter(fn($m) => $m->kategori === 'Ekstrakurikuler');

        // Get Data Single
        $data = $this->getRaporData($santriId, $tahunAjaran, $semester);
        $dataRapor = [$data];

        // Check if download is requested
        $isPdfDownload = $request->has('download') && $request->download == '1';
        
        if ($isPdfDownload) {
            $pdf = Pdf::loadView('pendidikan.laporan.rapor-pdf-v2', compact(
                'dataRapor', 'settings', 'mapel_wajib', 'mapel_diniyah', 'mapel_ekstra', 'tahunAjaran', 'semester', 'isPdfDownload'
            ));
            $pdf->setPaper([0, 0, 609.45, 935.43], 'portrait'); // F4 in points
            
            // Sanitize filename properly - remove special characters
            $cleanTA = preg_replace('/[^A-Za-z0-9\-]/', '-', $tahunAjaran);
            $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '-', $santri->nama_santri);
            $fileName = sprintf('Rapor-%s-%s.pdf', $cleanTA, $cleanName);
            
            // Use streamDownload with explicit headers for better browser compatibility
            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, $fileName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }

        $isPdfDownload = false;
        return view('pendidikan.laporan.rapor-pdf-v2', compact(
            'dataRapor', 'settings', 'mapel_wajib', 'mapel_diniyah', 'mapel_ekstra', 'tahunAjaran', 'semester', 'isPdfDownload'
        ));
    }

    // Export Rapor Kelas
    public function exportRaporKelas(Request $request)
    {
        $kelasId = $request->kelas_id;
        $tahunAjaran = $request->tahun_ajaran;
        $semester = $request->semester;
        $gender = $request->gender;
        
        $settings = \App\Models\ReportSettings::firstOrCreate([], [
            'nama_yayasan' => 'YAYASAN PONDOK PESANTREN',
            'nama_pondok' => 'RIYADLUL HUDA',
            'kota_terbit' => 'Tasikmalaya',
        ]);
        
        $allMapel = MataPelajaran::where('is_active', true)
            ->where(function($query) use ($kelasId) {
                $query->whereDoesntHave('kelas')
                      ->orWhereHas('kelas', function($q) use ($kelasId) {
                          $q->where('kelas.id', $kelasId);
                      });
            })
            ->get();
            
        $categoryOrder = ['Umum', 'Wajib', 'Agama', 'Diniyah', 'Mulok', 'Ekstrakurikuler'];
        $allMapel = $allMapel->sortBy(function($mapel) use ($categoryOrder) {
            $order = array_search($mapel->kategori, $categoryOrder);
            return $order === false ? 99 : $order;
        })->sortBy('nama_mapel');

        $mapel_wajib = $allMapel->filter(fn($m) => in_array($m->kategori, ['Umum', 'Wajib']));
        $mapel_diniyah = $allMapel->filter(fn($m) => in_array($m->kategori, ['Agama', 'Diniyah', 'Mulok']));
        $mapel_ekstra = $allMapel->filter(fn($m) => $m->kategori === 'Ekstrakurikuler');

        // Fetch All Students in Class with optional gender filter
        $query = Santri::where('kelas_id', $kelasId)->where('is_active', true);
        
        if ($gender && $gender !== 'all') {
            $query->where('gender', $gender);
        }
        
        $santriList = $query->orderBy('nama_santri')->get();
        
        $dataRapor = [];
        foreach($santriList as $santri) {
            $dataRapor[] = $this->getRaporData($santri->id, $tahunAjaran, $semester);
        }

        // Check if download is requested
        if ($request->has('download') && $request->download == '1') {
            $kelas = Kelas::find($kelasId);
            $pdf = Pdf::loadView('pendidikan.laporan.rapor-pdf-v2', compact(
                'dataRapor', 'settings', 'mapel_wajib', 'mapel_diniyah', 'mapel_ekstra', 'tahunAjaran', 'semester'
            ));
            $pdf->setPaper([0, 0, 609.45, 935.43], 'portrait');
            
            // Sanitize filename properly - remove special characters
            $cleanKelas = preg_replace('/[^A-Za-z0-9\-]/', '-', $kelas->nama_kelas);
            $cleanTA = preg_replace('/[^A-Za-z0-9\-]/', '-', $tahunAjaran);
            $fileName = sprintf('Rapor-Kelas-%s-%s.pdf', $cleanKelas, $cleanTA);
            
            // Use streamDownload with explicit headers for better browser compatibility
            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, $fileName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }

        return view('pendidikan.laporan.rapor-pdf-v2', compact(
            'dataRapor', 'settings', 'mapel_wajib', 'mapel_diniyah', 'mapel_ekstra', 'tahunAjaran', 'semester'
        ));
    }

    // Export Daftar Nilai
    public function exportDaftarNilai(Request $request)
    {
        $kelasId = $request->kelas_id;
        $tahunAjaran = $request->tahun_ajaran;
        $semester = $request->semester;
        $gender = $request->gender; // L, P, or all
        
        $kelas = Kelas::findOrFail($kelasId);
        
        $query = NilaiSantri::where('kelas_id', $kelasId)
                        ->where('semester', $semester);

        if ($tahunAjaran && $tahunAjaran !== 'all') {
            $query->where('tahun_ajaran', $tahunAjaran);
        }

        if ($gender && $gender !== 'all') {
            $query->whereHas('santri', function($q) use ($gender) {
                $q->where('gender', $gender);
            });
        }
                        
        $dataNilai = $query->with(['santri', 'mataPelajaran'])
                        ->get()
                        ->groupBy('santri_id');
                        
        return view('pendidikan.laporan.daftar-nilai-pdf', compact('kelas', 'dataNilai', 'tahunAjaran', 'semester', 'gender'));
    }

    // Export Statistik Prestasi
    public function exportStatistikPrestasi(Request $request)
    {
        // Top 20 All Classes or Specific Class
        $tahunAjaran = $request->tahun_ajaran;
        $semester = $request->semester;
        $kelasId = $request->kelas_id;
        $gender = $request->gender;
        
        $query = NilaiSantri::where('semester', $semester);
        
        if ($tahunAjaran && $tahunAjaran !== 'all') {
            $query->where('tahun_ajaran', $tahunAjaran);
        }
                        
        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }
        
        if ($gender && $gender !== 'all') {
            $query->whereHas('santri', function($q) use ($gender) {
                $q->where('gender', $gender);
            });
        }
        
        $rankings = $query->with('santri.kelas')
                        ->get()
                        ->groupBy('santri_id')
                        ->map(function ($items) {
                            return [
                                'santri' => $items->first()->santri,
                                'rata_rata' => $items->avg('nilai_akhir'),
                                'total_nilai' => $items->sum('nilai_akhir'),
                            ];
                        })
                        ->sortByDesc('rata_rata')
                        ->take(20);

        return view('pendidikan.laporan.statistik-prestasi-pdf', compact('rankings', 'tahunAjaran', 'semester', 'kelasId', 'gender'));
    }

    // Export Rekapitulasi Kehadiran
    public function exportRekapAbsensi(Request $request)
    {
        $kelasId = $request->kelas_id;
        $tahun = $request->tahun;
        $semester = $request->semester; // Added semester filter although table structure relies on year/week usually
        $gender = $request->gender;
        
        $kelas = Kelas::findOrFail($kelasId);
        
        // Absensi structure assumes yearly/weekly data. If semester is needed, we might need date filtering.
        // For now, let's keep year as primary filter, but we can filter students by gender.
        
        $query = \App\Models\AbsensiSantri::where('kelas_id', $kelasId)
                    ->where('tahun', $tahun);
                    
        if ($gender && $gender !== 'all') {
            $query->whereHas('santri', function($q) use ($gender) {
                $q->where('gender', $gender);
            });
        }
        
        $absensi = $query->with('santri')
                    ->get()
                    ->groupBy('santri_id');
                    
        return view('pendidikan.laporan.rekap-absensi-pdf', compact('kelas', 'absensi', 'tahun', 'gender'));
    }

    // Export Ranking Kelas
    public function exportRankingKelas(Request $request)
    {
        $kelasId = $request->kelas_id;
        $tahunAjaran = $request->tahun_ajaran;
        $semester = $request->semester;
        $gender = $request->gender;
        
        $kelas = Kelas::findOrFail($kelasId);
        
        $query = NilaiSantri::where('kelas_id', $kelasId)
                        ->where('semester', $semester);

        if ($tahunAjaran && $tahunAjaran !== 'all') {
            $query->where('tahun_ajaran', $tahunAjaran);
        }
                        
        if ($gender && $gender !== 'all') {
            $query->whereHas('santri', function($q) use ($gender) {
                $q->where('gender', $gender);
            });
        }
        
        $rankings = $query->with('santri')
                        ->get()
                        ->groupBy('santri_id')
                        ->map(function ($items) {
                            return [
                                'santri' => $items->first()->santri,
                                'rata_rata' => $items->avg('nilai_akhir'),
                                'total_nilai' => $items->sum('nilai_akhir'),
                            ];
                        })
                        ->sortByDesc('rata_rata')
                        ->values();

        return view('pendidikan.laporan.ranking-kelas-pdf', compact('kelas', 'rankings', 'tahunAjaran', 'semester', 'gender'));
    }

    
    
    // Absensi Santri - Index (Alfa per 2 weeks)
    public function absensi(Request $request)
    {
        $query = \App\Models\AbsensiSantri::with(['santri', 'kelas']);
        
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('minggu_ke')) {
            $query->where('minggu_ke', $request->minggu_ke);
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        
        $absensi = $query->latest('tahun')->latest('minggu_ke')->paginate(15);
        $kelasList = Kelas::all();
        $santriList = Santri::where('is_active', true)->get();
        
        // Get current week
        $currentWeek = date('W');
        $currentYear = date('Y');
        
        return view('pendidikan.absensi.index', compact('absensi', 'kelasList', 'santriList', 'currentWeek', 'currentYear'));
    }

    // Absensi - Cetak (Print View)
    public function cetakAbsensi(Request $request)
    {
        $query = \App\Models\AbsensiSantri::with(['santri', 'kelas']);
        
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('minggu_ke')) {
            $query->where('minggu_ke', $request->minggu_ke);
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        
        $absensi = $query->latest('tahun')->latest('minggu_ke')->get(); // No pagination
        
        return view('pendidikan.absensi.cetak', compact('absensi'));
    }
    
    // Absensi - Bulk Input (Alfa per 2 weeks)
    public function storeAbsensi(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tahun' => 'required|integer',
            'minggu_ke' => 'required|integer|min:1|max:52',
            'santri' => 'required|array',
            'santri.*.id' => 'required|exists:santri,id',
            'santri.*.alfa_sorogan' => 'required|integer|min:0',
            'santri.*.alfa_menghafal_malam' => 'required|integer|min:0',
            'santri.*.alfa_menghafal_subuh' => 'required|integer|min:0',
            'santri.*.alfa_tahajud' => 'required|integer|min:0',
            'santri.*.keterangan' => 'nullable|string',
        ]);
        
        // Get 2-week period dates
        $period = \App\Models\AbsensiSantri::getTwoWeekPeriod($validated['minggu_ke'], $validated['tahun']);
        
        foreach ($validated['santri'] as $santriData) {
            \App\Models\AbsensiSantri::updateOrCreate(
                [
                    'santri_id' => $santriData['id'],
                    'tahun' => $validated['tahun'],
                    'minggu_ke' => $validated['minggu_ke'],
                ],
                [
                    'kelas_id' => $validated['kelas_id'],
                    'tanggal_mulai' => $period['start'],
                    'tanggal_selesai' => $period['end'],
                    'alfa_sorogan' => $santriData['alfa_sorogan'],
                    'alfa_menghafal_malam' => $santriData['alfa_menghafal_malam'],
                    'alfa_menghafal_subuh' => $santriData['alfa_menghafal_subuh'],
                    'alfa_tahajud' => $santriData['alfa_tahajud'],
                    'keterangan' => $santriData['keterangan'] ?? null,
                    'created_by' => \Illuminate\Support\Facades\Auth::id(),
                ]
            );
        }
        
        return redirect()->route('pendidikan.absensi')
            ->with('success', 'Data alfa berhasil disimpan');
    }
    
    // Absensi - Update
    public function updateAbsensi(Request $request, $id)
    {
        $absensi = \App\Models\AbsensiSantri::findOrFail($id);
        
        $validated = $request->validate([
            'alfa_sorogan' => 'required|integer|min:0',
            'alfa_menghafal_malam' => 'required|integer|min:0',
            'alfa_menghafal_subuh' => 'required|integer|min:0',
            'alfa_tahajud' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);
        
        $absensi->update($validated);
        
        return redirect()->route('pendidikan.absensi')
            ->with('success', 'Data alfa berhasil diperbarui');
    }
    
    // Absensi - Delete
    public function destroyAbsensi($id)
    {
        $absensi = \App\Models\AbsensiSantri::findOrFail($id);
        $absensi->delete();
        
        return redirect()->route('pendidikan.absensi')
            ->with('success', 'Data alfa berhasil dihapus');
    }
    
    // Absensi - Rekap
    public function rekapAbsensi(Request $request)
    {
        $kelasId = $request->kelas_id;
        $tahun = $request->tahun ?? date('Y');
        $mingguMulai = $request->minggu_mulai ?? 1;
        $mingguSelesai = $request->minggu_selesai ?? date('W');
        
        $kelas = Kelas::find($kelasId);
        $santriList = Santri::where('kelas_id', $kelasId)->where('is_active', true)->get();
        
        $rekap = [];
        foreach ($santriList as $santri) {
            $data = \App\Models\AbsensiSantri::where('santri_id', $santri->id)
                ->where('tahun', $tahun)
                ->whereBetween('minggu_ke', [$mingguMulai, $mingguSelesai])
                ->get();
            
            $totalSorogan = $data->sum('alfa_sorogan');
            $totalMenghafalMalam = $data->sum('alfa_menghafal_malam');
            $totalMenghafalSubuh = $data->sum('alfa_menghafal_subuh');
            $totalTahajud = $data->sum('alfa_tahajud');
            $totalAlfa = $totalSorogan + $totalMenghafalMalam + $totalMenghafalSubuh + $totalTahajud;
            
            $rekap[] = [
                'santri' => $santri,
                'sorogan' => $totalSorogan,
                'menghafal_malam' => $totalMenghafalMalam,
                'menghafal_subuh' => $totalMenghafalSubuh,
                'tahajud' => $totalTahajud,
                'total_alfa' => $totalAlfa,
            ];
        }
        
        // Sort by total alfa (descending)
        usort($rekap, function($a, $b) {
            return $b['total_alfa'] <=> $a['total_alfa'];
        });
        
        $kelasList = Kelas::all();
        
        return view('pendidikan.absensi.rekap', compact('rekap', 'kelas', 'kelasList', 'tahun', 'mingguMulai', 'mingguSelesai'));
    }
    
    

    // Tahun Ajaran - Store
    public function storeTahunAjaran(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:20|unique:tahun_ajaran,nama',
        ]);
        
        TahunAjaran::create([
            'nama' => $validated['nama'],
            'is_active' => false
        ]);
        
        return back()->with('success', 'Tahun ajaran berhasil ditambahkan');
    }
    
    // Tahun Ajaran - Delete
    public function destroyTahunAjaranId($id)
    {
        $ta = TahunAjaran::findOrFail($id);
        $ta->delete();
        
        return back()->with('success', 'Tahun ajaran berhasil dihapus');
    }

    // Kalender Pendidikan - Index
    public function kalender()
    {
        $events = \App\Models\KalenderPendidikan::orderBy('tanggal_mulai')->get();
        return view('pendidikan.kalender.index', compact('events'));
    }

    // Kalender Pendidikan - Store
    public function storeKalender(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'kategori' => 'required|in:Libur,Ujian,Kegiatan,Rapat,Lainnya',
        ]);
        
        $validated['warna'] = \App\Models\KalenderPendidikan::getCategoryColor($validated['kategori']);
        
        $event = \App\Models\KalenderPendidikan::create($validated);
        
        // 1. Create System Notification (Database -> Web/Mobile App)
        try {
            \App\Models\Notification::createSystemNotification(
                'Agenda Baru: ' . $validated['judul'],
                "Kategori: {$validated['kategori']}\nTanggal: " . date('d M Y', strtotime($validated['tanggal_mulai'])),
                'wali_santri' // Role target (bisa disesuaikan logic-nya di Notification model)
            );
        } catch (\Exception $e) {
            Log::warning('System notification failed: ' . $e->getMessage());
        }

        // 2. Send WhatsApp Notification (Fonnte -> WA Group Wali Santri)
        try {
             $fonnte = new \App\Services\FonnteService();
             // Mengambil ID Group Wali Santri dari Config/Env (Pastikan diset di .env: FONNTE_GROUP_WALI)
             $targetWali = config('services.fonnte.group_wali'); 
             
             if ($targetWali) {
                 $kategoriIcon = [
                    'Libur' => '🏖️',
                    'Ujian' => '📝',
                    'Kegiatan' => '🎯',
                    'Rapat' => '👥',
                    'Lainnya' => '📅'
                ];
                 $tanggal = date('d M Y', strtotime($validated['tanggal_mulai']));
                 if (!empty($validated['tanggal_selesai']) && $validated['tanggal_selesai'] != $validated['tanggal_mulai']) {
                    $tanggal .= ' - ' . date('d M Y', strtotime($validated['tanggal_selesai']));
                 }

                 $icon = (isset($kategoriIcon[$validated['kategori']])) ? $kategoriIcon[$validated['kategori']] : '📅';
                 $judul = $validated['judul'];
                 $kategori = $validated['kategori'];
                 $deskripsi = !empty($validated['deskripsi']) ? "\n📝 Ket: " . $validated['deskripsi'] : "";
                 
                 $message = "📌 *{$judul}*\n" .
                            "{$icon} Kategori: {$kategori}\n" .
                            "🗓 Tanggal: {$tanggal}" .
                            $deskripsi;

                 $fonnte->notify(
                     $targetWali, 
                     'AGENDA KALENDER AKADEMIK', 
                     $message
                 );
             }
        } catch (\Exception $e) {
             Log::warning('Fonnte notification failed: ' . $e->getMessage());
        }

        // 3. Send Telegram notification for new calendar event (Existing)
        try {
            $telegram = new \App\Services\TelegramService();
            $kategoriIcon = [
                'Libur' => '🏖️',
                'Ujian' => '📝',
                'Kegiatan' => '🎯',
                'Rapat' => '👥',
                'Lainnya' => '📅'
            ];
            
            $tanggal = date('d M Y', strtotime($validated['tanggal_mulai']));
            if (!empty($validated['tanggal_selesai']) && $validated['tanggal_selesai'] != $validated['tanggal_mulai']) {
                $tanggal .= ' - ' . date('d M Y', strtotime($validated['tanggal_selesai']));
            }
            
            $icon = $kategoriIcon[$validated['kategori']] ?? '📅';
            $judul = $validated['judul'];
            $kategori = $validated['kategori'];
            $deskripsi = !empty($validated['deskripsi']) ? "\n📝 " . $validated['deskripsi'] : "";

            $message = "📌 {$judul}\n" .
                       "{$icon} Kategori: {$kategori}\n" .
                       "📅 Tanggal: {$tanggal}" .
                       $deskripsi;

            $telegram->notify(
                'AGENDA BARU - KALENDER AKADEMIK',
                $message,
                '🗓️'
            );
        } catch (\Exception $e) {
            Log::warning('Telegram notification failed: ' . $e->getMessage());
        }
        
        return redirect()->route('pendidikan.kalender')
            ->with('success', 'Agenda berhasil ditambahkan');
    }

    // Kalender Pendidikan - Destroy
    // Report Settings - Index
    public function settings()
    {
        $settings = \App\Models\ReportSettings::firstOrCreate([], [
            'nama_yayasan' => 'YAYASAN PONDOK PESANTREN',
            'nama_pondok' => 'RIYADLUL HUDA',
        ]);
        
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        
        return view('pendidikan.settings.index', compact('settings', 'kelasList'));
    }

    // Report Settings - Update
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'nama_yayasan' => 'required|string|max:255',
            'nama_pondok' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string',
            'kota_terbit' => 'required|string',
            'pimpinan_nama' => 'required|string',
            'pimpinan_jabatan' => 'required|string',
            'logo_pondok' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'logo_pendidikan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'pimpinan_ttd' => 'nullable|image|mimes:jpeg,png,jpg,png|max:2048',
        ]);

        $settings = \App\Models\ReportSettings::first();

        // Handle File Uploads
        if ($request->hasFile('logo_pondok')) {
             $path = $request->file('logo_pondok')->store('images', 'public');
             $validated['logo_pondok_path'] = $path;
        }
        
        if ($request->hasFile('logo_pendidikan')) {
             $path = $request->file('logo_pendidikan')->store('images', 'public');
             $validated['logo_pendidikan_path'] = $path;
        }
        
        if ($request->hasFile('pimpinan_ttd')) {
             $path = $request->file('pimpinan_ttd')->store('ttd', 'public');
             $validated['pimpinan_ttd_path'] = $path;
        }

        $settings->update($validated);

        return redirect()->route('pendidikan.settings')
            ->with('success', 'Pengaturan rapor berhasil diperbarui');
    }
    
    // Wali Kelas TTD Update (Per Class)
    public function uploadWaliKelasTTD(Request $request, $kelasId)
    {
        $request->validate([
            'ttd_file' => 'required|image|mimes:png|max:2048', // PNG transparent requested
            'type' => 'required|string|in:umum,putra,putri', // Type of TTD
        ]);
        
        $kelas = Kelas::findOrFail($kelasId);
        $type = $request->type;
        $file = $request->file('ttd_file');
        
        if ($file) {
            // Determine path based on type
            $folder = 'ttd/wali_kelas';
            if ($type === 'putra') $folder .= '/putra';
            if ($type === 'putri') $folder .= '/putri';
            
            $path = $file->store($folder, 'public');
            
            // Update correct column
            if ($type === 'putra') {
                $kelas->wali_kelas_ttd_path_putra = $path;
                $kelas->wali_kelas_putra = $request->input('wali_kelas_nama') ?? $kelas->wali_kelas_putra;
            } elseif ($type === 'putri') {
                $kelas->wali_kelas_ttd_path_putri = $path;
                $kelas->wali_kelas_putri = $request->input('wali_kelas_nama') ?? $kelas->wali_kelas_putri;
            } else {
                $kelas->wali_kelas_ttd_path = $path;
                $kelas->wali_kelas = $request->input('wali_kelas_nama') ?? $kelas->wali_kelas;
            }
            
            $kelas->save();
        }
        
        return back()->with('success', 'Tanda tangan Wali Kelas berhasil diupload');
    }

    // ===============================
    // IJAZAH MANAGEMENT
    // ===============================
    
    public function ijazah(Request $request)
    {
        $settings = \App\Models\IjazahSetting::getSettings();
        $reportSettings = \App\Models\ReportSettings::getSettings();
        
        // Get kelas 3 Ibtida and 3 Tsanawi
        $kelasIbtida = Kelas::where('nama_kelas', 'LIKE', '%3%')
            ->where('nama_kelas', 'LIKE', '%Ibtida%')
            ->first();
        
        $kelasTsanawi = Kelas::where('nama_kelas', 'LIKE', '%3%')
            ->where('nama_kelas', 'LIKE', '%Tsanawi%')
            ->first();
        
        // Get students for each class with optional gender filter
        $gender = $request->gender;
        
        $queryIbtida = Santri::where('is_active', true);
        $queryTsanawi = Santri::where('is_active', true);
        
        if ($gender && $gender !== 'all') {
            $queryIbtida->where('gender', $gender);
            $queryTsanawi->where('gender', $gender);
        }
        
        $santriIbtida = $kelasIbtida 
            ? $queryIbtida->where('kelas_id', $kelasIbtida->id)->orderBy('nama_santri')->get() 
            : collect();
        
        $santriTsanawi = $kelasTsanawi 
            ? $queryTsanawi->where('kelas_id', $kelasTsanawi->id)->orderBy('nama_santri')->get() 
            : collect();
        
        return view('pendidikan.ijazah.index', compact(
            'settings',
            'reportSettings',
            'kelasIbtida',
            'kelasTsanawi',
            'santriIbtida',
            'santriTsanawi'
        ));
    }
    
    public function updateIjazahSettings(Request $request)
    {
        $settings = \App\Models\IjazahSetting::getSettings();
        
        $settings->update([
            'tanggal_ijazah' => $request->tanggal_ijazah,
            'tahun_ajaran' => $request->tahun_ajaran,
        ]);
        
        return back()->with('success', 'Pengaturan ijazah berhasil disimpan');
    }
    
    public function cetakIjazah(Request $request, $type, $kelasId)
    {
        $settings = \App\Models\IjazahSetting::getSettings();
        $reportSettings = \App\Models\ReportSettings::getSettings();
        
        $kelas = Kelas::findOrFail($kelasId);
        
        // Build query with optional gender filter
        $query = Santri::where('kelas_id', $kelasId)->where('is_active', true);
        
        $gender = $request->gender;
        if ($gender && $gender !== 'all') {
            $query->where('gender', $gender);
        }
        
        $santriList = $query->orderBy('nama_santri')->get();
        
        // Get grades for each santri
        $dataIjazah = [];
        foreach ($santriList as $santri) {
            $grades = NilaiSantri::where('santri_id', $santri->id)
                ->where('tahun_ajaran', $settings->tahun_ajaran)
                ->get();
            
            // Calculate average
            $rataRata = $grades->avg('nilai_akhir') ?? 0;
            
            // Generate nomor ijazah
            $nomorIjazah = $type === 'ibtida' 
                ? $settings->generateNomorIbtida() 
                : $settings->generateNomorTsanawi();
            
            $dataIjazah[] = [
                'santri' => $santri,
                'kelas' => $kelas,
                'rataRata' => $rataRata,
                'nomorIjazah' => $nomorIjazah,
                'nip' => \App\Models\IjazahSetting::generateNIP(),
            ];
        }
        
        // Check if download is requested
        if ($request->has('download') && $request->download == '1') {
            $pdf = Pdf::loadView('pendidikan.ijazah.ijazah-pdf', compact(
                'dataIjazah', 'settings', 'reportSettings', 'type', 'kelas'
            ));
            $pdf->setPaper([0, 0, 609.45, 935.43], 'portrait');
            
            // Sanitize filename properly - remove special characters
            $cleanType = ucfirst(preg_replace('/[^A-Za-z0-9\-]/', '-', $type));
            $cleanKelas = preg_replace('/[^A-Za-z0-9\-]/', '-', $kelas->nama_kelas);
            $fileName = sprintf('Ijazah-%s-%s.pdf', $cleanType, $cleanKelas);
            
            // Use streamDownload with explicit headers for better browser compatibility
            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, $fileName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }
        
        return view('pendidikan.ijazah.ijazah-pdf', compact(
            'dataIjazah',
            'settings',
            'reportSettings',
            'type',
            'kelas'
        ));
    }
    
    public function cetakIjazahSantri($type, $santriId)
    {
        $settings = \App\Models\IjazahSetting::getSettings();
        $reportSettings = \App\Models\ReportSettings::getSettings();
        
        $santri = Santri::with('kelas')->findOrFail($santriId);
        $kelas = $santri->kelas;
        
        // Get grades
        $grades = NilaiSantri::where('santri_id', $santri->id)
            ->where('tahun_ajaran', $settings->tahun_ajaran)
            ->get();
        
        // Calculate average
        $rataRata = $grades->avg('nilai_akhir') ?? 0;
        
        // Generate nomor ijazah
        $nomorIjazah = $type === 'ibtida' 
            ? $settings->generateNomorIbtida() 
            : $settings->generateNomorTsanawi();
        
        $dataIjazah = [[
            'santri' => $santri,
            'kelas' => $kelas,
            'rataRata' => $rataRata,
            'nomorIjazah' => $nomorIjazah,
            'nip' => \App\Models\IjazahSetting::generateNIP(),
        ]];
        
        // Check if download is requested
        if (request()->has('download') && request()->download == '1') {
            $pdf = Pdf::loadView('pendidikan.ijazah.ijazah-pdf', compact(
                'dataIjazah', 'settings', 'reportSettings', 'type', 'kelas'
            ));
            $pdf->setPaper([0, 0, 609.45, 935.43], 'portrait');
            
            // Sanitize filename properly - remove special characters
            $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '-', $santri->nama_santri);
            $cleanType = ucfirst(preg_replace('/[^A-Za-z0-9\-]/', '-', $type));
            $fileName = sprintf('Ijazah-%s-%s.pdf', $cleanName, $cleanType);
            
            // Use streamDownload with explicit headers for better browser compatibility
            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, $fileName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }
        
        return view('pendidikan.ijazah.ijazah-pdf', compact(
            'dataIjazah',
            'settings',
            'reportSettings',
            'type',
            'kelas'
        ));
    }

    /**
     * Helper to calculate grade statistics to reduce complexity of cetakNilai
     */
    private function getNilaiStatistics($kelasId, $tahunAjaran, $semester, $santriList, $mapelList)
    {
        // Fetch all grades for these students efficiently
        $allNilai = NilaiSantri::where('kelas_id', $kelasId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->get();
            
        // Map data for O(1) access: [santri_id][mapel_id] => nilai_akhir
        $nilaiMap = [];
        foreach ($allNilai as $n) {
            $nilaiMap[$n->santri_id][$n->mapel_id] = $n->nilai_akhir;
        }

        $nilaiData = [];
        $studentAverages = [];
        
        foreach ($santriList as $santri) {
            $nilaiData[$santri->id] = [];
            $total = 0;
            $count = 0;
            
            foreach ($mapelList as $mapel) {
                // Use the map instead of query
                $nilaiAkhir = $nilaiMap[$santri->id][$mapel->id] ?? null;
                $nilaiData[$santri->id][$mapel->id] = $nilaiAkhir;
                
                if ($nilaiAkhir) {
                    $total += $nilaiAkhir;
                    $count++;
                }
            }
            
            $studentAverages[$santri->id] = [
                'total' => $total,
                'average' => $count > 0 ? $total / $count : 0,
                'count' => $count
            ];
        }
        
        $studentRankings = [];
        $sortedStudents = collect($studentAverages)
            ->sortByDesc('average')
            ->keys();
        
        foreach ($sortedStudents as $index => $santriId) {
            $studentRankings[$santriId] = $index + 1;
        }
        
        // Calculate column statistics
        $columnStats = [];
        foreach ($mapelList as $mapel) {
            $sum = 0;
            $count = 0;
            foreach ($santriList as $santri) {
                $nilai = $nilaiData[$santri->id][$mapel->id] ?? null;
                if ($nilai) {
                    $sum += $nilai;
                    $count++;
                }
            }
            
            $columnStats[$mapel->id] = [
                'sum' => $sum,
                'average' => $count > 0 ? $sum / $count : 0,
                'count' => $count
            ];
        }
        
        return compact('nilaiData', 'studentAverages', 'studentRankings', 'columnStats');
    }
}
