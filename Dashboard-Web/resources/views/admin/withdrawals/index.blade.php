@extends('layouts.app')

@section('title', 'Tracking Pencairan Dana')
@section('page-title', 'Tracking Pencairan Dana')

@section('sidebar-menu')
    @include('admin.partials.sidebar-menu')
@endsection

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <!-- Filter Card -->
    <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #f1f5f9; margin-bottom: 24px;">
        <form action="{{ route('admin.withdrawals') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">Status</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.9rem;">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending (Diproses)</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved (Berhasil)</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected (Ditolak)</option>
                </select>
            </div>
            <button type="submit" style="background: #1e293b; color: white; padding: 10px 20px; border-radius: 10px; font-weight: 700; border: none; cursor: pointer;">Filter</button>
            <a href="{{ route('admin.withdrawals') }}" style="background: #f1f5f9; color: #475569; padding: 10px 20px; border-radius: 10px; font-weight: 700; text-decoration: none; border: 1px solid #e2e8f0;">Reset</a>
        </form>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div style="background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; padding: 16px; border-radius: 8px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <i data-feather="check-circle" style="width: 20px; height: 20px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Withdrawals Table -->
    <div style="background: white; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #f1f5f9;">
                    <th style="text-align: left; padding: 16px; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Waktu & Pengaju</th>
                    <th style="text-align: left; padding: 16px; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Bank Tujuan</th>
                    <th style="text-align: right; padding: 16px; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Nominal</th>
                    <th style="text-align: center; padding: 16px; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Status</th>
                    <th style="text-align: center; padding: 16px; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $withdrawal)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#fcfdfe'" onmouseout="this.style.background='white'">
                        <td style="padding: 16px;">
                            <div style="font-weight: 700; color: #1e2937;">{{ $withdrawal->user->name }}</div>
                            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;">{{ $withdrawal->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-weight: 600; color: #475569;">{{ $withdrawal->bankAccount->bank_name }}</div>
                            <div style="font-size: 0.8rem; color: #64748b;">{{ $withdrawal->bankAccount->account_number }} a.n {{ $withdrawal->bankAccount->account_holder }}</div>
                        </td>
                        <td style="padding: 16px; text-align: right;">
                            <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a;">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</div>
                            @if($withdrawal->notes)
                                <div style="font-size: 0.75rem; color: #64748b; font-style: italic;">"{{ $withdrawal->notes }}"</div>
                            @endif
                        </td>
                        <td style="padding: 16px; text-align: center;">
                            @php
                                $statusStyles = [
                                    'pending' => ['bg' => '#eff6ff', 'color' => '#3b82f6', 'label' => 'Pending'],
                                    'approved' => ['bg' => '#ecfdf5', 'color' => '#10b981', 'label' => 'Berhasil'],
                                    'rejected' => ['bg' => '#fef2f2', 'color' => '#ef4444', 'label' => 'Ditolak'],
                                ];
                                $style = $statusStyles[$withdrawal->status] ?? $statusStyles['pending'];
                            @endphp
                            <span style="background: {{ $style['bg'] }}; color: {{ $style['color'] }}; font-size: 10px; font-weight: 800; padding: 4px 10px; border-radius: 6px; text-transform: uppercase;">{{ $style['label'] }}</span>
                        </td>
                        <td style="padding: 16px; text-align: center;">
                            @if($withdrawal->status == 'pending')
                                <button onclick="openApproveModal({{ json_encode($withdrawal) }})" style="background: #10b981; color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-size: 12px;">
                                    <i data-feather="check" style="width: 14px; height: 14px;"></i> Proses
                                </button>
                            @else
                                @if($withdrawal->proof_of_transfer)
                                    <a href="{{ asset('storage/' . $withdrawal->proof_of_transfer) }}" target="_blank" style="color: #6366f1; text-decoration: none; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 4px;">
                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i> Bukti
                                    </a>
                                @else
                                    <span style="color: #94a3b8; font-size: 11px;">Selesai</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 48px; text-align: center; color: #94a3b8;">
                            <i data-feather="inbox" style="width: 48px; height: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                            <p>Tidak ada data penarikan dana.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px;">
        {{ $withdrawals->links() }}
    </div>
