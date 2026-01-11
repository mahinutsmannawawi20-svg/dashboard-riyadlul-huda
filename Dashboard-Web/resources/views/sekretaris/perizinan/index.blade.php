@extends('layouts.app')

@section('title', 'Perizinan Santri')

@section('sidebar-menu')
    @include('sekretaris.partials.sidebar-menu')
@endsection

@section('content')
<div class="container-fluid">

    @if(session('success'))
    <div class="alert alert-success" style="background-color: var(--color-primary-lightest); color: var(--color-primary-dark); padding: var(--spacing-md); border-radius: var(--radius-md); margin-bottom: var(--spacing-lg); border: 1px solid var(--color-primary-light);">
        {{ session('success') }}
    </div>
    @endif

    <!-- Compact Gradient Header -->
    <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 10px; padding: 16px 24px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(59,130,246,0.25);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="background: rgba(255,255,255,0.2); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                    <i class="fas fa-envelope-open-text" style="font-size: 20px; color: white;"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.1rem; font-weight: 700; color: white; margin: 0 0 2px 0;">Perizinan Santri</h2>
                    <p style="color: rgba(255,255,255,0.85); font-size: 0.75rem; margin: 0;">Kelola pengajuan izin pulang/sakit santri</p>
                </div>
            </div>
        </div>
        
        <!-- Modern Filter Section -->
        <div style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 16px;">
            <form method="GET" action="{{ route('sekretaris.perizinan.index') }}" style="display: flex; align-items: end; gap: 12px; flex-wrap: wrap;">
                <!-- Search -->
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-size: 10px; font-weight: 600; color: rgba(255,255,255,0.9); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; display: block;">Cari Santri</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIS..." 
                        style="width: 100%; height: 38px; border: 1px solid rgba(255,255,255,0.3); border-radius: 6px; padding: 0 12px; font-size: 13px; background: rgba(255,255,255,0.95); color: #1f2937;">
                </div>
                
                <!-- Status -->
                <div style="min-width: 140px;">
                    <label style="font-size: 10px; font-weight: 600; color: rgba(255,255,255,0.9); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; display: block;">Status Izin</label>
                    <select name="status" style="width: 100%; height: 38px; border: 1px solid rgba(255,255,255,0.3); border-radius: 6px; padding: 0 12px; font-size: 13px; background: rgba(255,255,255,0.95); color: #1f2937; cursor: pointer;">
                        <option value="">Semua Status</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                
                <!-- Tanggal Range -->
                <div style="min-width: 140px;">
                    <label style="font-size: 10px; font-weight: 600; color: rgba(255,255,255,0.9); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; display: block;">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                        style="width: 100%; height: 38px; border: 1px solid rgba(255,255,255,0.3); border-radius: 6px; padding: 0 12px; font-size: 13px; background: rgba(255,255,255,0.95); color: #1f2937;">
                </div>
                <div style="min-width: 140px;">
                    <label style="font-size: 10px; font-weight: 600; color: rgba(255,255,255,0.9); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; display: block;">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                        style="width: 100%; height: 38px; border: 1px solid rgba(255,255,255,0.3); border-radius: 6px; padding: 0 12px; font-size: 13px; background: rgba(255,255,255,0.95); color: #1f2937;">
                </div>
                
                <!-- Buttons -->
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('sekretaris.perizinan.create') }}" style="height: 38px; padding: 0 16px; background: #10b981; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; text-decoration: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                        <i class="fas fa-plus"></i> Buat Izin
                    </a>
                    <button type="submit" style="height: 38px; padding: 0 16px; background: #1e40af; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('sekretaris.perizinan.index') }}" style="height: 38px; padding: 0 16px; background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 6px; text-decoration: none;">
                        <i class="fas fa-sync"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Table Card -->
    <div style="background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 1px solid rgba(59,130,246,0.1);">
        <div class="table-responsive">
            <table class="table" style="font-size: 13px; width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr style="background: #f8fafc; color: #475569;">
                        <th style="padding: 12px 16px; border-bottom: 2px solid #e2e8f0; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">Tanggal Request</th>
                        <th style="padding: 12px 16px; border-bottom: 2px solid #e2e8f0; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">Nama Santri</th>
                        <th style="padding: 12px 16px; border-bottom: 2px solid #e2e8f0; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">Jenis Izin</th>
                        <th style="padding: 12px 16px; border-bottom: 2px solid #e2e8f0; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">Waktu Izin</th>
                        <th style="padding: 12px 16px; border-bottom: 2px solid #e2e8f0; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">Alasan</th>
                        <th style="padding: 12px 16px; border-bottom: 2px solid #e2e8f0; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">Bukti</th>
                        <th style="padding: 12px 16px; border-bottom: 2px solid #e2e8f0; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">Status</th>
                        <th style="padding: 12px 16px; border-bottom: 2px solid #e2e8f0; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($perizinan as $item)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                        <td style="padding: 12px 16px; color: #64748b;">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td style="padding: 12px 16px; font-weight: 600; color: #334155;">{{ $item->santri->nama_santri ?? '-' }}</td>
                        <td style="padding: 12px 16px;">
                            <span style="display: inline-block; padding: 4px 10px; background: #e0f2fe; color: #0284c7; border-radius: 20px; font-size: 11px; font-weight: 600;">{{ $item->jenis }}</span>
                        </td>
                        <td style="padding: 12px 16px; color: #334155;">
                            {{ \Carbon\Carbon::parse($item->tgl_mulai)->format('d M') }}
                            @if($item->tgl_selesai)
                                - {{ \Carbon\Carbon::parse($item->tgl_selesai)->format('d M Y') }}
                            @else
                                <span class="text-muted">(1 hari)</span>
                            @endif
                        </td>
                        <td style="padding: 12px 16px; color: #64748b;">{{ Str::limit($item->alasan, 30) }}</td>
                        <td style="padding: 12px 16px;">
                            @if($item->bukti_foto)
                                <a href="{{ $item->bukti_foto }}" target="_blank" 
                                   style="color: #3b82f6; text-decoration: none; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-image"></i> Lihat
                                </a>
                            @else
                                <span style="color: #94a3b8; font-size: 12px;">-</span>
                            @endif
                        </td>
                        <td style="padding: 12px 16px;">
                            @if($item->status == 'Pending')
                                <span style="display: inline-block; padding: 4px 10px; background: #fef3c7; color: #d97706; border-radius: 20px; font-size: 11px; font-weight: 600;">Pending</span>
                            @elseif($item->status == 'Disetujui')
                                <span style="display: inline-block; padding: 4px 10px; background: #dcfce7; color: #16a34a; border-radius: 20px; font-size: 11px; font-weight: 600;">Disetujui</span>
                            @else
                                <span style="display: inline-block; padding: 4px 10px; background: #fee2e2; color: #dc2626; border-radius: 20px; font-size: 11px; font-weight: 600;">Ditolak</span>
                            @endif
                        </td>
                        <td style="padding: 12px 16px;">
                            <div style="display: flex; gap: 6px;">
                                <a href="{{ route('sekretaris.perizinan.edit', $item->id) }}"
                                   style="padding: 6px 10px; background: #fff; border: 1px solid #f59e0b; color: #f59e0b; border-radius: 6px; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center;"
                                   title="Edit"
                                   onmouseover="this.style.background='#fef3c7';"
                                   onmouseout="this.style.background='#fff';">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if($item->status == 'Pending')
                                <form action="{{ route('sekretaris.perizinan.approval', $item->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="Disetujui">
                                    <button type="submit" 
                                        style="padding: 6px 10px; background: #fff; border: 1px solid #16a34a; color: #16a34a; border-radius: 6px; transition: all 0.2s;"
                                        title="Setujui"
                                        onmouseover="this.style.background='#dcfce7';"
                                        onmouseout="this.style.background='#fff';">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('sekretaris.perizinan.approval', $item->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="Ditolak">
                                    <button type="submit" 
                                        style="padding: 6px 10px; background: #fff; border: 1px solid #dc2626; color: #dc2626; border-radius: 6px; transition: all 0.2s;"
                                        title="Tolak"
                                        onmouseover="this.style.background='#fee2e2';"
                                        onmouseout="this.style.background='#fff';">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endif

                                <form action="{{ route('sekretaris.perizinan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        style="padding: 6px 10px; background: #fff; border: 1px solid #e2e8f0; color: #ef4444; border-radius: 6px; transition: all 0.2s;"
                                        title="Hapus"
                                        onmouseover="this.style.background='#fee2e2'; this.style.borderColor='#ef4444';"
                                        onmouseout="this.style.background='#fff'; this.style.borderColor='#e2e8f0';">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Belum ada pengajuan izin</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $perizinan->links() }}
        </div>
    </div>
</div>
@endsection
