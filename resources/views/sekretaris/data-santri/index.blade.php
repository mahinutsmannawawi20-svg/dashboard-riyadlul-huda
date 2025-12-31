@extends('layouts.app')

@section('title', 'Data Santri')
@section('page-title', 'Data Santri')

@section('sidebar-menu')
    <li class="sidebar-menu-item">
        <a href="{{ route('sekretaris.dashboard') }}" class="sidebar-menu-link">
            <i data-feather="home" class="sidebar-menu-icon"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="sidebar-menu-item">
        <a href="{{ route('sekretaris.data-santri') }}" class="sidebar-menu-link active">
            <i data-feather="users" class="sidebar-menu-icon"></i>
            <span>Data Santri</span>
        </a>
    </li>
    <li class="sidebar-menu-item">
        <a href="{{ route('sekretaris.kartu-digital') }}" class="sidebar-menu-link">
            <i data-feather="credit-card" class="sidebar-menu-icon"></i>
            <span>Kartu Digital</span>
        </a>
    </li>
    <li class="sidebar-menu-item">
        <a href="{{ route('sekretaris.mutasi-santri') }}" class="sidebar-menu-link">
            <i data-feather="repeat" class="sidebar-menu-icon"></i>
            <span>Mutasi Santri</span>
        </a>
    </li>
    <li class="sidebar-menu-item">
        <a href="{{ route('sekretaris.kenaikan-kelas') }}" class="sidebar-menu-link">
            <i data-feather="trending-up" class="sidebar-menu-icon"></i>
            <span>Kenaikan Kelas</span>
        </a>
    </li>
    <li class="sidebar-menu-item">
        <a href="{{ route('sekretaris.perpindahan') }}" class="sidebar-menu-link">
            <i data-feather="shuffle" class="sidebar-menu-icon"></i>
            <span>Perpindahan</span>
        </a>
    </li>
    <li class="sidebar-menu-item">
        <a href="{{ route('sekretaris.laporan') }}" class="sidebar-menu-link">
            <i data-feather="file-text" class="sidebar-menu-icon"></i>
            <span>Laporan</span>
        </a>
    </li>
    <li class="sidebar-menu-item">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-menu-link" style="width: 100%; background: none; border: none; cursor: pointer; text-align: left;">
                <i data-feather="log-out" class="sidebar-menu-icon"></i>
                <span>Logout</span>
            </button>
        </form>
    </li>
@endsection

@section('bottom-nav')
    <li class="bottom-nav-item">
        <a href="{{ route('sekretaris.dashboard') }}" class="bottom-nav-link">
            <i data-feather="home" class="bottom-nav-icon"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="bottom-nav-item">
        <a href="{{ route('sekretaris.data-santri') }}" class="bottom-nav-link active">
            <i data-feather="users" class="bottom-nav-icon"></i>
            <span>Santri</span>
        </a>
    </li>
    <li class="bottom-nav-item">
        <a href="{{ route('sekretaris.mutasi-santri') }}" class="bottom-nav-link">
            <i data-feather="repeat" class="bottom-nav-icon"></i>
            <span>Mutasi</span>
        </a>
    </li>
    <li class="bottom-nav-item">
        <a href="{{ route('sekretaris.laporan') }}" class="bottom-nav-link">
            <i data-feather="file-text" class="bottom-nav-icon"></i>
            <span>Laporan</span>
        </a>
    </li>
@endsection

