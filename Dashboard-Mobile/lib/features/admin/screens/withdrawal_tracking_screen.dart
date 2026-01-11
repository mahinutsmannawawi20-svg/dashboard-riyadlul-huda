import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../../core/services/api_service.dart';
import 'package:intl/intl.dart';
import 'package:image_picker/image_picker.dart';
import 'package:dio/dio.dart' as dio;
import 'dart:io';

class WithdrawalTrackingScreen extends StatefulWidget {
  const WithdrawalTrackingScreen({super.key});

  @override
  State<WithdrawalTrackingScreen> createState() =>
      _WithdrawalTrackingScreenState();
}

class _WithdrawalTrackingScreenState extends State<WithdrawalTrackingScreen> {
  List<dynamic> _withdrawals = [];
  bool _isLoading = false;
  final ImagePicker _picker = ImagePicker();

  final NumberFormat _currencyFormat = NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  );

  @override
  void initState() {
    super.initState();
    _fetchWithdrawals();
  }

  Future<void> _fetchWithdrawals() async {
    setState(() => _isLoading = true);
    try {
      final response = await ApiService().get('admin/withdrawals');
      setState(() {
        _withdrawals = response.data['data'] ?? [];
        _isLoading = false;
      });
    } catch (e) {
      debugPrint('Error fetching admin withdrawals: $e');
      setState(() => _isLoading = false);
    }
  }

  Future<void> _approveWithdrawal(int id, String status,
      {String? notes, XFile? image}) async {
    setState(() => _isLoading = true);
    try {
      dynamic data;
      if (status == 'approved' && image != null) {
        data = dio.FormData.fromMap({
          'status': status,
          'notes': notes ?? 'Disetujui oleh Admin',
          'proof_of_transfer': await dio.MultipartFile.fromFile(
            image.path,
            filename: image.name,
          ),
        });
      } else {
        data = {
          'status': status,
          'notes': notes ??
              (status == 'approved'
                  ? 'Disetujui oleh Admin'
                  : 'Ditolak oleh Admin'),
        };
      }

      final response = await ApiService().post(
        'admin/withdrawals/$id/approve',
        data: data,
      );

      if (response.data['status'] == 'success') {
        _fetchWithdrawals();
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
                content: Text(
                    'Penarikan berhasil ${status == 'approved' ? 'disetujui' : 'ditolak'}')),
          );
        }
      }
    } catch (e) {
      debugPrint('Error approving withdrawal: $e');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Gagal memproses penarikan')),
        );
      }
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Tracking Pencairan',
            style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.white,
        foregroundColor: Colors.black,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _fetchWithdrawals,
          ),
        ],
      ),
      body: _isLoading && _withdrawals.isEmpty
          ? const Center(child: CircularProgressIndicator())
          : _withdrawals.isEmpty
              ? Center(
                  child: Text('Tidak ada data pengajuan',
                      style: GoogleFonts.outfit(color: Colors.grey)))
              : ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: _withdrawals.length,
                  itemBuilder: (context, index) {
                    final w = _withdrawals[index];
                    return _buildWithdrawalCard(w);
                  },
                ),
    );
  }

  Widget _buildWithdrawalCard(dynamic w) {
    final statusColor = _getStatusColor(w['status']);
    final isPending = w['status'] == 'pending';

    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      w['user']['name'] ?? 'Bendahara',
                      style: GoogleFonts.outfit(
                          fontWeight: FontWeight.bold, fontSize: 16),
                    ),
                    Text(
                      DateFormat('dd MMM yyyy, HH:mm')
                          .format(DateTime.parse(w['created_at'])),
                      style:
                          GoogleFonts.outfit(fontSize: 12, color: Colors.grey),
                    ),
                  ],
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
            const Divider(height: 24),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Nominal:',
                  style: GoogleFonts.outfit(color: Colors.grey.shade600),
                ),
                Text(
                  _currencyFormat
                      .format(double.tryParse(w['amount'].toString()) ?? 0),
                  style: GoogleFonts.outfit(
                      fontWeight: FontWeight.bold,
                      fontSize: 18,
                      color: const Color(0xFF1B5E20)),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              'Tujuan: ${w['bank_account']['bank_name']}',
              style: GoogleFonts.outfit(fontWeight: FontWeight.w500),
            ),
            Text(
              'No. Rek: ${w['bank_account']['account_number']}',
              style: GoogleFonts.outfit(fontSize: 13),
            ),
            Text(
              'Atas Nama: ${w['bank_account']['account_holder']}',
              style: GoogleFonts.outfit(fontSize: 13),
            ),
            if (w['notes'] != null && w['notes'].isNotEmpty) ...[
              const SizedBox(height: 12),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.grey.shade50,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  'Catatan: ${w['notes']}',
                  style: GoogleFonts.outfit(
                      fontSize: 12, fontStyle: FontStyle.italic),
                ),
              ),
            ],
            if (isPending) ...[
              const SizedBox(height: 20),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: _isLoading
                          ? null
                          : () => _showActionDialog(w, 'rejected'),
                      style: OutlinedButton.styleFrom(
                        foregroundColor: Colors.red,
                        side: const BorderSide(color: Colors.red),
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12)),
                      ),
                      child: const Text('Tolak'),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: _isLoading
                          ? null
                          : () => _showActionDialog(w, 'approved'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF1B5E20),
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12)),
                      ),
                      child: const Text('Setujui',
                          style: TextStyle(color: Colors.white)),
                    ),
                  ),
                ],
              ),
            ],
          ],
        ),
      ),
    );
  }

  void _showActionDialog(dynamic w, String status) {
    final notesController = TextEditingController();
    XFile? pickedImage;

    showDialog(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setModalState) => AlertDialog(
          title: Text(
              status == 'approved' ? 'Setujui Penarikan' : 'Tolak Penarikan'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                    'Apakah Anda yakin ingin ${status == 'approved' ? 'menyetujui' : 'menolak'} penarikan ini?'),
                const SizedBox(height: 16),
                if (status == 'approved') ...[
                  const Text('Bukti Transfer (Wajib)',
                      style: TextStyle(fontWeight: FontWeight.bold)),
                  const SizedBox(height: 8),
                  GestureDetector(
                    onTap: () async {
                      final image = await _picker.pickImage(
                        source: ImageSource.gallery,
                        imageQuality: 50,
                      );
                      if (image != null) {
                        setModalState(() => pickedImage = image);
                      }
                    },
                    child: Container(
                      height: 150,
                      width: double.infinity,
                      decoration: BoxDecoration(
                        border: Border.all(color: Colors.grey.shade300),
                        borderRadius: BorderRadius.circular(12),
                        color: Colors.grey.shade50,
                      ),
                      child: pickedImage != null
                          ? ClipRRect(
                              borderRadius: BorderRadius.circular(11),
                              child: Image.file(File(pickedImage!.path),
                                  fit: BoxFit.cover),
                            )
                          : Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(Icons.add_a_photo,
                                    color: Colors.grey.shade400, size: 40),
                                const SizedBox(height: 8),
                                Text('Pilih Bukti Transfer',
                                    style:
                                        TextStyle(color: Colors.grey.shade500)),
                              ],
                            ),
                    ),
                  ),
                  const SizedBox(height: 16),
                ],
                TextField(
                  controller: notesController,
                  decoration: InputDecoration(
                    labelText: 'Catatan untuk Bendahara',
                    border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12)),
                  ),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(
                onPressed: () => Navigator.pop(context),
                child: const Text('Batal')),
            TextButton(
              onPressed: () {
                if (status == 'approved' && pickedImage == null) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                        content: Text('Silakan pilih bukti transfer dulu')),
                  );
                  return;
                }
                Navigator.pop(context);
                _approveWithdrawal(w['id'], status,
                    notes: notesController.text, image: pickedImage);
              },
              child: Text(status == 'approved' ? 'Ya, Setujui' : 'Ya, Tolak',
                  style: TextStyle(
                      color: status == 'approved' ? Colors.green : Colors.red)),
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
