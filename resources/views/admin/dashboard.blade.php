@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('sidebar-menu')
    @include('admin.partials.sidebar-menu')
@endsection

@section('content')
<div style="padding: 24px; max-width: 1400px; margin: 0 auto;">
    <!-- Header -->
    <div style="background: linear-gradient(120deg, #6366f1 0%, #8b5cf6 100%); border-radius: 20px; padding: 32px; margin-bottom: 32px; color: white; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);">
        <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
        <div style="position: absolute; bottom: -30px; left: -30px; width: 140px; height: 140px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
        
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; position: relative; z-index: 1;">
            <div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                    <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 12px; backdrop-filter: blur(4px);">
                        <i data-feather="shield" style="width: 28px; height: 28px; color: white;"></i>
                    </div>
                    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0; color: white !important;">Admin Dashboard</h1>
                </div>
                <p style="color: rgba(255,255,255,0.95) !important; font-size: 1rem; margin: 0; font-weight: 500;">Kelola sistem, user, dan backup database dengan mudah</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="{{ route('backup.download') }}" style="background: rgba(255, 255, 255, 0.2); color: white; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; border: 1px solid rgba(255,255,255,0.3); transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    <i data-feather="download" style="width: 18px; height: 18px;"></i>
                    Backup Data
                </a>
                <a href="{{ route('admin.activity-log') }}" style="background: white; color: #6366f1; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <i data-feather="activity" style="width: 18px; height: 18px;"></i>
                    Riwayat Aktivitas
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #ecfdf5; color: #047857; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; border: 1px solid #a7f3d0;">
            <i data-feather="check-circle" style="width: 24px; height: 24px;"></i>
            <span style="font-weight: 600;">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fef2f2; color: #b91c1c; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; border: 1px solid #fecaca;">
            <i data-feather="alert-circle" style="width: 24px; height: 24px;"></i>
            <span style="font-weight: 600;">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 32px;">
        <!-- User Stats -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div>
                    <span style="font-size: 0.875rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total User</span>
                    <div style="font-size: 2.25rem; font-weight: 800; color: #1e293b; margin-top: 4px;">{{ $userStats['total'] }}</div>
                </div>
                <div style="background: #f5f3ff; paddoing: 12px; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i data-feather="users" style="width: 24px; height: 24px; color: #7c3aed;"></i>
                </div>
            </div>
            <div style="font-size: 0.75rem; color: #94a3b8; display: flex; gap: 8px; flex-wrap: wrap;">
                <span style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px;">Admin: {{ $userStats['admin'] }}</span>
                <span style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px;">Sekretaris: {{ $userStats['sekretaris'] }}</span>
            </div>
        </div>

        <!-- Santri Stats -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div>
                    <span style="font-size: 0.875rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Santri Aktif</span>
                    <div style="font-size: 2.25rem; font-weight: 800; color: #1e293b; margin-top: 4px;">{{ $santriStats['aktif'] }}</div>
                </div>
                <div style="background: #eff6ff; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i data-feather="user-check" style="width: 24px; height: 24px; color: #2563eb;"></i>
                </div>
            </div>
            <div style="font-size: 0.75rem; color: #94a3b8; display: flex; gap: 8px; flex-wrap: wrap;">
                <span style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px;">Putra: {{ $santriStats['putra'] }}</span>
                <span style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px;">Putri: {{ $santriStats['putri'] }}</span>
            </div>
        </div>

        <!-- Financial Stats -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div>
                    <span style="font-size: 0.875rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Pembayaran</span>
                    <div style="font-size: 2.25rem; font-weight: 800; color: #16a34a; margin-top: 4px;">{{ $financialStats['lunas_bulan_ini'] }}</div>
                </div>
                <div style="background: #ecfdf5; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i data-feather="credit-card" style="width: 24px; height: 24px; color: #059669;"></i>
                </div>
            </div>
            <div style="font-size: 0.75rem; color: #64748b;">
                Tunggakan bulan ini: <span style="color: #dc2626; font-weight: 700;">{{ $financialStats['tunggakan_bulan_ini'] }}</span>
            </div>
        </div>

        <!-- Tunggakan -->
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div>
                    <span style="font-size: 0.875rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Tunggakan</span>
                    <div style="font-size: 2.25rem; font-weight: 800; color: #dc2626; margin-top: 4px;">{{ $financialStats['total_tunggakan'] }}</div>
                </div>
                <div style="background: #fef2f2; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i data-feather="alert-triangle" style="width: 24px; height: 24px; color: #dc2626;"></i>
                </div>
            </div>
            <div style="font-size: 0.75rem; color: #94a3b8;">
                Total tagihan belum lunas
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
        <!-- User Management -->
        <div style="background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #f1f5f9;">
            <div style="padding: 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 0;">Manajemen User</h2>
                    <p style="margin: 4px 0 0 0; color: #64748b; font-size: 0.85rem;">Kelola akses pengguna sistem</p>
                </div>
                <button onclick="document.getElementById('addUserModal').style.display='flex'" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <i data-feather="plus" style="width: 16px; height: 16px;"></i> Tambah User
                </button>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <th style="padding: 16px 24px; text-align: left; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">User Info</th>
                            <th style="padding: 16px 24px; text-align: left; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Role & Akses</th>
                            <th style="padding: 16px 24px; text-align: center; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding: 20px 24px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; background: #e0e7ff; color: #4338ca; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #1e293b; color: #0f172a;">{{ $user->name }}</div>
                                        <div style="color: #64748b; font-size: 0.85rem;">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 20px 24px;">
                                @php
                                    $roleStyles = [
                                        'admin' => 'background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;',
                                        'sekretaris' => 'background: #eff6ff; color: #1e40af; border: 1px solid #dbeafe;',
                                        'bendahara' => 'background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;',
                                        'pendidikan' => 'background: #fffbeb; color: #92400e; border: 1px solid #fde68a;',
                                    ];
                                @endphp
                                <span style="padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; {{ $roleStyles[$user->role] ?? '' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td style="padding: 20px 24px; text-align: center;">
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.user.destroy', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: #fee2e2; color: #dc2626; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; transition: all 0.2s;" title="Hapus User" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                                        <i data-feather="trash-2" style="width: 16px; height: 16px;"></i>
                                    </button>
                                </form>
                                @else
                                <span style="background: #f1f5f9; color: #94a3b8; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600;">YOU</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Activity & Info -->
        <div style="display: flex; flex-direction: column; gap: 32px;">
            <!-- System Info -->
            <div style="background: white; border-radius: 20px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
                <h3 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0 0 20px 0; display: flex; align-items: center; gap: 10px;">
                    <div style="background: #e0f2fe; padding: 6px; border-radius: 8px;">
                        <i data-feather="server" style="width: 18px; height: 18px; color: #0284c7;"></i>
                    </div>
                    Informasi Sistem
                </h3>
                <div style="display: grid; gap: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f8fafc; padding-bottom: 12px;">
                        <span style="color: #64748b; font-size: 0.9rem;">Database</span>
                        <span style="color: #0f172a; font-weight: 600; background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem;">{{ strtoupper($dbInfo['connection']) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f8fafc; padding-bottom: 12px;">
                        <span style="color: #64748b; font-size: 0.9rem;">DB Name</span>
                        <span style="color: #0f172a; font-weight: 600; font-size: 0.9rem;">{{ $dbInfo['database'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f8fafc; padding-bottom: 12px;">
                        <span style="color: #64748b; font-size: 0.9rem;">PHP</span>
                        <span style="color: #0f172a; font-weight: 600; font-size: 0.9rem;">v{{ PHP_VERSION }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b; font-size: 0.9rem;">Laravel</span>
                        <span style="color: #0f172a; font-weight: 600; font-size: 0.9rem;">v{{ app()->version() }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div style="background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); flex: 1; border: 1px solid #f1f5f9; display: flex; flex-direction: column;">
                <div style="padding: 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px;">
                        <div style="background: #fdf4ff; padding: 6px; border-radius: 8px;">
                            <i data-feather="activity" style="width: 18px; height: 18px; color: #c026d3;"></i>
                        </div>
                        Aktivitas
                    </h3>
                    <a href="{{ route('admin.activity-log') }}" style="font-size: 0.8rem; color: #6366f1; text-decoration: none; font-weight: 600;">Lihat Semua</a>
                </div>
                <div style="overflow-y: auto; flex: 1; padding: 0 10px;">
                    @forelse($recentActivities as $activity)
                    <div style="padding: 16px 10px; border-bottom: 1px solid #f8fafc; font-size: 0.9rem; display: flex; gap: 12px;">
                        <div style="margin-top: 4px;">
                            <div style="width: 8px; height: 8px; background: #cbd5e1; border-radius: 50%;"></div>
                        </div>
                        <div>
                            <div style="color: #334155; font-weight: 500; line-height: 1.4;">{{ Str::limit($activity->description, 50) }}</div>
                            <div style="color: #94a3b8; font-size: 0.75rem; margin-top: 4px;">
                                {{ $activity->user?->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div style="padding: 40px; text-align: center; color: #94a3b8;">
                        <i data-feather="inbox" style="width: 32px; height: 32px; margin-bottom: 8px; opacity: 0.5;"></i>
                        <p style="margin: 0; font-size: 0.9rem;">Belum ada aktivitas</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; z-index: 1000;" onclick="if(event.target === this) this.style.display='none'">
    <div style="background: white; border-radius: 20px; padding: 32px; width: 90%; max-width: 420px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <h3 style="font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0 0 8px 0;">Tambah User Baru</h3>
        <p style="color: #64748b; font-size: 0.9rem; margin: 0 0 24px 0;">Isi form untuk membuat akun pengguna baru.</p>
        
        <form action="{{ route('admin.user.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 8px;">Nama Lengkap</label>
                <input type="text" name="name" required style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.95rem; box-sizing: border-box; transition: all 0.2s;" onfocus="this.style.borderColor='#6366f1'; this.style.outline='none'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 8px;">Email Address</label>
                <input type="email" name="email" required style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.95rem; box-sizing: border-box; transition: all 0.2s;" onfocus="this.style.borderColor='#6366f1'; this.style.outline='none'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 8px;">Password</label>
                <input type="password" name="password" required minlength="6" style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.95rem; box-sizing: border-box; transition: all 0.2s;" onfocus="this.style.borderColor='#6366f1'; this.style.outline='none'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div style="margin-bottom: 28px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 8px;">Role</label>
                <div style="position: relative;">
                    <select name="role" required style="width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 0.95rem; box-sizing: border-box; appearance: none; background: white; cursor: pointer; transition: all 0.2s;" onfocus="this.style.borderColor='#6366f1'; this.style.outline='none'" onblur="this.style.borderColor='#e2e8f0'">
                        <option value="admin">Admin</option>
                        <option value="sekretaris">Sekretaris</option>
                        <option value="bendahara">Bendahara</option>
                        <option value="pendidikan">Pendidikan</option>
                    </select>
                    <i data-feather="chevron-down" style="position: absolute; right: 14px; top: 14px; width: 20px; height: 20px; color: #94a3b8; pointer-events: none;"></i>
                </div>
            </div>
            <div style="display: flex; gap: 16px;">
                <button type="button" onclick="document.getElementById('addUserModal').style.display='none'" style="flex: 1; padding: 12px; background: #f1f5f9; color: #64748b; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">Batal</button>
                <button type="submit" style="flex: 1; padding: 12px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">Simpan User</button>
            </div>
        </form>
    </div>
</div>
@endsection
