class Santri {
  final int id;
  final String nama;
  final String nis;
  final String kelas;
  final String kamar;
  final String fotoPath;
  final String status;
  final String? virtualAccountNumber;

  // Additional fields for Add/Edit
  final String? negara;
  final String? provinsi;
  final String? kotaKabupaten;
  final String? kecamatan;
  final String? desaKampung;
  final String? rtRw;
  final String? namaOrtuWali;
  final String? noHpOrtuWali;
  final int? asramaId;
  final int? kobongId;
  final int? kelasId;
  final String? gender;

  Santri({
    required this.id,
    required this.nama,
    required this.nis,
    required this.kelas,
    required this.kamar,
    this.fotoPath = '',
    this.status = 'Aktif',
    this.virtualAccountNumber,
    this.negara,
    this.provinsi,
    this.kotaKabupaten,
    this.kecamatan,
    this.desaKampung,
    this.rtRw,
    this.namaOrtuWali,
    this.noHpOrtuWali,
    this.asramaId,
    this.kobongId,
    this.kelasId,
    this.gender,
  });

  factory Santri.fromJson(Map<String, dynamic> json) {
    return Santri(
      id: json['id'],
      nama: json['nama'] ?? json['nama_santri'] ?? 'N/A',
      nis: json['nis'] ?? '-',
      kelas: json['kelas'] is Map
          ? json['kelas']['nama_kelas']
          : (json['kelas'] ?? '-'),
      kamar: json['kamar'] ?? '-',
      fotoPath: json['foto_path'] ?? '',
      status: (json['is_active'] == false) ? 'Nonaktif' : 'Aktif',
      virtualAccountNumber: json['virtual_account_number'],
      negara: json['negara'],
      provinsi: json['provinsi'],
      kotaKabupaten: json['kota_kabupaten'],
      kecamatan: json['kecamatan'],
      desaKampung: json['desa_kampung'],
      rtRw: json['rt_rw'],
      namaOrtuWali: json['nama_ortu_wali'],
      noHpOrtuWali: json['no_hp_ortu_wali'],
      asramaId: json['asrama_id'],
      kobongId: json['kobong_id'],
      kelasId: json['kelas_id'],
      gender: json['gender'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'nama_santri': nama,
      'nis': nis,
      'negara': negara,
      'provinsi': provinsi,
      'kota_kabupaten': kotaKabupaten,
      'kecamatan': kecamatan,
      'desa_kampung': desaKampung,
      'rt_rw': rtRw,
      'nama_ortu_wali': namaOrtuWali,
      'no_hp_ortu_wali': noHpOrtuWali,
      'asrama_id': asramaId,
      'kobong_id': kobongId,
      'kelas_id': kelasId,
      'gender': gender,
    };
  }
}