@section('drawer-menu')
    <li class="drawer-menu-item">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="drawer-menu-link" style="width: 100%; background: none; border: none; cursor: pointer; text-align: left;">
                <i data-feather="log-out"></i>
                <span>Logout</span>
            </button>
        </form>
    </li>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success" style="background-color: var(--color-primary-lightest); color: var(--color-primary-dark); padding: var(--spacing-md); border-radius: var(--radius-md); margin-bottom: var(--spacing-lg); border: 1px solid var(--color-primary-light);">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" style="background-color: #FFEBEE; color: #C62828; padding: var(--spacing-md); border-radius: var(--radius-md); margin-bottom: var(--spacing-lg); border: 1px solid #EF9A9A;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Combined Aesthetic Header with Actions -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 24px 32px; margin-bottom: 24px; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3); position: relative; overflow: hidden;">
        <div style="position: absolute; top: -30px; right: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
        <div style="position: absolute; bottom: -40px; left: 40%; width: 80px; height: 80px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
        
        <!-- Title Row -->
        <div style="display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 1; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="background: rgba(255,255,255,0.2); width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                    <i data-feather="users" style="width: 28px; height: 28px; color: white;"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: white; margin: 0 0 4px 0;">Data Santri</h2>
                    <p style="color: rgba(255,255,255,0.9); font-size: 0.875rem; margin: 0;">Kelola dan Pantau Data Seluruh Santri</p>
                </div>
            </div>
            <div style="display: flex; gap: 8px;">
                <button onclick="if(confirm('Yakin ingin generate Virtual Account untuk SEMUA santri yang belum punya? Proses ini mungkin memakan waktu.')) document.getElementById('form-generate-va-bulk').submit()" 
                    style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.2); color: white; padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; border: 1px solid rgba(255,255,255,0.3); cursor: pointer; backdrop-filter: blur(10px); transition: background 0.2s;" 
                    onmouseover="this.style.background='rgba(255,255,255,0.3)';" 
                    onmouseout="this.style.background='rgba(255,255,255,0.2)';">
                    <i data-feather="zap" style="width: 18px; height: 18px;"></i>
                    Generate VA Massal
                </button>

                <button onclick="if(confirm('⚠️ PERINGATAN: Aksi ini akan MENGHAPUS SEMUA Virtual Account santri yang ada. Santri harus di-generate ulang VA-nya untuk bisa membayar. Yakin ingin melanjutkan?')) document.getElementById('form-reset-va-bulk').submit()" 
                    style="display: inline-flex; align-items: center; gap: 8px; background: rgba(239, 68, 68, 0.25); color: #fee2e2; padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; border: 1px solid rgba(239, 68, 68, 0.4); cursor: pointer; backdrop-filter: blur(10px); transition: background 0.2s;" 
                    onmouseover="this.style.background='rgba(239, 68, 68, 0.35)';" 
                    onmouseout="this.style.background='rgba(239, 68, 68, 0.25)';">
                    <i data-feather="refresh-cw" style="width: 18px; height: 18px;"></i>
                    Reset VA Massal
                </button>
                
                <a href="{{ route('sekretaris.data-santri.create') }}" style="display: inline-flex; align-items: center; gap: 8px; background: white; color: #667eea; padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(0,0,0,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';">
                    <i data-feather="user-plus" style="width: 18px; height: 18px;"></i>
                    Tambah Santri
                </a>
            </div>
            
            <form id="form-generate-va-bulk" action="{{ route('santri.generate-va-bulk') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <form id="form-reset-va-bulk" action="{{ route('santri.reset-va-bulk') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
        
        <!-- Import Actions Row -->
        <div style="display: flex; align-items: center; gap: 16px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 12px; padding: 16px 20px; position: relative; z-index: 1;">
            <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
                <i data-feather="upload" style="width: 20px; height: 20px; color: white;"></i>
                <span style="font-size: 13px; font-weight: 600; color: white;">Import Data:</span>
            </div>
            
            <!-- Template Downloads -->
            <div style="display: flex; gap: 8px; flex-shrink: 0;">
                <a href="{{ route('sekretaris.data-santri.template-excel') }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.2); color: white; padding: 8px 14px; border-radius: 8px; font-weight: 500; font-size: 12px; text-decoration: none; border: 1px solid rgba(255,255,255,0.3); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)';" onmouseout="this.style.background='rgba(255,255,255,0.2)';">
                    <i data-feather="file" style="width: 14px; height: 14px;"></i>
                    Template Excel
                </a>
                <a href="{{ route('sekretaris.data-santri.template-csv') }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.2); color: white; padding: 8px 14px; border-radius: 8px; font-weight: 500; font-size: 12px; text-decoration: none; border: 1px solid rgba(255,255,255,0.3); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)';" onmouseout="this.style.background='rgba(255,255,255,0.2)';">
                    <i data-feather="file-text" style="width: 14px; height: 14px;"></i>
                    Template CSV
                </a>
            </div>
            
            <!-- Divider -->
            <div style="width: 1px; height: 32px; background: rgba(255,255,255,0.3); flex-shrink: 0;"></div>
            
            <!-- File Upload -->
            <form method="POST" action="{{ route('sekretaris.data-santri.import') }}" enctype="multipart/form-data" style="display: flex; gap: 10px; align-items: center; flex: 1;">
                @csrf
                <input type="file" name="file" accept=".csv,.xls,.xlsx" required style="flex: 1; background: white; border: none; border-radius: 8px; padding: 8px 12px; font-size: 12px; color: #374151; min-width: 150px;">
                <button type="submit" style="display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 8px 18px; border-radius: 8px; font-weight: 600; font-size: 12px; border: none; cursor: pointer; transition: transform 0.2s; box-shadow: 0 2px 8px rgba(67, 233, 123, 0.3);" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
                    <i data-feather="upload-cloud" style="width: 14px; height: 14px;"></i>
                    Import
                </button>
            </form>
        </div>
    </div>

    <!-- Filter Section with Gradient -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 20px 24px; margin-bottom: 24px; box-shadow: 0 8px 24px rgba(102, 126, 234, 0.25);">
        <form method="GET" action="{{ route('sekretaris.data-santri') }}">
            <div style="display: flex; align-items: flex-end; gap: 16px; flex-wrap: wrap;">
                <!-- Search -->
                <div style="flex: 1.5; min-width: 180px;">
                    <label style="font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.9); text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 4px; margin-bottom: 6px;">
                        <i data-feather="search" style="width: 12px; height: 12px;"></i>
                        Cari (NIS/Nama)
                    </label>
                    <input type="text" name="search" placeholder="Cari santri..." value="{{ request('search') }}" style="width: 100%; height: 40px; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; padding: 0 14px; font-size: 13px; background: rgba(255,255,255,0.95); color: #1f2937;">
                </div>
                
                <!-- Gender -->
                <div style="flex: 1; min-width: 120px;">
                    <label style="font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.9); text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 4px; margin-bottom: 6px;">
                        <i data-feather="users" style="width: 12px; height: 12px;"></i>
                        Gender
                    </label>
                    <select name="gender" style="width: 100%; height: 40px; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; padding: 0 12px; font-size: 13px; background: rgba(255,255,255,0.95); color: #1f2937; cursor: pointer;">
                        <option value="">Semua</option>
                        <option value="putra" {{ request('gender') == 'putra' ? 'selected' : '' }}>Putra</option>
                        <option value="putri" {{ request('gender') == 'putri' ? 'selected' : '' }}>Putri</option>
                    </select>
                </div>
                
                <!-- Kelas -->
                <div style="flex: 1; min-width: 140px;">
                    <label style="font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.9); text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 4px; margin-bottom: 6px;">
                        <i data-feather="book" style="width: 12px; height: 12px;"></i>
                        Kelas
                    </label>
                    <select name="kelas_id" style="width: 100%; height: 40px; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; padding: 0 12px; font-size: 13px; background: rgba(255,255,255,0.95); color: #1f2937; cursor: pointer;">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Asrama -->
                <div style="flex: 1; min-width: 140px;">
                    <label style="font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.9); text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 4px; margin-bottom: 6px;">
                        <i data-feather="home" style="width: 12px; height: 12px;"></i>
                        Asrama
                    </label>
                    <select name="asrama_id" style="width: 100%; height: 40px; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; padding: 0 12px; font-size: 13px; background: rgba(255,255,255,0.95); color: #1f2937; cursor: pointer;">
                        <option value="">Semua Asrama</option>
                        @foreach($asramaList as $asrama)
                            <option value="{{ $asrama->id }}" {{ request('asrama_id') == $asrama->id ? 'selected' : '' }}>{{ $asrama->nama_asrama }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Buttons -->
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('sekretaris.data-santri') }}" style="height: 40px; padding: 0 16px; display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; font-weight: 600; font-size: 13px; text-decoration: none; backdrop-filter: blur(10px);">
                        <i data-feather="rotate-ccw" style="width: 14px; height: 14px;"></i>
                        Reset
                    </a>
                    <button type="submit" style="height: 40px; padding: 0 20px; display: inline-flex; align-items: center; gap: 6px; background: white; color: #667eea; border: none; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                        <i data-feather="search" style="width: 14px; height: 14px;"></i>
                        Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table with Modern Styling -->
    <div style="background: white; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <th style="padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 600; color: white; text-transform: uppercase; letter-spacing: 0.5px;">No</th>
                    <th style="padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 600; color: white; text-transform: uppercase; letter-spacing: 0.5px;">NIS</th>
                    <th style="padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 600; color: white; text-transform: uppercase; letter-spacing: 0.5px;">Nama Santri</th>
                    <th style="padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 600; color: white; text-transform: uppercase; letter-spacing: 0.5px;">Gender</th>
                    <th style="padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 600; color: white; text-transform: uppercase; letter-spacing: 0.5px;">Kelas</th>
                    <th style="padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 600; color: white; text-transform: uppercase; letter-spacing: 0.5px;">Asrama</th>
                    <th style="padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 600; color: white; text-transform: uppercase; letter-spacing: 0.5px;">Kobong</th>
                    <th style="padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 600; color: white; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                    <th style="padding: 14px 16px; text-align: center; font-size: 12px; font-weight: 600; color: white; text-transform: uppercase; letter-spacing: 0.5px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($santri as $index => $s)
                    <tr style="border-bottom: 1px solid #f3f4f6; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb';" onmouseout="this.style.background='white';">
                        <td style="padding: 12px 16px; font-size: 13px; color: #6b7280; font-weight: 600;">{{ ($santri->currentPage() - 1) * $santri->perPage() + $index + 1 }}</td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #374151; font-weight: 500;">{{ $s->nis }}</td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #1f2937; font-weight: 600;">{{ $s->nama_santri }}</td>
                        <td style="padding: 12px 16px;">
                            <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; {{ $s->gender == 'putra' ? 'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;' : 'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;' }}">
                                {{ ucfirst($s->gender) }}
                            </span>
                        </td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #374151;">{{ $s->kelas->nama_kelas ?? '-' }}</td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #374151;">{{ $s->asrama->nama_asrama ?? '-' }}</td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #374151;">Kobong {{ $s->kobong->nomor_kobong ?? '-' }}</td>
                        <td style="padding: 12px 16px;">
                            <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; {{ $s->is_active ? 'background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;' : 'background: #ef4444; color: white;' }}">
                                {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <a href="{{ route('sekretaris.data-santri.edit', $s->id) }}" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1)';">
                                    <i data-feather="edit-2" style="width: 14px; height: 14px; color: white;"></i>
                                </a>
                                @if($s->is_active)
                                    <form method="POST" action="{{ route('sekretaris.data-santri.deactivate', $s->id) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Yakin ingin menonaktifkan santri ini?')" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #ff6a00 0%, #ee0979 100%); border: none; border-radius: 8px; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1)';">
                                            <i data-feather="x-circle" style="width: 14px; height: 14px; color: white;"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="padding: 48px; text-align: center; color: #9ca3af;">
                            <i data-feather="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                            <p style="margin: 0; font-size: 14px;">Tidak ada data santri</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modern Pagination -->
    @if($santri->hasPages())
        <div style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center; background: white; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 13px; color: #6b7280;">
                Menampilkan <strong>{{ $santri->firstItem() }}</strong> - <strong>{{ $santri->lastItem() }}</strong> dari <strong>{{ $santri->total() }}</strong> data
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                @if($santri->onFirstPage())
                    <span style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 8px; color: #9ca3af; cursor: not-allowed;">
                        <i data-feather="chevron-left" style="width: 18px; height: 18px;"></i>
                    </span>
                @else
                    <a href="{{ $santri->previousPageUrl() }}" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; color: white; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1)';">
                        <i data-feather="chevron-left" style="width: 18px; height: 18px;"></i>
                    </a>
                @endif

                @foreach($santri->getUrlRange(max(1, $santri->currentPage() - 2), min($santri->lastPage(), $santri->currentPage() + 2)) as $page => $url)
                    @if($page == $santri->currentPage())
                        <span style="min-width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; color: white; font-weight: 600; font-size: 13px;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="min-width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 8px; color: #374151; font-weight: 500; font-size: 13px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#e5e7eb';" onmouseout="this.style.background='#f3f4f6';">{{ $page }}</a>
                    @endif
                @endforeach

                @if($santri->hasMorePages())
                    <a href="{{ $santri->nextPageUrl() }}" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; color: white; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1)';">
                        <i data-feather="chevron-right" style="width: 18px; height: 18px;"></i>
                    </a>
                @else
                    <span style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 8px; color: #9ca3af; cursor: not-allowed;">
                        <i data-feather="chevron-right" style="width: 18px; height: 18px;"></i>
                    </span>
                @endif
            </div>
        </div>
    @endif
@endsection
