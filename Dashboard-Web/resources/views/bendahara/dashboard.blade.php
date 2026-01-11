@extends('layouts.app')

@section('title', 'Dashboard Bendahara')
@section('page-title', 'Dashboard Bendahara')

@section('sidebar-menu')
    @include('bendahara.partials.sidebar-menu')
@endsection

@section('bottom-nav')
    <li class="bottom-nav-item">
        <a href="{{ route('bendahara.dashboard') }}" class="bottom-nav-link active">
            <i data-feather="home" class="bottom-nav-icon"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="bottom-nav-item">
        <a href="{{ route('bendahara.syahriah') }}" class="bottom-nav-link">
            <i data-feather="dollar-sign" class="bottom-nav-icon"></i>
            <span>Syahriah</span>
        </a>
    </li>
    <li class="bottom-nav-item">
        <a href="{{ route('bendahara.pemasukan') }}" class="bottom-nav-link">
            <i data-feather="trending-up" class="bottom-nav-icon"></i>
            <span>Pemasukan</span>
        </a>
    </li>
    <li class="bottom-nav-item">
        <a href="{{ route('bendahara.laporan') }}" class="bottom-nav-link">
            <i data-feather="file-text" class="bottom-nav-icon"></i>
            <span>Laporan</span>
        </a>
    </li>
@endsection

@section('drawer-menu')
    <li class="drawer-menu-item">
        <a href="{{ route('bendahara.pengeluaran') }}" class="drawer-menu-link">
            <i data-feather="trending-down"></i>
            <span>Pengeluaran</span>
        </a>
    </li>
    <li class="drawer-menu-item">
        <a href="{{ route('bendahara.pegawai') }}" class="drawer-menu-link">
            <i data-feather="briefcase"></i>
            <span>Pegawai</span>
        </a>
    </li>
    <li class="drawer-menu-item">
        <a href="{{ route('bendahara.gaji') }}" class="drawer-menu-link">
            <i data-feather="credit-card"></i>
            <span>Gaji</span>
        </a>
    </li>
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

@push('styles')
<style>
    /* Prevent layout shift on initial load */
    .main-content {
        min-height: 100vh;
    }
    
    /* Optimize grid rendering */
    [style*="display: grid"] {
        will-change: contents;
        contain: layout style paint;
    }

    /* ===== MOBILE RESPONSIVE OVERRIDES ===== */
    @media (max-width: 767px) {
        /* Welcome Banner - Compact on mobile */
        .welcome-banner-content {
            flex-direction: column !important;
            text-align: center !important;
            gap: 8px !important;
            padding: 16px !important;
        }
        .welcome-banner-content > div:first-child {
            width: 48px !important;
            height: 48px !important;
        }
        .welcome-banner-content > div:first-child i {
            width: 24px !important;
            height: 24px !important;
        }
        .welcome-banner-content h2 {
            font-size: 1rem !important;
            margin-bottom: 4px !important;
        }
        .welcome-banner-content p {
            font-size: 0.75rem !important;
            line-height: 1.3 !important;
        }
        
        /* Filter Section - 2 column grid on mobile */
        .filter-grid {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 12px !important;
        }
        .filter-grid > div {
            width: 100% !important;
            min-width: unset !important;
            flex: unset !important;
        }
        .filter-buttons {
            grid-column: span 2 !important;
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
        }
        .filter-buttons > button,
        .filter-buttons > a {
            width: 100% !important;
            justify-content: center !important;
        }
        
        /* KPI Cards - 2 columns on mobile */
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }
        .kpi-grid > div {
            padding: 12px !important;
        }
        .kpi-grid h3 {
            font-size: 0.9rem !important;
        }
        .kpi-grid p {
            font-size: 9px !important;
        }
        
        /* Charts - 1 column on mobile for spacious layout */
        .charts-grid {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }
        .charts-grid > div:last-child {
            grid-column: span 1 !important;
        }
        
        /* Quick Actions - 2 columns on mobile */
        .quick-actions-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        .quick-actions-grid > a {
            padding: 16px 10px !important;
            font-size: 0.7rem !important;
            box-sizing: border-box !important;
            text-align: center !important;
            flex-direction: column !important;
        }
        .quick-actions-grid > a > div {
            width: 32px !important;
            height: 32px !important;
            margin-bottom: 8px !important;
        }
        .quick-actions-grid > a i {
            width: 16px !important;
            height: 16px !important;
        }
        .quick-actions-grid > a > span {
            display: block !important;
        }
        
        /* Module Summaries - 2 columns on mobile */
        .module-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }
        .module-grid > div {
            padding: 16px !important;
        }
        .module-grid h4 {
            font-size: 0.65rem !important;
        }
        .module-grid .module-value {
            font-size: 1.1rem !important;
        }
        
        /* Recent Transactions - 1 column, scrollable tables */
        .transactions-grid {
            grid-template-columns: 1fr !important;
            gap: 16px !important;
        }
        .transactions-grid > div {
            padding: 12px !important;
        }
        .transactions-grid > div > h3 {
            font-size: 0.875rem !important;
            margin-bottom: 12px !important;
        }
        .transactions-grid > div > div[style*="overflow"] {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            margin: 0 -12px !important;
            padding: 0 12px !important;
        }
        .transactions-grid table {
            width: max-content !important;
            min-width: 100% !important;
            font-size: 11px !important;
        }
        .transactions-grid th,
        .transactions-grid td {
            padding: 8px 6px !important;
            font-size: 11px !important;
            white-space: nowrap !important;
        }
    }
