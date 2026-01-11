import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../../core/services/api_service.dart';
import 'package:intl/intl.dart';
import 'add_bank_account_screen.dart';

class WithdrawalScreen extends StatefulWidget {
  const WithdrawalScreen({super.key});

  @override
  State<WithdrawalScreen> createState() => _WithdrawalScreenState();
}

class _WithdrawalScreenState extends State<WithdrawalScreen> {
  final _amountController = TextEditingController();
  final _notesController = TextEditingController();
  List<dynamic> _bankAccounts = [];
  List<dynamic> _withdrawals = [];
  bool _isLoading = false;
  int? _selectedBankAccountId;

  final NumberFormat _currencyFormat = NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  );

  @override
  void initState() {
    super.initState();
    _fetchData();
  }

  Future<void> _fetchData() async {
    setState(() => _isLoading = true);
    try {
      final accountsRes = await ApiService().get('bendahara/bank-accounts');
      final withdrawalsRes = await ApiService().get('bendahara/withdrawals');

      setState(() {
        _bankAccounts = accountsRes.data['data'] ?? [];
        _withdrawals = withdrawalsRes.data['data'] ?? [];
        _isLoading = false;
      });
    } catch (e) {
      debugPrint('Error fetching withdrawal data: $e');
      setState(() => _isLoading = false);
    }
  }

  Future<void> _submitRequest() async {
    if (_selectedBankAccountId == null || _amountController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pilih rekening dan isi nominal')),
      );
      return;
    }

    setState(() => _isLoading = true);
    try {
      final response = await ApiService().post(
        'bendahara/withdrawals',
        data: {
          'bank_account_id': _selectedBankAccountId,
          'amount': _amountController.text,
          'notes': _notesController.text,
        },
      );

      if (response.data['status'] == 'success') {
        _amountController.clear();
        _notesController.clear();
        _selectedBankAccountId = null;
        _fetchData();
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Pengajuan berhasil dikirim')),
          );
        }
      }
    } catch (e) {
      debugPrint('Error submitting withdrawal: $e');
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Penarikan Dana',
            style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.white,
        foregroundColor: Colors.black,
        elevation: 0,
      ),
      body: _isLoading && _withdrawals.isEmpty
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _fetchData,
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildRequestForm(),
                    const SizedBox(height: 24),
                    Text(
                      'Riwayat Penarikan',
                      style: GoogleFonts.outfit(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 12),
                    _buildWithdrawalList(),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildRequestForm() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.1),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Ajukan Penarikan Baru',
                style: GoogleFonts.outfit(
                    fontWeight: FontWeight.bold, fontSize: 16),
              ),
              TextButton.icon(
                onPressed: () async {
                  final result = await Navigator.push(
                    context,
                    MaterialPageRoute(
                        builder: (context) => const AddBankAccountScreen()),
                  );
                  if (result == true) _fetchData();
                },
                icon: const Icon(Icons.add, size: 18),
                label: const Text('Rekening'),
              ),
            ],
          ),
          const SizedBox(height: 16),
          DropdownButtonFormField<int>(
            decoration: InputDecoration(
              labelText: 'Pilih Rekening Tujuan',
              border:
                  OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
              prefixIcon: const Icon(Icons.account_balance),
            ),
            value: _selectedBankAccountId,
            items: _bankAccounts.map((acc) {
              return DropdownMenuItem<int>(
                value: acc['id'],
                child: Text('${acc['bank_name']} - ${acc['account_number']}'),
              );
            }).toList(),
            onChanged: (val) => setState(() => _selectedBankAccountId = val),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: _amountController,
            keyboardType: TextInputType.number,
            decoration: InputDecoration(
              labelText: 'Nominal Penarikan',
              border:
                  OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
              prefixIcon: const Icon(Icons.money),
            ),
          ),
          const SizedBox(height: 16),
          TextField(
            controller: _notesController,
            decoration: InputDecoration(
              labelText: 'Catatan (Opsional)',
              border:
                  OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
              prefixIcon: const Icon(Icons.note_alt_outlined),
            ),
          ),
          const SizedBox(height: 20),
          SizedBox(
            width: double.infinity,
            height: 50,
            child: ElevatedButton(
              onPressed: _isLoading ? null : _submitRequest,
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF1B5E20),
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12)),
              ),
              child: _isLoading
                  ? const CircularProgressIndicator(color: Colors.white)
                  : Text('Kirim Pengajuan',
                      style: GoogleFonts.outfit(
                          fontWeight: FontWeight.bold, color: Colors.white)),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildWithdrawalList() {
    if (_withdrawals.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 20),
          child: Text('Belum ada riwayat penarikan',
              style: GoogleFonts.outfit(color: Colors.grey)),
        ),
      );
    }

    return ListView.separated(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: _withdrawals.length,
      separatorBuilder: (context, index) => const SizedBox(height: 12),
      itemBuilder: (context, index) {
        final w = _withdrawals[index];
        final statusColor = _getStatusColor(w['status']);

        return Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: Colors.grey.shade200),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    _currencyFormat
                        .format(double.tryParse(w['amount'].toString()) ?? 0),
                    style: GoogleFonts.outfit(
                        fontWeight: FontWeight.bold, fontSize: 16),
                  ),
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: statusColor.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      w['status'].toString().toUpperCase(),
                      style: GoogleFonts.outfit(
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                        color: statusColor,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              Text(
                'Tujuan: ${w['bank_account']['bank_name']} - ${w['bank_account']['account_number']}',
                style: GoogleFonts.outfit(
                    fontSize: 12, color: Colors.grey.shade700),
              ),
              const SizedBox(height: 4),
              Text(
                'Tanggal: ${DateFormat('dd MMM yyyy HH:mm').format(DateTime.parse(w['created_at']))}',
                style: GoogleFonts.outfit(
                    fontSize: 10, color: Colors.grey.shade500),
              ),
              if (w['notes'] != null && w['notes'].isNotEmpty) ...[
                const SizedBox(height: 8),
                Text(
                  'Catatan: ${w['notes']}',
                  style: GoogleFonts.outfit(
                      fontSize: 11, fontStyle: FontStyle.italic),
                ),
              ],
              if (w['status'] == 'approved' &&
                  w['proof_of_transfer'] != null) ...[
                const SizedBox(height: 12),
                TextButton.icon(
                  onPressed: () => _showProofImage(w['proof_of_transfer']),
                  icon: const Icon(Icons.receipt_long, size: 16),
                  label: const Text('Lihat Bukti Transfer'),
                ),
              ],
            ],
          ),
        );
      },
    );
  }

  void _showProofImage(String imageUrl) {
    showDialog(
      context: context,
      builder: (context) => Dialog(
        backgroundColor: Colors.transparent,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                IconButton(
                  icon: const Icon(Icons.close, color: Colors.white, size: 30),
                  onPressed: () => Navigator.pop(context),
                ),
              ],
            ),
            ClipRRect(
              borderRadius: BorderRadius.circular(12),
              child: InteractiveViewer(
                child: Image.network(
                  'https://dashboard.riyadlulhuda.my.id/storage/$imageUrl',
                  loadingBuilder: (context, child, loadingProgress) {
                    if (loadingProgress == null) return child;
                    return Container(
                      height: 300,
                      width: double.infinity,
                      color: Colors.white,
                      child: const Center(child: CircularProgressIndicator()),
                    );
                  },
                  errorBuilder: (context, error, stackTrace) => Container(
                    height: 300,
                    width: double.infinity,
                    color: Colors.white,
                    child: const Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.error_outline, color: Colors.red, size: 40),
                        SizedBox(height: 8),
                        Text('Gagal memuat gambar'),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'approved':
        return Colors.green;
      case 'rejected':
        return Colors.red;
      default:
        return Colors.orange;
    }
  }
}
