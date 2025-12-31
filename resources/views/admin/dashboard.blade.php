@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div style="padding: 24px; max-width: 1400px; margin: 0 auto;">
    <!-- Header -->
    <div style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border-radius: 16px; padding: 24px; margin-bottom: 24px; color: white;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                    <i data-feather="shield" style="width: 28px; height: 28px;"></i>
                    <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0;">Admin Dashboard</h1>
                </div>
                <p style="color: rgba(255,255,255,0.8); font-size: 0.875rem; margin: 0;">Kelola sistem, user, dan backup database</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="{{ route('backup.download') }}" style="background: #22c55e; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.875rem; display: flex; align-items: center; gap: 8px;">
                    <i data-feather="download" style="width: 16px; height: 16px;"></i>
                    Download Backup
                </a>
                <a href="{{ route('admin.activity-log') }}" style="background: #6366f1; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.875rem; display: flex; align-items: center; gap: 8px;">
                    <i data-feather="activity" style="width: 16px; height: 16px;"></i>
                    Riwayat Aktivitas
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i data-feather="check-circle" style="width: 18px; height: 18px;"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i data-feather="alert-circle" style="width: 18px; height: 18px;"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <!-- User Stats -->
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="background: #ede9fe; padding: 10px; border-radius: 10px;">
                    <i data-feather="users" style="width: 20px; height: 20px; color: #7c3aed;"></i>
                </div>
                <span style="font-size: 0.875rem; color: #64748b;">Total User</span>
            </div>
            <div style="font-size: 2rem; font-weight: 700; color: #1e293b;">{{ $userStats['total'] }}</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;">
                Admin: {{ $userStats['admin'] }} | Sekretaris: {{ $userStats['sekretaris'] }} | Bendahara: {{ $userStats['bendahara'] }} | Pendidikan: {{ $userStats['pendidikan'] }}
            </div>
        </div>

        <!-- Santri Stats -->
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="background: #dbeafe; padding: 10px; border-radius: 10px;">
                    <i data-feather="user-check" style="width: 20px; height: 20px; color: #2563eb;"></i>
                </div>
                <span style="font-size: 0.875rem; color: #64748b;">Santri Aktif</span>
            </div>
            <div style="font-size: 2rem; font-weight: 700; color: #1e293b;">{{ $santriStats['aktif'] }}</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;">
                Putra: {{ $santriStats['putra'] }} | Putri: {{ $santriStats['putri'] }} | Non-aktif: {{ $santriStats['nonaktif'] }}
            </div>
        </div>

        <!-- Financial Stats -->
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="background: #dcfce7; padding: 10px; border-radius: 10px;">
                    <i data-feather="credit-card" style="width: 20px; height: 20px; color: #16a34a;"></i>
                </div>
                <span style="font-size: 0.875rem; color: #64748b;">Pembayaran Bulan Ini</span>
            </div>
            <div style="font-size: 2rem; font-weight: 700; color: #16a34a;">{{ $financialStats['lunas_bulan_ini'] }}</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;">
                Lunas | Tunggakan bulan ini: {{ $financialStats['tunggakan_bulan_ini'] }}
            </div>
        </div>

        <!-- Tunggakan -->
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="background: #fee2e2; padding: 10px; border-radius: 10px;">
                    <i data-feather="alert-triangle" style="width: 20px; height: 20px; color: #dc2626;"></i>
                </div>
                <span style="font-size: 0.875rem; color: #64748b;">Total Tunggakan</span>
            </div>
            <div style="font-size: 2rem; font-weight: 700; color: #dc2626;">{{ $financialStats['total_tunggakan'] }}</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;">Tagihan belum lunas</div>
        </div>
    </div>

    <!-- Main Grid -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- User Management -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
            <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 1rem; font-weight: 600; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i data-feather="users" style="width: 18px; height: 18px;"></i>
                    Kelola User
                </h2>
                <button onclick="document.getElementById('addUserModal').style.display='flex'" style="background: #6366f1; color: white; border: none; padding: 8px 14px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <i data-feather="plus" style="width: 14px; height: 14px;"></i> Tambah User
                </button>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 12px 16px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b;">Nama</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b;">Email</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b;">Role</th>
                            <th style="padding: 12px 16px; text-align: center; font-size: 0.75rem; font-weight: 600; color: #64748b;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 12px 16px; font-size: 0.875rem; color: #1e293b; font-weight: 500;">{{ $user->name }}</td>
                            <td style="padding: 12px 16px; font-size: 0.875rem; color: #64748b;">{{ $user->email }}</td>
                            <td style="padding: 12px 16px;">
                                @php
                                    $roleColors = [
                                        'admin' => 'background: #1e293b; color: white;',
                                        'sekretaris' => 'background: #dbeafe; color: #1d4ed8;',
                                        'bendahara' => 'background: #dcfce7; color: #166534;',
                                        'pendidikan' => 'background: #fef3c7; color: #92400e;',
                                    ];
                                @endphp
                                <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; {{ $roleColors[$user->role] ?? '' }}">{{ $user->role }}</span>
                            </td>
                            <td style="padding: 12px 16px; text-align: center;">
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.user.destroy', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: #fee2e2; color: #dc2626; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer; font-size: 0.75rem;">
                                        <i data-feather="trash-2" style="width: 12px; height: 12px;"></i>
                                    </button>
                                </form>
                                @else
                                <span style="color: #94a3b8; font-size: 0.75rem;">Anda</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activities & System Info -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- System Info -->
            <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h3 style="font-size: 0.9rem; font-weight: 600; color: #1e293b; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px;">
                    <i data-feather="database" style="width: 16px; height: 16px;"></i>
                    Informasi Sistem
                </h3>
                <div style="display: flex; flex-direction: column; gap: 10px; font-size: 0.8rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">Database:</span>
                        <span style="color: #1e293b; font-weight: 500;">{{ strtoupper($dbInfo['connection']) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">Nama DB:</span>
                        <span style="color: #1e293b; font-weight: 500;">{{ $dbInfo['database'] }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">PHP Version:</span>
                        <span style="color: #1e293b; font-weight: 500;">{{ PHP_VERSION }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">Laravel:</span>
                        <span style="color: #1e293b; font-weight: 500;">{{ app()->version() }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); flex: 1;">
                <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9;">
                    <h3 style="font-size: 0.9rem; font-weight: 600; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i data-feather="activity" style="width: 16px; height: 16px;"></i>
                        Aktivitas Terbaru
                    </h3>
                </div>
                <div style="max-height: 300px; overflow-y: auto;">
                    @forelse($recentActivities as $activity)
                    <div style="padding: 10px 16px; border-bottom: 1px solid #f8fafc; font-size: 0.8rem;">
                        <div style="color: #1e293b; font-weight: 500;">{{ Str::limit($activity->description, 40) }}</div>
                        <div style="color: #94a3b8; font-size: 0.7rem; margin-top: 2px;">
                            {{ $activity->user?->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @empty
                    <div style="padding: 30px; text-align: center; color: #94a3b8; font-size: 0.8rem;">
                        Belum ada aktivitas
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;" onclick="if(event.target === this) this.style.display='none'">
    <div style="background: white; border-radius: 16px; padding: 24px; width: 90%; max-width: 400px; box-shadow: 0 20px 50px rgba(0,0,0,0.3);">
        <h3 style="font-size: 1.1rem; font-weight: 600; color: #1e293b; margin: 0 0 20px 0;">Tambah User Baru</h3>
        <form action="{{ route('admin.user.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 6px;">Nama</label>
                <input type="text" name="name" required style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 6px;">Email</label>
                <input type="email" name="email" required style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 6px;">Password</label>
                <input type="password" name="password" required minlength="6" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 6px;">Role</label>
                <select name="role" required style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; box-sizing: border-box;">
                    <option value="admin">Admin</option>
                    <option value="sekretaris">Sekretaris</option>
                    <option value="bendahara">Bendahara</option>
                    <option value="pendidikan">Pendidikan</option>
                </select>
            </div>
            <div style="display: flex; gap: 12px;">
                <button type="button" onclick="document.getElementById('addUserModal').style.display='none'" style="flex: 1; padding: 10px; background: #f1f5f9; color: #64748b; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Batal</button>
                <button type="submit" style="flex: 1; padding: 10px; background: #6366f1; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
