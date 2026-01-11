@extends('layouts.app')

@section('title', 'Penarikan Dana')
@section('page-title', 'Penarikan Dana')

@section('sidebar-menu')
    @include('bendahara.partials.sidebar-menu')
@endsection

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 32px;">
        <!-- Withdrawal Form -->
        <div>
            <!-- Balance Card -->
            <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 20px; padding: 24px; color: white; margin-bottom: 24px; box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4);">
                <h4 style="margin: 0; font-size: 0.875rem; font-weight: 500; opacity: 0.9;">Saldo Payment Gateway Available</h4>
                <h2 style="margin: 8px 0 0 0; font-size: 2rem; font-weight: 800;">Rp {{ number_format($saldoPaymentGateway ?? 0, 0, ',', '.') }}</h2>
                <p style="margin: 8px 0 0 0; font-size: 0.8rem; opacity: 0.8;">Dana yang tersedia untuk ditarik ke rekening yayasan.</p>
            </div>

            <div style="background: white; border-radius: 20px; padding: 32px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; position: sticky; top: 24px;">
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #1e2937; margin-bottom: 8px;">Ajukan Penarikan</h3>
                <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 24px;">Tarik saldo operasional ke rekening bank yang terdaftar.</p>

                <form action="{{ route('bendahara.withdrawals.store') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Pilih Rekening Tujuan</label>
                        <select name="bank_account_id" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc; font-size: 0.95rem; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2364748b%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
                            <option value="">Pilih Rekening</option>
                            @foreach($bankAccounts as $account)
                                <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ $account->account_number }} ({{ $account->account_holder }})</option>
                            @endforeach
                        </select>
                        <a href="{{ route('bendahara.bank-accounts.index') }}" style="display: inline-block; margin-top: 8px; font-size: 12px; color: #3b82f6; font-weight: 600; text-decoration: none;">+ Tambah Rekening Lain</a>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Jumlah Penarikan (Rp)</label>
                        <input type="text" name="amount" id="amount" required placeholder="Contoh: 1.000.000" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 1.1rem; font-weight: 700; color: #10b981;">
                    </div>

                    <div style="margin-bottom: 32px;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Catatan (Opsional)</label>
                        <textarea name="notes" placeholder="Tuliskan keterangan jika ada..." style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; min-height: 80px; font-size: 0.95rem; resize: vertical;"></textarea>
                    </div>

                    <button type="submit" style="width: 100%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 16px; border-radius: 14px; font-weight: 700; border: none; cursor: pointer; font-size: 1.05rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); transition: all 0.2s ease;">Ajukan Penarikan Sekarang</button>
                </form>
            </div>
        </div>

        <!-- Withdrawal History -->
        <div>
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #1e2937; margin-bottom: 24px;">Riwayat Penarikan</h3>
            
            <div style="display: flex; flex-direction: column; gap: 16px;">
                @forelse($withdrawals as $withdrawal)
                    <div style="background: white; border-radius: 16px; padding: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                            <div>
                                <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">{{ $withdrawal->created_at->format('d M Y, H:i') }}</span>
                                <h4 style="font-size: 1.125rem; font-weight: 800; color: #1e2937; margin: 4px 0;">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</h4>
                            </div>
                            @php
                                $statusStyles = [
                                    'pending' => ['bg' => '#eff6ff', 'color' => '#3b82f6', 'label' => 'Diproses'],
                                    'approved' => ['bg' => '#ecfdf5', 'color' => '#10b981', 'label' => 'Disetujui'],
                                    'rejected' => ['bg' => '#fef2f2', 'color' => '#ef4444', 'label' => 'Ditolak'],
                                ];
                                $style = $statusStyles[$withdrawal->status] ?? $statusStyles['pending'];
                            @endphp
                            <span style="background: {{ $style['bg'] }}; color: {{ $style['color'] }}; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 6px; text-transform: uppercase;">{{ $style['label'] }}</span>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                            <div style="width: 28px; height: 28px; background: #f8fafc; border-radius: 6px; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0;">
                                <i data-feather="credit-card" style="width: 14px; height: 14px; color: #64748b;"></i>
                            </div>
                            <p style="font-size: 0.875rem; color: #475569; margin: 0;">{{ $withdrawal->bankAccount->bank_name }} - {{ $withdrawal->bankAccount->account_number }}</p>
                        </div>

                        @if($withdrawal->notes)
                            <div style="background: #f8fafc; border-radius: 8px; padding: 12px; margin-bottom: 12px; border: 1px dashed #e2e8f0;">
                                <p style="font-size: 0.8rem; color: #64748b; margin: 0;"><strong>Catatan:</strong> {{ $withdrawal->notes }}</p>
                            </div>
                        @endif

                        @if($withdrawal->proof_of_transfer)
                            <div style="margin-top: 12px; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                                <a href="{{ asset('storage/' . $withdrawal->proof_of_transfer) }}" target="_blank" style="display: flex; align-items: center; gap: 8px; color: #10b981; font-size: 12px; font-weight: 700; text-decoration: none;">
                                    <i data-feather="image" style="width: 14px; height: 14px;"></i> Lihat Bukti Transfer
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div style="background: white; border-radius: 16px; padding: 48px; text-align: center; border: 2px dashed #e2e8f0;">
                        <i data-feather="clock" style="width: 48px; height: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
                        <h4 style="font-size: 1rem; font-weight: 700; color: #64748b;">Belum ada riwayat penarikan</h4>
                    </div>
                @endforelse

                <div style="margin-top: 16px;">
                    {{ $withdrawals->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Format currency input
    const amountInput = document.getElementById('amount');
    amountInput.addEventListener('keyup', function(e) {
        let value = this.value.replace(/[^0-9]/g, '');
        if (value) {
            this.value = parseInt(value).toLocaleString('id-ID');
        } else {
            this.value = '';
        }
    });

    amountInput.addEventListener('blur', function() {
        if (!this.value) return;
        let value = parseInt(this.value.replace(/[^0-9]/g, ''));
        if (value < 1000) {
            alert('Minimal penarikan adalah Rp 1.000');
            this.value = '1.000';
        }
    });
</script>
@endsection