</style>
@endpush

@section('content')
    <!-- Welcome Banner -->
    <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 16px; padding: 32px; margin-bottom: 32px; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3); position: relative; overflow: hidden;">
        <div style="position: absolute; top: -30px; right: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
        <div style="position: absolute; bottom: -40px; left: 30%; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
        <div style="position: absolute; top: 20%; left: -20px; width: 60px; height: 60px; background: rgba(255,255,255,0.08); border-radius: 12px; transform: rotate(15deg);"></div>
        
        <div class="welcome-banner-content" style="display: flex; align-items: center; gap: 24px; position: relative; z-index: 1; color: white;">
            <div style="background: rgba(255,255,255,0.2); width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);">
                <i data-feather="briefcase" style="width: 32px; height: 32px; color: white;"></i>
            </div>
            <div>
                <h2 style="font-size: 1.875rem; font-weight: 800; margin: 0 0 6px 0; letter-spacing: -0.025em;">Selamat Datang, {{ auth()->user()->name }} 👋</h2>
                <p style="opacity: 0.9; font-size: 1.05rem; font-weight: 400; margin: 0;">Dashboard Bendahara - Kelola keuangan pesantren dengan transparan dan akuntabel.</p>
            </div>
        </div>
    </div>

    <!-- Filter Section (Compact) -->
    <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin-bottom: 32px; border: 1px solid #f3f4f6;">
        <form method="GET" action="{{ route('bendahara.dashboard') }}">
            <div class="filter-grid" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end;">
                <div style="flex: 1; min-width: 120px;">
                    <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; display: block;">Tahun</label>
                    <input type="number" name="tahun" class="form-input" value="{{ $tahun }}" style="height: 38px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0 12px; width: 100%; font-size: 13px;">
                </div>
                
                <div style="flex: 1.5; min-width: 150px;">
                    <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; display: block;">Bulan</label>
                    <select name="bulan" class="form-select" style="height: 38px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0 12px; width: 100%; font-size: 13px;">
                        <option value="">Semua Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                        @endfor
                    </select>
                </div>

                <div style="flex: 1.5; min-width: 150px;">
                    <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; display: block;">Kelas</label>
                    <select name="kelas_id" class="form-select" style="height: 38px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0 12px; width: 100%; font-size: 13px;">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="flex: 1; min-width: 120px;">
                    <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; display: block;">Gender</label>
                    <select name="gender" class="form-select" style="height: 38px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0 12px; width: 100%; font-size: 13px;">
                        <option value="">Semua</option>
                        <option value="putra" {{ $gender == 'putra' ? 'selected' : '' }}>Putra</option>
                        <option value="putri" {{ $gender == 'putri' ? 'selected' : '' }}>Putri</option>
                    </select>
                </div>

                <div class="filter-buttons" style="display: flex; gap: 8px;">
                    <button type="submit" style="height: 38px; padding: 0 16px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 13px;">
                        <i data-feather="filter" style="width: 14px; height: 14px;"></i> Filter
                    </button>
                    <a href="{{ route('bendahara.dashboard') }}" style="height: 38px; padding: 0 12px; background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; border-radius: 8px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 13px;">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- KPI Cards Grid -->
    <div class="kpi-grid" style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; margin-bottom: 32px;">
        <!-- Saldo Dana -->
        <div style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border-radius: 16px; padding: 16px; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2); transition: transform 0.3s ease; position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';" onclick="window.location.href='{{ route('bendahara.syahriah') }}'">
            <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="display: flex; flex-direction: column; gap: 8px; position: relative; z-index: 1;">
                <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3);">
                    <i data-feather="briefcase" style="width: 18px; height: 18px; color: white;"></i>
                </div>
                <div>
                    <p style="font-size: 10px; color: rgba(255,255,255,0.9); font-weight: 700; margin-bottom: 2px; text-transform: uppercase;">Saldo Dana</p>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: white; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Rp {{ number_format($saldoDana, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Total Pemasukan -->
        <div style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border-radius: 16px; padding: 16px; box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2); transition: transform 0.3s ease; position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';" onclick="window.location.href='{{ route('bendahara.pemasukan') }}'">
            <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="display: flex; flex-direction: column; gap: 8px; position: relative; z-index: 1;">
                <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3);">
                    <i data-feather="trending-up" style="width: 18px; height: 18px; color: white;"></i>
                </div>
                <div>
                    <p style="font-size: 10px; color: rgba(255,255,255,0.9); font-weight: 700; margin-bottom: 2px; text-transform: uppercase;">Pemasukan</p>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: white; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Total Pengeluaran -->
        <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 16px; padding: 16px; box-shadow: 0 10px 20px rgba(245, 158, 11, 0.2); transition: transform 0.3s ease; position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';" onclick="window.location.href='{{ route('bendahara.pengeluaran') }}'">
            <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="display: flex; flex-direction: column; gap: 8px; position: relative; z-index: 1;">
                <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3);">
                    <i data-feather="trending-down" style="width: 18px; height: 18px; color: white;"></i>
                </div>
                <div>
                    <p style="font-size: 10px; color: rgba(255,255,255,0.9); font-weight: 700; margin-bottom: 2px; text-transform: uppercase;">Pengeluaran</p>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: white; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Total Santri Aktif -->
        <div style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 16px; padding: 16px; box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2); transition: transform 0.3s ease; position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';" onclick="window.location.href='{{ route('bendahara.data-santri') }}'">
            <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="display: flex; flex-direction: column; gap: 8px; position: relative; z-index: 1;">
                <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3);">
                    <i data-feather="users" style="width: 18px; height: 18px; color: white;"></i>
                </div>
                <div>
                    <p style="font-size: 10px; color: rgba(255,255,255,0.9); font-weight: 700; margin-bottom: 2px; text-transform: uppercase;">Santri Aktif</p>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: white; margin: 0;">
                        {{ number_format($totalSantriAktif, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Total Santri Putra -->
        <div style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 16px; padding: 16px; box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2); transition: transform 0.3s ease; position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';" onclick="window.location.href='{{ route('bendahara.data-santri', ['gender' => 'putra']) }}'">
            <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="display: flex; flex-direction: column; gap: 8px; position: relative; z-index: 1;">
                <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3);">
                    <i data-feather="user" style="width: 18px; height: 18px; color: white;"></i>
                </div>
                <div>
                    <p style="font-size: 10px; color: rgba(255,255,255,0.9); font-weight: 700; margin-bottom: 2px; text-transform: uppercase;">Santri Putra</p>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: white; margin: 0;">
                        {{ number_format($totalSantriPutra, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Total Santri Putri -->
        <div style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); border-radius: 16px; padding: 16px; box-shadow: 0 10px 20px rgba(236, 72, 153, 0.2); transition: transform 0.3s ease; position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';" onclick="window.location.href='{{ route('bendahara.data-santri', ['gender' => 'putri']) }}'">
            <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="display: flex; flex-direction: column; gap: 8px; position: relative; z-index: 1;">
                <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3);">
                    <i data-feather="user" style="width: 18px; height: 18px; color: white;"></i>
                </div>
                <div>
                    <p style="font-size: 10px; color: rgba(255,255,255,0.9); font-weight: 700; margin-bottom: 2px; text-transform: uppercase;">Santri Putri</p>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: white; margin: 0;">
                        {{ number_format($totalSantriPutri, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Santri Putra Lunas -->
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 16px; padding: 16px; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.15); transition: transform 0.3s ease; position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';" onclick="window.location.href='{{ route('bendahara.syahriah', ['gender' => 'putra', 'status_lunas' => 1]) }}'">
            <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="display: flex; flex-direction: column; gap: 8px; position: relative; z-index: 1;">
                <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3);">
                    <i data-feather="check-circle" style="width: 18px; height: 18px; color: white;"></i>
                </div>
                <div>
                    <p style="font-size: 10px; color: rgba(255,255,255,0.9); font-weight: 700; margin-bottom: 2px; text-transform: uppercase;">Putra Lunas</p>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: white; margin: 0;">
                        {{ number_format($totalSantriPutraLunas, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Santri Putri Lunas -->
        <div style="background: linear-gradient(135deg, #f472b6 0%, #db2777 100%); border-radius: 16px; padding: 16px; box-shadow: 0 10px 20px rgba(244, 114, 182, 0.15); transition: transform 0.3s ease; position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';" onclick="window.location.href='{{ route('bendahara.syahriah', ['gender' => 'putri', 'status_lunas' => 1]) }}'">
            <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="display: flex; flex-direction: column; gap: 8px; position: relative; z-index: 1;">
                <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3);">
                    <i data-feather="check-circle" style="width: 18px; height: 18px; color: white;"></i>
                </div>
                <div>
                    <p style="font-size: 10px; color: rgba(255,255,255,0.9); font-weight: 700; margin-bottom: 2px; text-transform: uppercase;">Putri Lunas</p>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: white; margin: 0;">
                        {{ number_format($totalSantriPutriLunas, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Syahriah Manual -->
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 16px; padding: 16px; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.15); transition: transform 0.3s ease; position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';" onclick="window.location.href='{{ route('bendahara.syahriah') }}'">
            <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="display: flex; flex-direction: column; gap: 8px; position: relative; z-index: 1;">
                <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3);">
                    <i data-feather="dollar-sign" style="width: 18px; height: 18px; color: white;"></i>
                </div>
                <div>
                    <p style="font-size: 10px; color: rgba(255,255,255,0.9); font-weight: 700; margin-bottom: 2px; text-transform: uppercase;">Syahriah Manual</p>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: white; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Rp {{ number_format($syriahManual, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Syahriah Gateway -->
        <div style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border-radius: 16px; padding: 16px; box-shadow: 0 10px 20px rgba(14, 165, 233, 0.15); transition: transform 0.3s ease; position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';" onclick="window.location.href='{{ route('bendahara.syahriah') }}'">
            <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="display: flex; flex-direction: column; gap: 8px; position: relative; z-index: 1;">
                <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3);">
                    <i data-feather="credit-card" style="width: 18px; height: 18px; color: white;"></i>
                </div>
                <div>
                    <p style="font-size: 10px; color: rgba(255,255,255,0.9); font-weight: 700; margin-bottom: 2px; text-transform: uppercase;">Syahriah Gateway</p>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: white; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Rp {{ number_format($syahriahGateway, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Total Tunggakan -->
        <div style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); border-radius: 16px; padding: 24px; box-shadow: 0 10px 20px rgba(239, 68, 68, 0.2); transition: transform 0.3s ease; position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';" onclick="window.location.href='{{ route('bendahara.syahriah') }}'">
            <div style="position: absolute; top: -10px; right: -10px; width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="display: flex; flex-direction: column; gap: 12px; position: relative; z-index: 1;">
                <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3);">
                    <i data-feather="alert-circle" style="width: 22px; height: 22px; color: white;"></i>
                </div>
                <div>
                    <p style="font-size: 0.8rem; color: rgba(255,255,255,0.9); font-weight: 600; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.025em;">Total Tunggakan</p>
                    <h3 style="font-size: 1.5rem; font-weight: 800; color: white; margin: 0;">
                        Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Total Gaji Bulan Ini -->
        <div style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); border-radius: 16px; padding: 16px; box-shadow: 0 10px 20px rgba(139, 92, 246, 0.2); transition: transform 0.3s ease; position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';" onclick="window.location.href='{{ route('bendahara.gaji') }}'">
            <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="display: flex; flex-direction: column; gap: 8px; position: relative; z-index: 1;">
                <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3);">
                    <i data-feather="credit-card" style="width: 18px; height: 18px; color: white;"></i>
                </div>
                <div>
                    <p style="font-size: 10px; color: rgba(255,255,255,0.9); font-weight: 700; margin-bottom: 2px; text-transform: uppercase;">Gaji Bulan Ini</p>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: white; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Rp {{ number_format($totalGajiBulanIni, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Total Gaji Tertunda -->
        <div style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 16px; padding: 16px; box-shadow: 0 10px 20px rgba(249, 115, 22, 0.2); transition: transform 0.3s ease; position: relative; overflow: hidden; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';" onclick="window.location.href='{{ route('bendahara.gaji', ['status' => 'pending']) }}'">
            <div style="position: absolute; top: -10px; right: -10px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="display: flex; flex-direction: column; gap: 8px; position: relative; z-index: 1;">
                <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.3);">
                    <i data-feather="alert-triangle" style="width: 18px; height: 18px; color: white;"></i>
                </div>
                <div>
                    <p style="font-size: 10px; color: rgba(255,255,255,0.9); font-weight: 700; margin-bottom: 2px; text-transform: uppercase;">Gaji Tertunda</p>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: white; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Rp {{ number_format($totalGajiTertunda, 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 32px;">
        <div style="background: white; border-radius: 16px; padding: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6;">
            <h3 style="font-size: 0.75rem; font-weight: 800; color: #1f2937; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                <i data-feather="bar-chart-2" style="width: 14px; height: 14px; color: #10b981;"></i>
                Keuangan ({{ $tahun }})
            </h3>
            <canvas id="chartPemasukanPengeluaran" style="max-height: 140px; width: 100%;"></canvas>
        </div>
        <div style="background: white; border-radius: 16px; padding: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6;">
            <h3 style="font-size: 0.75rem; font-weight: 800; color: #1f2937; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                <i data-feather="home" style="width: 14px; height: 14px; color: #3b82f6;"></i>
                Per Asrama
            </h3>
            <canvas id="chartPerAsrama" style="max-height: 140px; width: 100%;"></canvas>
        </div>
        <div style="background: white; border-radius: 16px; padding: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6;">
            <h3 style="font-size: 0.75rem; font-weight: 800; color: #1f2937; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                <i data-feather="grid" style="width: 14px; height: 14px; color: #8b5cf6;"></i>
                Per Kelas
            </h3>
            <canvas id="chartPerKelas" style="max-height: 140px; width: 100%;"></canvas>
        </div>
        <div style="background: white; border-radius: 16px; padding: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6;">
            <h3 style="font-size: 0.75rem; font-weight: 800; color: #1f2937; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                <i data-feather="pie-chart" style="width: 14px; height: 14px; color: #db2777;"></i>
                Putra/Putri
            </h3>
            <canvas id="chartDistribusiSantri" style="max-height: 140px; width: 100%;"></canvas>
        </div>
        <div style="background: white; border-radius: 16px; padding: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6;">
            <h3 style="font-size: 0.75rem; font-weight: 800; color: #1f2937; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                <i data-feather="dollar-sign" style="width: 14px; height: 14px; color: #f97316;"></i>
                Status Syahriah
            </h3>
            <canvas id="chartLunasMenunggak" style="max-height: 140px; width: 100%;"></canvas>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="background: white; border-radius: 20px; padding: 28px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; margin-bottom: 32px; position: relative; overflow: hidden;">
        <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: #f8fafc; border-radius: 50%; z-index: 0;"></div>
        <h3 style="font-size: 1.125rem; font-weight: 800; color: #1e2937; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; position: relative; z-index: 1;">
            <div style="width: 32px; height: 32px; background: #fff7ed; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <i data-feather="zap" style="width: 18px; height: 18px; color: #f59e0b;"></i>
            </div>
            Aksi Cepat
        </h3>
        <div class="quick-actions-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; position: relative; z-index: 1;">
            <a href="{{ route('bendahara.syahriah') }}" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 16px; border-radius: 14px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 12px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(16, 185, 129, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.2)';">
                <div style="background: rgba(255,255,255,0.2); width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i data-feather="plus-circle" style="width: 20px; height: 20px;"></i>
                </div>
                <span>Tambah Syahriah</span>
            </a>
            <a href="{{ route('bendahara.pemasukan') }}" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; padding: 16px; border-radius: 14px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 12px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(59, 130, 246, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.2)';">
                <div style="background: rgba(255,255,255,0.2); width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i data-feather="trending-up" style="width: 20px; height: 20px;"></i>
                </div>
                <span>Catat Pemasukan</span>
            </a>
            <a href="{{ route('bendahara.pengeluaran') }}" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 16px; border-radius: 14px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 12px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(245, 158, 11, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(245, 158, 11, 0.2)';">
                <div style="background: rgba(255,255,255,0.2); width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i data-feather="trending-down" style="width: 20px; height: 20px;"></i>
                </div>
                <span>Catat Pengeluaran</span>
            </a>
            <a href="{{ route('bendahara.gaji') }}" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white; padding: 16px; border-radius: 14px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 12px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(139, 92, 246, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(139, 92, 246, 0.2)';">
                <div style="background: rgba(255,255,255,0.2); width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i data-feather="credit-card" style="width: 20px; height: 20px;"></i>
                </div>
                <span>Bayar Gaji</span>
            </a>
            <a href="{{ route('bendahara.withdrawals') }}" style="background: linear-gradient(135deg, #64748b 0%, #334155 100%); color: white; padding: 16px; border-radius: 14px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 12px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 12px rgba(100, 116, 139, 0.2);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(100, 116, 139, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(100, 116, 139, 0.2)';">
                <div style="background: rgba(255,255,255,0.2); width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i data-feather="external-link" style="width: 20px; height: 20px;"></i>
                </div>
                <span>Penarikan Dana</span>
            </a>
            <a href="{{ route('bendahara.bank-accounts.index') }}" style="background: linear-gradient(135deg, #475569 0%, #1e293b 100%); color: white; padding: 16px; border-radius: 14px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 12px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 12px rgba(71, 85, 105, 0.2);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(71, 85, 105, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(71, 85, 105, 0.2)';">
                <div style="background: rgba(255,255,255,0.2); width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i data-feather="settings" style="width: 20px; height: 20px;"></i>
                </div>
                <span>Kelola Rekening</span>
            </a>
        </div>
    </div>

    <!-- Module Summaries Grid -->
    <div class="module-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 32px;">
        <!-- Data Santri Summary -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 2px solid transparent; background-image: linear-gradient(white, white), linear-gradient(135deg, #10b981 0%, #059669 100%); background-origin: border-box; background-clip: padding-box, border-box; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <h4 style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Data Santri</h4>
                    <div style="width: 32px; height: 32px; background: #ecfdf5; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="users" style="width: 16px; height: 16px; color: #10b981;"></i>
                    </div>
                </div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #1e2937; margin-bottom: 2px;">{{ number_format($totalSantriAktif, 0, ',', '.') }}</div>
                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 500;">Santri Aktif Terdaftar</div>
            </div>
            <a href="{{ route('bendahara.syahriah') }}" style="margin-top: 20px; display: flex; align-items: center; justify-content: center; padding: 10px; background: #ecfdf5; border-radius: 10px; color: #10b981; font-weight: 700; text-decoration: none; font-size: 0.8rem; transition: background 0.2s;" onmouseover="this.style.background='#d1fae5'" onmouseout="this.style.background='#ecfdf5'">
                Kelola Santri
            </a>
        </div>

        <!-- Syahriah Summary -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 2px solid transparent; background-image: linear-gradient(white, white), linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); background-origin: border-box; background-clip: padding-box, border-box; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <h4 style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Syahriah Bulan Ini</h4>
                    <div style="width: 32px; height: 32px; background: #eff6ff; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="dollar-sign" style="width: 16px; height: 16px; color: #3b82f6;"></i>
                    </div>
                </div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #1e2937; margin-bottom: 2px;">Rp {{ number_format($syahriahBulanIni, 0, ',', '.') }}</div>
                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 500;">Penerimaan Tercatat</div>
            </div>
            <a href="{{ route('bendahara.syahriah') }}" style="margin-top: 20px; display: flex; align-items: center; justify-content: center; padding: 10px; background: #eff6ff; border-radius: 10px; color: #3b82f6; font-weight: 700; text-decoration: none; font-size: 0.8rem; transition: background 0.2s;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                Detail Syahriah
            </a>
        </div>

        <!-- Pemasukan Summary -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 2px solid transparent; background-image: linear-gradient(white, white), linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); background-origin: border-box; background-clip: padding-box, border-box; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <h4 style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Pemasukan (Bulan Ini)</h4>
                    <div style="width: 32px; height: 32px; background: #f0f9ff; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="trending-up" style="width: 16px; height: 16px; color: #0ea5e9;"></i>
                    </div>
                </div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #1e2937; margin-bottom: 2px;">Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</div>
                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 500;">Penerimaan Umum</div>
            </div>
            <a href="{{ route('bendahara.pemasukan') }}" style="margin-top: 20px; display: flex; align-items: center; justify-content: center; padding: 10px; background: #f0f9ff; border-radius: 10px; color: #0ea5e9; font-weight: 700; text-decoration: none; font-size: 0.8rem; transition: background 0.2s;" onmouseover="this.style.background='#e0f2fe'" onmouseout="this.style.background='#f0f9ff'">
                Detail Pemasukan
            </a>
        </div>

        <!-- Pengeluaran Summary -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 2px solid transparent; background-image: linear-gradient(white, white), linear-gradient(135deg, #f59e0b 0%, #d97706 100%); background-origin: border-box; background-clip: padding-box, border-box; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <h4 style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Pengeluaran (Bulan Ini)</h4>
                    <div style="width: 32px; height: 32px; background: #fff7ed; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="trending-down" style="width: 16px; height: 16px; color: #f59e0b;"></i>
                    </div>
                </div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #1e2937; margin-bottom: 2px;">Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</div>
                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 500;">Operasional Tercatat</div>
            </div>
            <a href="{{ route('bendahara.pengeluaran') }}" style="margin-top: 20px; display: flex; align-items: center; justify-content: center; padding: 10px; background: #fff7ed; border-radius: 10px; color: #f59e0b; font-weight: 700; text-decoration: none; font-size: 0.8rem; transition: background 0.2s;" onmouseover="this.style.background='#ffedd5'" onmouseout="this.style.background='#fff7ed'">
                Detail Pengeluaran
            </a>
        </div>

        <!-- Pegawai Summary -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 2px solid transparent; background-image: linear-gradient(white, white), linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); background-origin: border-box; background-clip: padding-box, border-box; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <h4 style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">SDM / Pegawai</h4>
                    <div style="width: 32px; height: 32px; background: #f5f3ff; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="briefcase" style="width: 16px; height: 16px; color: #8b5cf6;"></i>
                    </div>
                </div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #1e2937; margin-bottom: 2px;">{{ number_format($totalPegawai, 0, ',', '.') }}</div>
                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 500;">Guru & Staf Aktif</div>
            </div>
            <a href="{{ route('bendahara.pegawai') }}" style="margin-top: 20px; display: flex; align-items: center; justify-content: center; padding: 10px; background: #f5f3ff; border-radius: 10px; color: #8b5cf6; font-weight: 700; text-decoration: none; font-size: 0.8rem; transition: background 0.2s;" onmouseover="this.style.background='#ede9fe'" onmouseout="this.style.background='#f5f3ff'">
                Kelola Pegawai
            </a>
        </div>

        <!-- Gaji Summary -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 2px solid transparent; background-image: linear-gradient(white, white), linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); background-origin: border-box; background-clip: padding-box, border-box; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <h4 style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Gaji Pending</h4>
                    <div style="width: 32px; height: 32px; background: #fef2f2; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="alert-triangle" style="width: 16px; height: 16px; color: #ef4444;"></i>
                    </div>
                </div>
                <div style="font-size: 1.5rem; font-weight: 800; color: #ef4444; margin-bottom: 2px;">{{ number_format($gajiTertundaCount, 0, ',', '.') }}</div>
                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 500;">Item Belum Terbayar</div>
            </div>
            <a href="{{ route('bendahara.gaji') }}" style="margin-top: 20px; display: flex; align-items: center; justify-content: center; padding: 10px; background: #fef2f2; border-radius: 10px; color: #ef4444; font-weight: 700; text-decoration: none; font-size: 0.8rem; transition: background 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                Bayar Sekarang
            </a>
        </div>

        <!-- Laporan Card -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 2px solid transparent; background-image: linear-gradient(white, white), linear-gradient(135deg, #64748b 0%, #334155 100%); background-origin: border-box; background-clip: padding-box, border-box; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <h4 style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Arsip Laporan</h4>
                    <div style="width: 32px; height: 32px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="file-text" style="width: 16px; height: 16px; color: #64748b;"></i>
                    </div>
                </div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #1e2937; margin-bottom: 2px;">Data Keuangan</div>
                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 500;">Rekapitulasi Periode</div>
            </div>
            <a href="{{ route('bendahara.laporan') }}" style="margin-top: 20px; display: flex; align-items: center; justify-content: center; padding: 10px; background: #f1f5f9; border-radius: 10px; color: #64748b; font-weight: 700; text-decoration: none; font-size: 0.8rem; transition: background 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                Buka Laporan
            </a>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="transactions-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px; margin-bottom: 32px;">
        <!-- Recent Syahriah -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6;">
            <h3 style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                <span>Pembayaran Syahriah Terbaru</span>
                <i data-feather="clock" style="width: 18px; height: 18px; color: #64748b;"></i>
            </h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9;">
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Santri</th>
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Periode</th>
                            <th style="text-align: right; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Nominal</th>
                            <th style="text-align: center; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Status</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($recentSyahriah as $syahriah)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 8px; color: #1f2937; font-weight: 500;">{{ $syahriah->santri->nama_santri ?? '-' }}</td>
                                <td style="padding: 12px 8px; color: #64748b;">{{ date('M', mktime(0, 0, 0, $syahriah->bulan, 1)) }} {{ $syahriah->tahun }}</td>
                                <td style="padding: 12px 8px; text-align: right; color: #0f172a; font-weight: 600;">Rp {{ number_format($syahriah->nominal, 0, ',', '.') }}</td>
                                <td style="padding: 12px 8px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; {{ $syahriah->is_lunas ? 'background: #ecfdf5; color: #059669;' : 'background: #fff7ed; color: #ea580c;' }}">
                                        {{ $syahriah->is_lunas ? 'Lunas' : 'Belum' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding: 24px; text-align: center; color: #94a3b8;">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Pemasukan -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6;">
            <h3 style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                <span>Pemasukan Terbaru</span>
                <i data-feather="trending-up" style="width: 18px; height: 18px; color: #10b981;"></i>
            </h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9;">
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Tanggal</th>
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Sumber</th>
                            <th style="text-align: right; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Nominal</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($recentPemasukan as $pemasukan)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 8px; color: #64748b;">{{ \Carbon\Carbon::parse($pemasukan->tanggal)->format('d M Y') }}</td>
                                <td style="padding: 12px 8px; color: #1f2937; font-weight: 500;">{{ $pemasukan->sumber }}</td>
                                <td style="padding: 12px 8px; text-align: right; color: #1d4ed8; font-weight: 600;">Rp {{ number_format($pemasukan->nominal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="padding: 24px; text-align: center; color: #94a3b8;">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Pengeluaran -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6;">
            <h3 style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                <span>Pengeluaran Terbaru</span>
                <i data-feather="trending-down" style="width: 18px; height: 18px; color: #f97316;"></i>
            </h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9;">
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Tanggal</th>
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Keperluan</th>
                            <th style="text-align: right; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Nominal</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($recentPengeluaran as $pengeluaran)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 8px; color: #64748b;">{{ \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d M Y') }}</td>
                                <td style="padding: 12px 8px; color: #1f2937; font-weight: 500;">{{ $pengeluaran->keperluan }}</td>
                                <td style="padding: 12px 8px; text-align: right; color: #c2410c; font-weight: 600;">Rp {{ number_format($pengeluaran->nominal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="padding: 24px; text-align: center; color: #94a3b8;">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Gaji -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6;">
            <h3 style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                <span>Pembayaran Gaji Terbaru</span>
                <i data-feather="credit-card" style="width: 18px; height: 18px; color: #8b5cf6;"></i>
            </h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9;">
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Pegawai</th>
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Periode</th>
                            <th style="text-align: right; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Nominal</th>
                            <th style="text-align: center; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Status</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($recentGaji as $gaji)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 8px; color: #1f2937; font-weight: 500;">{{ $gaji->pegawai->nama_pegawai ?? '-' }}</td>
                                <td style="padding: 12px 8px; color: #64748b;">{{ date('M', mktime(0, 0, 0, $gaji->bulan, 1)) }} {{ $gaji->tahun }}</td>
                                <td style="padding: 12px 8px; text-align: right; color: #6d28d9; font-weight: 600;">Rp {{ number_format($gaji->nominal, 0, ',', '.') }}</td>
                                <td style="padding: 12px 8px; text-align: center;">
                                    <span style="display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; {{ $gaji->is_dibayar ? 'background: #ecfdf5; color: #059669;' : 'background: #fef2f2; color: #dc2626;' }}">
                                        {{ $gaji->is_dibayar ? 'Dibayar' : 'Pending' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding: 24px; text-align: center; color: #94a3b8;">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Lists Santri Menunggak -->
    <div class="transactions-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
        <!-- Santri Putra Menunggak -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6;">
            <h3 style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                <span>Santri Putra Menunggak (Top 10)</span>
                <i data-feather="slash" style="width: 18px; height: 18px; color: #ef4444;"></i>
            </h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9;">
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">NIS</th>
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Nama</th>
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Kelas</th>
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Asrama</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($santriPutraMenunggak as $santri)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 8px; color: #64748b; font-family: monospace;">{{ $santri->nis }}</td>
                                <td style="padding: 12px 8px; color: #1f2937; font-weight: 600;">{{ $santri->nama_santri }}</td>
                                <td style="padding: 12px 8px; color: #64748b;">{{ $santri->kelas->nama_kelas ?? '-' }}</td>
                                <td style="padding: 12px 8px; color: #64748b;">{{ $santri->asrama->nama_asrama ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding: 24px; text-align: center; color: #94a3b8;">Tidak ada data tunggakan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Santri Putri Menunggak -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6;">
            <h3 style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                <span>Santri Putri Menunggak (Top 10)</span>
                <i data-feather="slash" style="width: 18px; height: 18px; color: #db2777;"></i>
            </h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9;">
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">NIS</th>
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Nama</th>
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Kelas</th>
                            <th style="text-align: left; padding: 12px 8px; font-size: 0.875rem; color: #64748b; font-weight: 600;">Asrama</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($santriPutriMenunggak as $santri)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 8px; color: #64748b; font-family: monospace;">{{ $santri->nis }}</td>
                                <td style="padding: 12px 8px; color: #1f2937; font-weight: 600;">{{ $santri->nama_santri }}</td>
                                <td style="padding: 12px 8px; color: #64748b;">{{ $santri->kelas->nama_kelas ?? '-' }}</td>
                                <td style="padding: 12px 8px; color: #64748b;">{{ $santri->asrama->nama_asrama ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding: 24px; text-align: center; color: #94a3b8;">Tidak ada data tunggakan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Pemasukan Pengeluaran
new Chart(document.getElementById('chartPemasukanPengeluaran'), {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Pemasukan',
            data: @json(array_values($chartPemasukanPengeluaran['pemasukan'])),
            borderColor: '#1976D2',
            backgroundColor: 'rgba(25, 118, 210, 0.1)',
            tension: 0.4
        }, {
            label: 'Pengeluaran',
            data: @json(array_values($chartPemasukanPengeluaran['pengeluaran'])),
            borderColor: '#F57C00',
            backgroundColor: 'rgba(245, 124, 0, 0.1)',
            tension: 0.4
        }]
    },
    options: { responsive: true, maintainAspectRatio: true }
});

// Chart Per Asrama
new Chart(document.getElementById('chartPerAsrama'), {
    type: 'bar',
    data: {
        labels: @json(array_keys($chartPerAsrama)),
        datasets: [{
            label: 'Jumlah Santri',
            data: @json(array_values($chartPerAsrama)),
            backgroundColor: '#4CAF50'
        }]
    },
    options: { responsive: true, maintainAspectRatio: true }
});

// Chart Per Kelas
new Chart(document.getElementById('chartPerKelas'), {
    type: 'bar',
    data: {
        labels: @json(array_keys($chartPerKelas)),
        datasets: [{
            label: 'Jumlah Santri',
            data: @json(array_values($chartPerKelas)),
            backgroundColor: '#2196F3'
        }]
    },
    options: { responsive: true, maintainAspectRatio: true }
});

// Chart Distribusi Santri
new Chart(document.getElementById('chartDistribusiSantri'), {
    type: 'pie',
    data: {
        labels: ['Putra', 'Putri'],
        datasets: [{
            data: [@json($chartDistribusiSantri['putra']), @json($chartDistribusiSantri['putri'])],
            backgroundColor: ['#1976D2', '#E91E63']
        }]
    },
    options: { responsive: true, maintainAspectRatio: true }
});

// Chart Lunas Menunggak
new Chart(document.getElementById('chartLunasMenunggak'), {
    type: 'pie',
    data: {
        labels: ['Lunas', 'Menunggak'],
        datasets: [{
            data: [@json($chartLunasMenunggak['lunas']), @json($chartLunasMenunggak['menunggak'])],
            backgroundColor: ['#4CAF50', '#f44336']
        }]
    },
    options: { responsive: true, maintainAspectRatio: true }
});
</script>
@endpush
