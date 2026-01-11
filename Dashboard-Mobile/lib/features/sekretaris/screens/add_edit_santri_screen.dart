import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../../core/services/api_service.dart';
import '../models/santri.dart';

class AddEditSantriScreen extends StatefulWidget {
  final Santri? santri;
  const AddEditSantriScreen({super.key, this.santri});

  @override
  State<AddEditSantriScreen> createState() => _AddEditSantriScreenState();
}

class _AddEditSantriScreenState extends State<AddEditSantriScreen> {
  final _formKey = GlobalKey<FormState>();
  final ApiService _apiService = ApiService();
  bool _isLoading = false;
  bool _isInitLoading = true;

  // Form controllers
  late TextEditingController _nameController;
  late TextEditingController _nisController;
  late TextEditingController _negaraController;
  late TextEditingController _provinsiController;
  late TextEditingController _kotaController;
  late TextEditingController _kecamatanController;
  late TextEditingController _desaController;
  late TextEditingController _rtRwController;
  late TextEditingController _ortuController;
  late TextEditingController _hpOrtuController;

  // Dropdown values
  String? _selectedGender;
  String? _selectedKelasId;
  String? _selectedAsramaId;
  String? _selectedKobongId;

  // Filter data
  List<dynamic> _kelasOptions = [];
  List<dynamic> _asramaOptions = [];
  List<dynamic> _kobongOptions = [];
  List<dynamic> _filteredKobong = [];

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.santri?.nama);
    _nisController = TextEditingController(text: widget.santri?.nis);
    _negaraController =
        TextEditingController(text: widget.santri?.negara ?? 'Indonesia');
    _provinsiController = TextEditingController(text: widget.santri?.provinsi);
    _kotaController = TextEditingController(text: widget.santri?.kotaKabupaten);
    _kecamatanController =
        TextEditingController(text: widget.santri?.kecamatan);
    _desaController = TextEditingController(text: widget.santri?.desaKampung);
    _rtRwController = TextEditingController(text: widget.santri?.rtRw);
    _ortuController = TextEditingController(text: widget.santri?.namaOrtuWali);
    _hpOrtuController =
        TextEditingController(text: widget.santri?.noHpOrtuWali);

    _selectedGender = widget.santri?.gender;
    _selectedKelasId = widget.santri?.kelasId?.toString();
    _selectedAsramaId = widget.santri?.asramaId?.toString();
    _selectedKobongId = widget.santri?.kobongId?.toString();

    _fetchOptions();
  }

  Future<void> _fetchOptions() async {
    try {
      final response = await _apiService.get('sekretaris/get-filters');
      if (response.data['status'] == 'success') {
        setState(() {
          _kelasOptions = response.data['data']['kelas'];
          _asramaOptions = response.data['data']['asrama'];
          _kobongOptions = response.data['data']['kobong'];
          _isInitLoading = false;
          _filterKobong();
        });
      }
    } catch (e) {
      debugPrint('Error fetching options: $e');
      setState(() => _isInitLoading = false);
    }
  }

  void _filterKobong() {
    if (_selectedAsramaId == null) {
      _filteredKobong = [];
    } else {
      _filteredKobong = _kobongOptions
          .where((k) => k['asrama_id'].toString() == _selectedAsramaId)
          .toList();
    }
    // Reset kobong if not in filtered list
    if (_selectedKobongId != null &&
        !_filteredKobong.any((k) => k['id'].toString() == _selectedKobongId)) {
      _selectedKobongId = null;
    }
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);
    try {
      final data = {
        'nama_santri': _nameController.text,
        'nis': _nisController.text,
        'negara': _negaraController.text,
        'provinsi': _provinsiController.text,
        'kota_kabupaten': _kotaController.text,
        'kecamatan': _kecamatanController.text,
        'desa_kampung': _desaController.text,
        'rt_rw': _rtRwController.text,
        'nama_ortu_wali': _ortuController.text,
        'no_hp_ortu_wali': _hpOrtuController.text,
        'asrama_id': _selectedAsramaId,
        'kobong_id': _selectedKobongId,
        'kelas_id': _selectedKelasId,
        'gender': _selectedGender,
      };

      final response = widget.santri == null
          ? await _apiService.post('sekretaris/santri', data: data)
          : await _apiService.put('sekretaris/santri/${widget.santri!.id}',
              data: data);

      if (response.data['status'] == 'success') {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
                content: Text(widget.santri == null
                    ? 'Santri berhasil ditambah'
                    : 'Data berhasil diupdate')),
          );
          Navigator.pop(context, true);
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e')),
        );
      }
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isInitLoading) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }

    return Scaffold(
      appBar: AppBar(
        title: Text(widget.santri == null ? 'Tambah Santri' : 'Edit Santri',
            style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildSectionTitle('Informasi Pribadi'),
              _buildTextField('Nama Lengkap', _nameController, Icons.person),
              _buildTextField('NIS', _nisController, Icons.badge),
              _buildDropdown(
                  'Gender',
                  _selectedGender,
                  [
                    const DropdownMenuItem(
                        value: 'putra', child: Text('Putra')),
                    const DropdownMenuItem(
                        value: 'putri', child: Text('Putri')),
                  ],
                  (v) => setState(() => _selectedGender = v)),
              const SizedBox(height: 16),
              _buildSectionTitle('Alamat'),
              _buildTextField('Provinsi', _provinsiController, Icons.map),
              _buildTextField(
                  'Kota/Kabupaten', _kotaController, Icons.location_city),
              _buildTextField(
                  'Kecamatan', _kecamatanController, Icons.location_on),
              _buildTextField('Desa/Kampung', _desaController, Icons.home),
              _buildTextField('RT/RW', _rtRwController, Icons.signpost),
              const SizedBox(height: 16),
              _buildSectionTitle('Orang Tua / Wali'),
              _buildTextField(
                  'Nama Ortu/Wali', _ortuController, Icons.family_restroom),
              _buildTextField(
                  'No. HP Ortu/Wali', _hpOrtuController, Icons.phone),
              const SizedBox(height: 16),
              _buildSectionTitle('Penempatan'),
              _buildDropdown(
                  'Kelas',
                  _selectedKelasId,
                  _kelasOptions
                      .map((k) => DropdownMenuItem(
                          value: k['id'].toString(),
                          child: Text(k['nama_kelas'])))
                      .toList(),
                  (v) => setState(() => _selectedKelasId = v)),
              _buildDropdown(
                  'Asrama',
                  _selectedAsramaId,
                  _asramaOptions
                      .map((a) => DropdownMenuItem(
                          value: a['id'].toString(),
                          child: Text(a['nama_asrama'])))
                      .toList(), (v) {
                setState(() {
                  _selectedAsramaId = v;
                  _filterKobong();
                });
              }),
              _buildDropdown(
                  'Kobong/Kamar',
                  _selectedKobongId,
                  _filteredKobong
                      .map((k) => DropdownMenuItem(
                          value: k['id'].toString(),
                          child: Text('Kobong ${k['nomor_kobong']}')))
                      .toList(),
                  (v) => setState(() => _selectedKobongId = v)),
              const SizedBox(height: 32),
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _submit,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF1B5E20),
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12)),
                  ),
                  child: _isLoading
                      ? const CircularProgressIndicator(color: Colors.white)
                      : Text('Simpan Data',
                          style: GoogleFonts.outfit(
                              color: Colors.white,
                              fontWeight: FontWeight.bold)),
                ),
              ),
              const SizedBox(height: 32),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12, top: 8),
      child: Text(title,
          style: GoogleFonts.outfit(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: const Color(0xFF1B5E20))),
    );
  }

  Widget _buildTextField(
      String label, TextEditingController controller, IconData icon) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: TextFormField(
        controller: controller,
        decoration: InputDecoration(
          labelText: label,
          prefixIcon: Icon(icon, size: 20),
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        ),
        validator: (v) => v!.isEmpty ? '$label tidak boleh kosong' : null,
      ),
    );
  }

  Widget _buildDropdown(String label, String? value,
      List<DropdownMenuItem<String>> items, ValueChanged<String?> onChanged) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: DropdownButtonFormField<String>(
        value: value,
        decoration: InputDecoration(
          labelText: label,
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        ),
        items: items,
        onChanged: onChanged,
        validator: (v) => v == null ? 'Pilih $label' : null,
      ),
    );
  }
}
