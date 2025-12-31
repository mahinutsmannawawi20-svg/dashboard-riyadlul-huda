@extends('layouts.app')

@section('title', 'Riwayat Aktivitas')

@section('content')
<div style="padding: 24px; max-width: 1400px; margin: 0 auto;">
    <!-- Header -->
    <div style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 16px; padding: 24px; margin-bottom: 24px; color: white;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
            <i data-feather="activity" style="width: 28px; height: 28px;"></i>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0;">Riwayat Aktivitas</h1>
        </div>
        <p style="color: rgba(255,255,255,0.9); font-size: 0.875rem; margin: 0;">Lacak semua perubahan data dalam sistem</p>
    </div>

    <!-- Filters -->
    <div style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <form method="GET" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
            <div style="flex: 1; min-width: 150px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #64748b; margin-bottom: 4px;">Model</label>
                <select name="model" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                    <option value="">Semua Model</option>
                    @foreach($modelTypes as $type)
                        <option value="{{ $type }}" {{ request('model') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #64748b; margin-bottom: 4px;">Event</label>
                <select name="event" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                    <option value="">Semua Event</option>
                    <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>✅ Created</option>
                    <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>✏️ Updated</option>
                    <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>🗑️ Deleted</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #64748b; margin-bottom: 4px;">Dari Tanggal</label>
                <input type="date" name="from" value="{{ request('from') }}" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #64748b; margin-bottom: 4px;">Sampai Tanggal</label>
                <input type="date" name="to" value="{{ request('to') }}" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
            </div>
            <div style="flex: 2; min-width: 200px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #64748b; margin-bottom: 4px;">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi atau user..." style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" style="background: #6366f1; color: white; border: none; padding: 8px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <i data-feather="filter" style="width: 14px; height: 14px;"></i> Filter
                </button>
                <a href="{{ route('admin.activity-log') }}" style="background: #f1f5f9; color: #64748b; border: none; padding: 8px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; text-decoration: none;">Reset</a>
            </div>
        </form>
    </div>

    <!-- Activity List -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        @forelse($logs as $log)
            <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: flex-start; gap: 12px; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                <!-- Event Icon -->
                <div style="
                    width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
                    @if($log->event == 'created') background: #dcfce7; color: #16a34a;
                    @elseif($log->event == 'updated') background: #fef3c7; color: #d97706;
                    @elseif($log->event == 'deleted') background: #fee2e2; color: #dc2626;
                    @else background: #f1f5f9; color: #64748b;
                    @endif
                ">
                    @if($log->event == 'created')
                        <i data-feather="plus-circle" style="width: 20px; height: 20px;"></i>
                    @elseif($log->event == 'updated')
                        <i data-feather="edit-2" style="width: 20px; height: 20px;"></i>
                    @elseif($log->event == 'deleted')
                        <i data-feather="trash-2" style="width: 20px; height: 20px;"></i>
                    @else
                        <i data-feather="activity" style="width: 20px; height: 20px;"></i>
                    @endif
                </div>

                <!-- Content -->
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                        <span style="font-weight: 600; color: #1e293b;">{{ $log->description }}</span>
                        <span style="background: #f1f5f9; color: #64748b; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">{{ strtoupper($log->log_name) }}</span>
                    </div>
                    <div style="font-size: 0.8rem; color: #94a3b8; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                        <span style="display: flex; align-items: center; gap: 4px;">
                            <i data-feather="user" style="width: 12px; height: 12px;"></i>
                            {{ $log->user?->name ?? 'System' }}
                        </span>
                        <span style="display: flex; align-items: center; gap: 4px;">
                            <i data-feather="clock" style="width: 12px; height: 12px;"></i>
                            {{ $log->created_at->diffForHumans() }}
                        </span>
                        <span style="display: flex; align-items: center; gap: 4px;">
                            <i data-feather="calendar" style="width: 12px; height: 12px;"></i>
                            {{ $log->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>

                    @if($log->properties && (isset($log->properties['old']) || isset($log->properties['attributes'])))
                        <div style="margin-top: 10px; background: #f8fafc; border-radius: 8px; padding: 12px; font-size: 0.75rem;">
                            @if(isset($log->properties['old']))
                                <div style="margin-bottom: 8px;">
                                    <span style="color: #ef4444; font-weight: 600;">Data Lama:</span>
                                    <code style="display: block; margin-top: 4px; color: #64748b; word-break: break-all;">
                                        @foreach($log->properties['old'] as $key => $value)
                                            <span style="background: #fee2e2; padding: 1px 4px; border-radius: 2px; margin-right: 4px;">{{ $key }}: {{ is_array($value) ? json_encode($value) : $value }}</span>
                                        @endforeach
                                    </code>
                                </div>
                            @endif
                            @if(isset($log->properties['attributes']))
                                <div>
                                    <span style="color: #22c55e; font-weight: 600;">Data Baru:</span>
                                    <code style="display: block; margin-top: 4px; color: #64748b; word-break: break-all;">
                                        @foreach($log->properties['attributes'] as $key => $value)
                                            <span style="background: #dcfce7; padding: 1px 4px; border-radius: 2px; margin-right: 4px;">{{ $key }}: {{ is_array($value) ? json_encode($value) : $value }}</span>
                                        @endforeach
                                    </code>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div style="padding: 60px 20px; text-align: center; color: #94a3b8;">
                <i data-feather="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                <p style="margin: 0; font-size: 0.875rem;">Belum ada aktivitas tercatat</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
