@extends('layouts.app')

@section('title', 'Kelola Rekening Bank')
@section('page-title', 'Kelola Rekening Bank')

@section('sidebar-menu')
    @include('bendahara.partials.sidebar-menu')
@endsection

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <!-- Header Action -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: #1e2937; margin: 0;">Daftar Rekening</h2>
            <p style="color: #64748b; margin-top: 4px;">Kelola rekening bank untuk tujuan pencairan dana.</p>
        </div>
        <button onclick="document.getElementById('modalAdd').style.display='flex'" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 10px 20px; border-radius: 10px; font-weight: 700; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">
            <i data-feather="plus" style="width: 18px; height: 18px;"></i> Tambah Rekening
        </button>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div style="background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; padding: 16px; border-radius: 8px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <i data-feather="check-circle" style="width: 20px; height: 20px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Accounts Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        @forelse($accounts as $account)
            <div style="background: white; border-radius: 16px; padding: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); position: relative; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 15px -3px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.05)';">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div style="width: 48px; height: 48px; background: #f8fafc; border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0;">
                        <i data-feather="credit-card" style="width: 24px; height: 24px; color: #64748b;"></i>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button onclick="openEditModal({{ json_encode($account) }})" style="background: #f1f5f9; color: #475569; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                        </button>
                        <form action="{{ route('bendahara.bank-accounts.destroy', $account->id) }}" method="POST" onsubmit="return confirm('Hapus rekening ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: #fef2f2; color: #ef4444; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
                <div style="margin-bottom: 4px;">
                    <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.025em;">{{ $account->bank_name }}</span>
                    <h3 style="font-size: 1.125rem; font-weight: 800; color: #1e2937; margin: 4px 0;">{{ $account->account_number }}</h3>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <p style="font-size: 0.875rem; color: #64748b; font-weight: 500; margin: 0;">{{ $account->account_holder }}</p>
                    @if($account->is_active)
                        <span style="background: #ecfdf5; color: #059669; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; text-transform: uppercase;">Aktif</span>
                    @else
                        <span style="background: #f1f5f9; color: #64748b; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; text-transform: uppercase;">Nonaktif</span>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; background: white; border-radius: 16px; padding: 48px; text-align: center; border: 2px dashed #e2e8f0;">
                <div style="width: 64px; height: 64px; background: #f8fafc; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <i data-feather="credit-card" style="width: 32px; height: 32px; color: #cbd5e1;"></i>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #64748b; margin: 0;">Belum Ada Rekening</h3>
                <p style="color: #94a3b8; margin-top: 8px; max-width: 300px; margin-left: auto; margin-right: auto;">Tambahkan rekening bank untuk memproses penarikan dana.</p>
                <button onclick="document.getElementById('modalAdd').style.display='flex'" style="margin-top: 24px; background: #f1f5f9; color: #475569; padding: 10px 20px; border-radius: 10px; font-weight: 700; border: none; cursor: pointer;">Tambah Sekarang</button>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Add -->
<div id="modalAdd" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: white; border-radius: 20px; width: 100%; max-width: 450px; padding: 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #1e2937; margin: 0;">Tambah Rekening Bank</h3>
            <button onclick="document.getElementById('modalAdd').style.display='none'" style="background: none; border: none; color: #94a3b8; cursor: pointer;">
                <i data-feather="x"></i>
            </button>
        </div>
        <form action="{{ route('bendahara.bank-accounts.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Nama Bank</label>
                <input type="text" name="bank_name" required placeholder="Contoh: BCA, Mandiri, BRI" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.95rem;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Nomor Rekening</label>
                <input type="text" name="account_number" required placeholder="0000000000" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.95rem;">
            </div>
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Atas Nama</label>
                <input type="text" name="account_holder" required placeholder="Nama lengkap pemilik rekening" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.95rem;">
            </div>
            <button type="submit" style="width: 100%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 14px; border-radius: 12px; font-weight: 700; border: none; cursor: pointer; font-size: 1rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">Simpan Rekening</button>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: white; border-radius: 20px; width: 100%; max-width: 450px; padding: 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #1e2937; margin: 0;">Edit Rekening Bank</h3>
            <button onclick="document.getElementById('modalEdit').style.display='none'" style="background: none; border: none; color: #94a3b8; cursor: pointer;">
                <i data-feather="x"></i>
            </button>
        </div>
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Nama Bank</label>
                <input type="text" name="bank_name" id="edit_bank_name" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Nomor Rekening</label>
                <input type="text" name="account_number" id="edit_account_number" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Atas Nama</label>
                <input type="text" name="account_holder" id="edit_account_holder" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
            </div>
            <div style="margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.875rem; font-weight: 700; color: #475569; cursor: pointer;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1"> Rekening Aktif
                </label>
            </div>
            <button type="submit" style="width: 100%; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; padding: 14px; border-radius: 12px; font-weight: 700; border: none; cursor: pointer; font-size: 1rem;">Perbarui Rekening</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(account) {
        document.getElementById('formEdit').action = `/bendahara/bank-accounts/${account.id}`;
        document.getElementById('edit_bank_name').value = account.bank_name;
        document.getElementById('edit_account_number').value = account.account_number;
        document.getElementById('edit_account_holder').value = account.account_holder;
        document.getElementById('edit_is_active').checked = account.is_active;
        document.getElementById('modalEdit').style.display = 'flex';
    }

    // Close modal on click outside
    window.onclick = function(event) {
        if (event.target.id == 'modalAdd') {
            document.getElementById('modalAdd').style.display = 'none';
        }
        if (event.target.id == 'modalEdit') {
            document.getElementById('modalEdit').style.display = 'none';
        }
    }
</script>
@endsection
