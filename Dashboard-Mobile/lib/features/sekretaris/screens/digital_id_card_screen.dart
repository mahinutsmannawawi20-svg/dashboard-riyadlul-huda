import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:qr_flutter/qr_flutter.dart';
import '../models/santri.dart';

class DigitalIdCardScreen extends StatelessWidget {
  final Santri santri;

  const DigitalIdCardScreen({super.key, required this.santri});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF3F4F6),
      appBar: AppBar(
        title: Text('Kartu Syahriah & Identitas',
            style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        foregroundColor: Colors.black,
      ),
      body: Center(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              _buildIdCard(),
              const SizedBox(height: 32),
              ElevatedButton.icon(
                onPressed: () {
                  // Logic to download or share card
                },
                icon: const Icon(Icons.download_outlined),
                label: const Text('Unduh Kartu'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF1B5E20),
                  foregroundColor: Colors.white,
                  padding:
                      const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildIdCard() {
    String formattedVA = santri.virtualAccountNumber ?? '-';
    if (formattedVA.length == 16) {
      formattedVA = formattedVA.replaceAllMapped(
          RegExp(r".{4}"), (match) => "${match.group(0)} ");
    }

    return Container(
      width: double.infinity,
      constraints: const BoxConstraints(maxWidth: 350),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.1),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          // Header Card
          Container(
            padding: const EdgeInsets.all(20),
            decoration: const BoxDecoration(
              color: Color(0xFF1B5E20),
              borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
            ),
            child: Row(
              children: [
                const Icon(Icons.account_balance_wallet,
                    color: Colors.white, size: 32),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'RIYADLUL HUDA',
                        style: GoogleFonts.outfit(
                          color: Colors.white,
                          fontWeight: FontWeight.bold,
                          fontSize: 14,
                        ),
                      ),
                      Text(
                        'KARTU SYAHRIAH & IDENTITAS',
                        style: GoogleFonts.outfit(
                          color: Colors.white70,
                          fontSize: 10,
                          letterSpacing: 1,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          Padding(
            padding: const EdgeInsets.all(24.0),
            child: Column(
              children: [
                // Profile Photo
                CircleAvatar(
                  radius: 45,
                  backgroundColor: Colors.grey.shade100,
                  child: santri.fotoPath.isNotEmpty
                      ? ClipRRect(
                          borderRadius: BorderRadius.circular(45),
                          child: Image.network(santri.fotoPath,
                              fit: BoxFit.cover, width: 90, height: 90),
                        )
                      : Icon(Icons.person,
                          size: 45, color: Colors.grey.shade400),
                ),
                const SizedBox(height: 16),

                // Name
                Text(
                  santri.nama.toUpperCase(),
                  textAlign: TextAlign.center,
                  style: GoogleFonts.outfit(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: const Color(0xFF1F2937),
                  ),
                ),
                const SizedBox(height: 2),

                // NIS
                Text(
                  'NIS: ${santri.nis}',
                  style: GoogleFonts.outfit(
                    fontSize: 12,
                    color: Colors.grey.shade600,
                    fontWeight: FontWeight.w500,
                  ),
                ),

                const SizedBox(height: 20),
                // Virtual Account Section
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: const Color(0xFF1B5E20).withOpacity(0.05),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(
                        color: const Color(0xFF1B5E20).withOpacity(0.1)),
                  ),
                  child: Column(
                    children: [
                      Text(
                        'VIRTUAL ACCOUNT (BSI)',
                        style: GoogleFonts.outfit(
                          fontSize: 10,
                          color: const Color(0xFF1B5E20),
                          fontWeight: FontWeight.bold,
                          letterSpacing: 1,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        formattedVA,
                        style: GoogleFonts.outfit(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF1B5E20),
                        ),
                      ),
                    ],
                  ),
                ),

                const Padding(
                  padding: EdgeInsets.symmetric(vertical: 16),
                  child: Divider(),
                ),

                // Details Row
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceAround,
                  children: [
                    _buildDetail('KELAS', santri.kelas),
                    _buildDetail('KAMAR', santri.kamar),
                  ],
                ),

                const SizedBox(height: 20),

                // QR Code
                QrImageView(
                  data: 'santri:${santri.nis}',
                  version: QrVersions.auto,
                  size: 110.0,
                  eyeStyle: const QrEyeStyle(
                    eyeShape: QrEyeShape.square,
                    color: Color(0xFF1B5E20),
                  ),
                  dataModuleStyle: const QrDataModuleStyle(
                    dataModuleShape: QrDataModuleShape.square,
                    color: Color(0xFF1B5E20),
                  ),
                ),

                const SizedBox(height: 8),
                Text(
                  'SCAN UNTUK VERIFIKASI',
                  style: GoogleFonts.outfit(
                    fontSize: 9,
                    color: Colors.grey.shade400,
                    fontWeight: FontWeight.bold,
                    letterSpacing: 1,
                  ),
                ),
              ],
            ),
          ),

          // Footer Watermark
          Container(
            padding: const EdgeInsets.symmetric(vertical: 12),
            width: double.infinity,
            decoration: BoxDecoration(
              color: Colors.grey.shade50,
              borderRadius:
                  const BorderRadius.vertical(bottom: Radius.circular(24)),
            ),
            child: Center(
              child: Text(
                'Aplikasi Management Riyadlul Huda',
                style: GoogleFonts.outfit(
                  fontSize: 10,
                  color: Colors.grey.shade400,
                  fontStyle: FontStyle.italic,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDetail(String label, String value) {
    return Column(
      children: [
        Text(
          label,
          style: GoogleFonts.outfit(
            fontSize: 10,
            color: Colors.grey.shade500,
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          value,
          style: GoogleFonts.outfit(
            fontSize: 13,
            fontWeight: FontWeight.bold,
            color: const Color(0xFF374151),
          ),
        ),
      ],
    );
  }
}
