import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../../core/services/api_service.dart';
import '../models/santri.dart';
import 'digital_id_card_screen.dart';
import 'add_perizinan_screen.dart';
import 'add_edit_santri_screen.dart';

class DataSantriScreen extends StatefulWidget {
  const DataSantriScreen({super.key});

  @override
  State<DataSantriScreen> createState() => _DataSantriScreenState();
}

class _DataSantriScreenState extends State<DataSantriScreen> {
  final ApiService _apiService = ApiService();
  List<Santri> _santriList = [];
  bool _isLoading = true;
  String _searchQuery = '';

  // Filter variables
  List<dynamic> _kelasFilters = [];
  String? _selectedKelas;
  String? _selectedGender;

  @override
  void initState() {
    super.initState();
    _fetchFilters();
    _fetchSantri();
  }

  Future<void> _fetchFilters() async {
    try {
      final response = await _apiService.get('sekretaris/get-filters');
      if (response.data['status'] == 'success') {
        setState(() {
          _kelasFilters = response.data['data']['kelas'];
        });
      }
    } catch (e) {
      debugPrint('Error fetching filters: $e');
    }
  }

  Future<void> _fetchSantri() async {
    setState(() => _isLoading = true);
    try {
      final response =
          await _apiService.get('sekretaris/santri', queryParameters: {
        if (_searchQuery.isNotEmpty) 'search': _searchQuery,
        if (_selectedKelas != null) 'kelas_id': _selectedKelas,
        if (_selectedGender != null) 'gender': _selectedGender,
      });

      if (response.data['status'] == 'success') {
        final List data = response.data['data'];
        setState(() {
          _santriList = data.map((e) => Santri.fromJson(e)).toList();
          _isLoading = false;
        });
      }
    } catch (e) {
      debugPrint('Error fetching santri: $e');
      setState(() => _isLoading = false);
    }
  }

  Future<void> _deactivateSantri(int id) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Konfirmasi',
            style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
        content: Text('Apakah Anda yakin ingin menonaktifkan santri ini?',
            style: GoogleFonts.outfit()),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(context, false),
              child: const Text('Batal')),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Ya, Nonaktifkan',
                style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );

    if (confirm == true) {
      try {
        final response = await _apiService.delete('sekretaris/santri/$id');
        if (response.data['status'] == 'success') {
          if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(content: Text('Santri berhasil dinonaktifkan')),
            );
            _fetchSantri();
          }
        }
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('Gagal: $e')),
          );
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Data Santri',
            style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              children: [
                TextField(
                  decoration: InputDecoration(
                    hintText: 'Cari nama atau NIS...',
                    prefixIcon: const Icon(Icons.search),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                    filled: true,
                    fillColor: Colors.white,
                  ),
                  onChanged: (value) {
                    _searchQuery = value;
                    _fetchSantri();
                  },
                ),
                const SizedBox(height: 12),
                SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: [
                      _buildFilterDropdown(
                        hint: 'Semua Kelas',
                        value: _selectedKelas,
                        items: _kelasFilters
                            .map((k) => DropdownMenuItem(
                                value: k['id'].toString(),
                                child: Text(k['nama_kelas'])))
                            .toList(),
                        onChanged: (v) {
                          setState(() => _selectedKelas = v);
                          _fetchSantri();
                        },
                      ),
                      const SizedBox(width: 8),
                      _buildFilterDropdown(
                        hint: 'Semua Gender',
                        value: _selectedGender,
                        items: const [
                          DropdownMenuItem(
                              value: 'putra', child: Text('Putra')),
                          DropdownMenuItem(
                              value: 'putri', child: Text('Putri')),
                        ],
                        onChanged: (v) {
                          setState(() => _selectedGender = v);
                          _fetchSantri();
                        },
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator())
                : _santriList.isEmpty
                    ? const Center(child: Text('Data santri tidak ditemukan'))
                    : RefreshIndicator(
                        onRefresh: _fetchSantri,
                        child: ListView.builder(
                          itemCount: _santriList.length,
                          padding: const EdgeInsets.symmetric(horizontal: 16),
                          itemBuilder: (context, index) {
                            final santri = _santriList[index];
                            return _buildSantriCard(santri);
                          },
                        ),
                      ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () async {
          final result = await Navigator.push(
            context,
            MaterialPageRoute(
                builder: (context) => const AddEditSantriScreen()),
          );
          if (result == true) _fetchSantri();
        },
        backgroundColor: const Color(0xFF1B5E20),
        child: const Icon(Icons.add, color: Colors.white),
      ),
    );
  }

  Widget _buildFilterDropdown({
    required String hint,
    required String? value,
    required List<DropdownMenuItem<String>> items,
    required ValueChanged<String?> onChanged,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade300),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<String>(
          value: value,
          hint: Text(hint, style: GoogleFonts.outfit(fontSize: 12)),
          items: [
            DropdownMenuItem(
                value: null,
                child: Text('Hapus Filter',
                    style:
                        GoogleFonts.outfit(fontSize: 12, color: Colors.red))),
            ...items,
          ],
          onChanged: onChanged,
        ),
      ),
    );
  }

  Widget _buildSantriCard(Santri santri) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
        side: BorderSide(color: Colors.grey.shade200),
      ),
      child: Column(
        children: [
          ListTile(
            contentPadding: const EdgeInsets.all(12),
            leading: CircleAvatar(
              backgroundColor: const Color(0xFF1B5E20).withOpacity(0.1),
              child: Text(
                santri.nama[0].toUpperCase(),
                style: GoogleFonts.outfit(
                    color: const Color(0xFF1B5E20),
                    fontWeight: FontWeight.bold),
              ),
            ),
            title: Row(
              children: [
                Expanded(
                  child: Text(
                    santri.nama,
                    style: GoogleFonts.outfit(fontWeight: FontWeight.bold),
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.edit_outlined,
                      size: 20, color: Colors.blue),
                  onPressed: () async {
                    final result = await Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (context) =>
                              AddEditSantriScreen(santri: santri)),
                    );
                    if (result == true) _fetchSantri();
                  },
                ),
                IconButton(
                  icon: const Icon(Icons.person_off_outlined,
                      size: 20, color: Colors.red),
                  onPressed: () => _deactivateSantri(santri.id),
                ),
              ],
            ),
            subtitle: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 4),
                Text('NIS: ${santri.nis}',
                    style: GoogleFonts.outfit(fontSize: 12)),
                Text('Kelas: ${santri.kelas} • Kamar: ${santri.kamar}',
                    style: GoogleFonts.outfit(fontSize: 12)),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                TextButton.icon(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) =>
                            DigitalIdCardScreen(santri: santri),
                      ),
                    );
                  },
                  icon: const Icon(Icons.qr_code_2, size: 18),
                  label:
                      Text('Kartu ID', style: GoogleFonts.outfit(fontSize: 12)),
                ),
                const SizedBox(width: 8),
                ElevatedButton.icon(
                  onPressed: () async {
                    final result = await Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) =>
                            AddPerizinanScreen(santri: santri),
                      ),
                    );
                    if (result == true) {
                      _fetchSantri(); // Refresh
                    }
                  },
                  icon:
                      const Icon(Icons.add_task, size: 18, color: Colors.white),
                  label: Text('Tambah Izin',
                      style: GoogleFonts.outfit(
                          fontSize: 12, color: Colors.white)),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF1B5E20),
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