</div>

<!-- Modal Approve -->
<div id="modalApprove" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: white; border-radius: 20px; width: 100%; max-width: 500px; padding: 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #1e2937; margin: 0;">Proses Penarikan Dana</h3>
            <button onclick="document.getElementById('modalApprove').style.display='none'" style="background: none; border: none; color: #94a3b8; cursor: pointer;">
                <i data-feather="x"></i>
            </button>
        </div>

        <div style="background: #f8fafc; border-radius: 12px; padding: 16px; margin-bottom: 24px; border: 1px solid #e2e8f0;">
            <div style="margin-bottom: 12px; display: flex; justify-content: space-between;">
                <span style="color: #64748b; font-size: 0.875rem;">Nominal:</span>
                <span style="font-weight: 800; color: #1e2937;" id="modal_amount"></span>
            </div>
            <div style="margin-bottom: 12px; display: flex; justify-content: space-between;">
                <span style="color: #64748b; font-size: 0.875rem;">Bank:</span>
                <span style="font-weight: 700; color: #475569;" id="modal_bank"></span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #64748b; font-size: 0.875rem;">Rekening:</span>
                <span style="font-weight: 700; color: #475569;" id="modal_account"></span>
            </div>
        </div>

        <form id="formApprove" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="status" id="modal_status" value="approved">
            
            <div id="uploadSection" style="margin-bottom: 24px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Upload Bukti Transfer</label>
                <div style="border: 2px dashed #e2e8f0; border-radius: 12px; padding: 24px; text-align: center; transition: border-color 0.2s;" onmouseover="this.style.borderColor='#3b82f6'" onmouseout="this.style.borderColor='#e2e8f0'">
                    <i data-feather="upload-cloud" style="width: 32px; height: 32px; color: #94a3b8; margin-bottom: 8px;"></i>
                    <input type="file" name="proof_of_transfer" id="proof_of_transfer" required style="width: 100%; font-size: 0.8rem;">
                    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 8px;">Format: JPG, PNG (Maks 2MB)</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <button type="button" onclick="rejectMode()" style="background: white; color: #ef4444; border: 1px solid #fee2e2; border-radius: 12px; padding: 14px; font-weight: 700; cursor: pointer;">Tolak</button>
                <button type="submit" id="btnSubmit" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 12px; padding: 14px; font-weight: 700; cursor: pointer;">Setujui & Kirim</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openApproveModal(withdrawal) {
        document.getElementById('formApprove').action = `/admin/withdrawals/${withdrawal.id}/approve`;
        document.getElementById('modal_amount').innerText = 'Rp ' + parseInt(withdrawal.amount).toLocaleString('id-ID');
        document.getElementById('modal_bank').innerText = withdrawal.bank_account.bank_name;
        document.getElementById('modal_account').innerText = withdrawal.bank_account.account_number + ' a.n ' + withdrawal.bank_account.account_holder;
        
        // Reset to approve mode
        document.getElementById('modal_status').value = 'approved';
        document.getElementById('uploadSection').style.display = 'block';
        document.getElementById('proof_of_transfer').required = true;
        document.getElementById('btnSubmit').innerText = 'Setujui & Kirim';
        document.getElementById('btnSubmit').style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
        
        document.getElementById('modalApprove').style.display = 'flex';
    }

    function rejectMode() {
        if(confirm('Apakah Anda yakin ingin menolak permintaan penarikan ini?')) {
            document.getElementById('modal_status').value = 'rejected';
            document.getElementById('uploadSection').style.display = 'none';
            document.getElementById('proof_of_transfer').required = false;
            document.getElementById('formApprove').submit();
        }
    }

    // Close modal on click outside
    window.onclick = function(event) {
        if (event.target.id == 'modalApprove') {
            document.getElementById('modalApprove').style.display = 'none';
        }
    }
</script>
@endsection
