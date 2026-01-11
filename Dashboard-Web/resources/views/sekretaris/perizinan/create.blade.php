@extends('layouts.app')

@section('title', 'Buat Izin Santri')

@section('sidebar-menu')
    @include('sekretaris.partials.sidebar-menu')
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Title & Back Button -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('sekretaris.perizinan.index') }}" style="width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #6b7280; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.transform='translateX(-2px)'; this.style.color='#4caf50'" onmouseout="this.style.transform='translateX(0)'; this.style.color='#6b7280'">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin: 0;">Buat Izin Baru</h1>
                <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Input data perizinan santri manual</p>
            </div>
        </div>
    </div>

    <!-- Modern Form Card -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); max-width: 800px; margin: 0 auto; overflow: hidden;">
        <div style="background: linear-gradient(to right, #4caf50, #43a047); padding: 2px;"></div>
        <div style="padding: 32px;">
            <form action="{{ route('sekretaris.perizinan.store') }}" method="POST">
                @csrf
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                    <!-- Santri Selection -->
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 8px;">Nama Santri <span style="color: #ef4444;">*</span></label>
                        <select name="santri_id" class="form-control" required style="height: 42px; border-radius: 8px; border: 1px solid #d1d5db; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                            <option value="">-- Pilih Santri --</option>
                            @foreach($santri as $s)
                            <option value="{{ $s->id }}">{{ $s->nis }} - {{ $s->nama_santri }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jenis Izin -->
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 8px;">Jenis Izin <span style="color: #ef4444;">*</span></label>
                        <select name="jenis" class="form-control" required style="height: 42px; border-radius: 8px; border: 1px solid #d1d5db; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                            <option value="Izin">Izin Keluar</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Pulang">Pulang ke Rumah</option>
                        </select>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 8px;">Tanggal Mulai <span style="color: #ef4444;">*</span></label>
                        <input type="date" name="tgl_mulai" value="{{ date('Y-m-d') }}" required
                               style="display: block; width: 100%; height: 42px; padding: 8px 12px; font-size: 0.95rem; line-height: 1.5; color: #1f2937; background-color: #fff; border: 1px solid #d1d5db; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;">
                    </div>

                    <!-- Tanggal Selesai -->
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 8px;">Tanggal Selesai <span style="font-weight: 400; color: #9ca3af;">(Opsional)</span></label>
                        <input type="date" name="tgl_selesai" 
                               style="display: block; width: 100%; height: 42px; padding: 8px 12px; font-size: 0.95rem; line-height: 1.5; color: #1f2937; background-color: #fff; border: 1px solid #d1d5db; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;">
                    </div>

                    <!-- Alasan -->
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 8px;">Alasan <span style="color: #ef4444;">*</span></label>
                        <textarea name="alasan" rows="3" required
                                  style="display: block; width: 100%; padding: 8px 12px; font-size: 0.95rem; line-height: 1.5; color: #1f2937; background-color: #fff; border: 1px solid #d1d5db; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); resize: vertical;"></textarea>
                    </div>
                </div>

                <div style="display: flex; gap: 12px; border-top: 1px solid #f3f4f6; padding-top: 24px; margin-top: 8px;">
                    <button type="submit" style="background: #4caf50; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(76, 175, 80, 0.3); transition: all 0.2s;" onmouseover="this.style.background='#43a047'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#4caf50'; this.style.transform='translateY(0)'">
                        <i class="fas fa-save"></i> Simpan Izin
                    </button>
                    <a href="{{ route('sekretaris.perizinan.index') }}" style="background: white; color: #6b7280; border: 1px solid #d1d5db; padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; transition: all 0.2s;" onmouseover="this.style.background='#f9fafb'; this.style.color='#374151'" onmouseout="this.style.background='white'; this.style.color='#6b7280'">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
