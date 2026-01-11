import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../auth/screens/login_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  String _userRole = '';
  String _userName = '';

  @override
  void initState() {
    super.initState();
    _loadUserInfo();
  }

  Future<void> _loadUserInfo() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      _userRole = prefs.getString('user_role') ?? 'Staff';
      _userName = prefs.getString('user_name') ?? 'User';
    });
  }

  List<Map<String, dynamic>> _getMenuItems() {
    switch (_userRole.toLowerCase()) {
      case 'admin':
        return [
          {'icon': Icons.people, 'label': 'Data Santri', 'color': Colors.blue},
          {
            'icon': Icons.assignment_ind,
            'label': 'Perizinan',
            'color': Colors.red
          },
          {
            'icon': Icons.attach_money,
            'label': 'Syahriah',
            'color': Colors.green
          },
          {'icon': Icons.menu_book, 'label': 'Hafalan', 'color': Colors.orange},
          {
            'icon': Icons.assignment_outlined,
            'label': 'Laporan',
            'color': Colors.purple
          },
          {'icon': Icons.settings, 'label': 'Pengaturan', 'color': Colors.grey},
        ];
      case 'sekretaris':
        return [
          {'icon': Icons.people, 'label': 'Data Santri', 'color': Colors.blue},
          {
            'icon': Icons.assignment_ind,
            'label': 'Perizinan',
            'color': Colors.red
          },
          {'icon': Icons.fact_check, 'label': 'Absensi', 'color': Colors.green},
          {
            'icon': Icons.mail_outline,
            'label': 'Surat-surat',
            'color': Colors.purple
          },
        ];
      case 'bendahara':
        return [
          {
            'icon': Icons.payments_outlined,
            'label': 'Cek Tunggakan',
            'color': Colors.orange
          },
          {
            'icon': Icons.attach_money,
            'label': 'Input Syahriah',
            'color': Colors.green
          },
          {
            'icon': Icons.analytics_outlined,
            'label': 'Laporan Keuangan',
            'color': Colors.blue
          },
          {
            'icon': Icons.account_balance_wallet,
            'label': 'Pengeluaran',
            'color': Colors.red
          },
        ];
      case 'pendidikan':
        return [
          {'icon': Icons.menu_book, 'label': 'Hafalan', 'color': Colors.orange},
          {
            'icon': Icons.event_note,
            'label': 'Jadwal Pelajaran',
            'color': Colors.purple
          },
          {'icon': Icons.school, 'label': 'Data Asatidz', 'color': Colors.teal},
          {'icon': Icons.grade, 'label': 'Input Nilai', 'color': Colors.blue},
        ];
      default:
        return [
          {
            'icon': Icons.help_outline,
            'label': 'Informasi',
            'color': Colors.grey
          },
          {
            'icon': Icons.chat_bubble_outline,
            'label': 'Bantuan',
            'color': Colors.blue
          },
        ];
    }
  }

  @override
  Widget build(BuildContext context) {
    final menuItems = _getMenuItems();

    return Scaffold(
      appBar: AppBar(
        title: Text('Dashboard',
            style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
        actions: [
          IconButton(
            icon: const Icon(Icons.notifications_outlined),
            onPressed: () {},
          ),
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () async {
              final prefs = await SharedPreferences.getInstance();
              await prefs.clear(); // Clear all data on logout

              if (context.mounted) {
                Navigator.pushAndRemoveUntil(
                  context,
                  MaterialPageRoute(builder: (context) => const LoginScreen()),
                  (route) => false,
                );
              }
            },
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Welcome Card
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF1B5E20), Color(0xFF4CAF50)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: Colors.green.withOpacity(0.3),
                    blurRadius: 10,
                    offset: const Offset(0, 5),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Selamat Datang,',
                    style: GoogleFonts.outfit(
                      color: Colors.white70,
                      fontSize: 14,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    _userName.isEmpty ? 'Memuat...' : _userName,
                    style: GoogleFonts.outfit(
                      color: Colors.white,
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      _userRole.toUpperCase(),
                      style: GoogleFonts.outfit(
                        color: Colors.white,
                        fontSize: 12,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Menu Grid
            Text(
              'Menu Utama',
              style: GoogleFonts.outfit(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: const Color(0xFF1F2937),
              ),
            ),
            const SizedBox(height: 16),
            GridView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                crossAxisSpacing: 16,
                mainAxisSpacing: 16,
                mainAxisExtent: 110,
              ),
              itemCount: menuItems.length,
              itemBuilder: (context, index) {
                final item = menuItems[index];
                return _buildMenuCard(
                  icon: item['icon'] as IconData,
                  label: item['label'] as String,
                  color: item['color'] as Color,
                  onTap: () {
                    // Navigate to specific feature screen
                  },
                );
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMenuCard({
    required IconData icon,
    required String label,
    required Color color,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.grey.withOpacity(0.05),
              blurRadius: 10,
              offset: const Offset(0, 2),
            ),
          ],
          border: Border.all(color: Colors.grey.shade100),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 28),
            ),
            const SizedBox(height: 12),
            Text(
              label,
              style: GoogleFonts.outfit(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: const Color(0xFF374151),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
