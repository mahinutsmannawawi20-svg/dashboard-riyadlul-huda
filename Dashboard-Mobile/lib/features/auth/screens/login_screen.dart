import 'dart:ui'; // For ImageFilter
import 'package:flutter/material.dart';
import 'package:dio/dio.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../../core/services/api_service.dart';
import '../../dashboard/screens/dashboard_screen.dart'; // Will be created next

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;
  bool _obscurePassword = true;

  Future<void> _login() async {
    debugPrint("Attempting login...");
    if (!_formKey.currentState!.validate()) {
      debugPrint("Form validation failed");
      return;
    }

    setState(() {
      _isLoading = true;
    });

    try {
      final apiService = ApiService();
      debugPrint(
          "Sending request to ${apiService.client.options.baseUrl}login");

      final response = await apiService.client.post('login', data: {
        'email': _emailController.text,
        'password': _passwordController.text,
      });

      debugPrint("Response status: ${response.statusCode}");
      debugPrint("Response data: ${response.data}");

      if (response.statusCode == 200) {
        final token = response.data['access_token']; // Ensure matches backend
        debugPrint("Token received: $token");

        // Save token & user info
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('token', token);
        await prefs.setString('user_name', response.data['user']['name']);
        await prefs.setString('user_role', response.data['user']['role']);

        apiService.setToken(token);

        if (mounted) {
          debugPrint("Navigating to Dashboard...");
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Login Berhasil!')),
          );

          Navigator.pushReplacement(
            context,
            MaterialPageRoute(builder: (context) => const DashboardScreen()),
          );
        }
      }
    } on DioException catch (e) {
      debugPrint("Login Error: ${e.message}");
      debugPrint("Error Response: ${e.response?.data}");
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(e.response?.data['message'] ??
                'Login Gagal. Cek koneksi atau kredensial.'),
            backgroundColor: Colors.red,
          ),
        );
      }
    } finally {
      if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF1B5E20), // Background dasar hijau
      body: Stack(
        children: [
          // 1. Header Section (Image + Gradient + Content)
          SizedBox(
            height: MediaQuery.of(context).size.height * 0.45,
            child: Stack(
              fit: StackFit.expand,
              children: [
                // A. Background Image (Blurred)
                ImageFiltered(
                  imageFilter: ImageFilter.blur(sigmaX: 3, sigmaY: 3),
                  child: Image.asset(
                    'assets/images/background.jpg',
                    fit: BoxFit.cover,
                  ),
                ),

                // B. Gradient Overlay (Green Transparency)
                Container(
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                      colors: [
                        const Color(0xFF1B5E20).withOpacity(0.85),
                        const Color(0xFF2E7D32).withOpacity(0.75),
                      ],
                    ),
                  ),
                ),

                // C. Logo & Text Content
                Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Container(
                        padding: const EdgeInsets.all(
                            12), // Reduced padding slightly
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.15),
                          shape: BoxShape.circle,
                          border: Border.all(
                              color: Colors.white.withOpacity(0.2), width: 1),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.1),
                              blurRadius: 20,
                              spreadRadius: 5,
                            )
                          ],
                        ),
                        child: Image.asset(
                          'assets/images/logo.png',
                          height: 70,
                          width: 70,
                          filterQuality: FilterQuality.high,
                        ),
                      ),
                      const SizedBox(height: 20),
                      Text(
                        'MANAGEMENT\nRIYADLUL HUDA',
                        textAlign: TextAlign.center,
                        style: GoogleFonts.outfit(
                          fontSize: 22,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                          letterSpacing: 1.2,
                          height: 1.2,
                        ),
                      ),
                      const SizedBox(height: 40), // Space for curve
                    ],
                  ),
                ),
              ],
            ),
          ),

          // 2. White Card Container (Bottom Sheet Style)
          Align(
            alignment: Alignment.bottomCenter,
            child: Container(
              height: MediaQuery.of(context).size.height * 0.60,
              decoration: const BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.only(
                  topLeft: Radius.circular(36),
                  topRight: Radius.circular(36),
                ),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black26,
                    blurRadius: 30,
                    offset: Offset(0, -10),
                  ),
                ],
              ),
              child: Padding(
                padding: const EdgeInsets.fromLTRB(25, 32, 25, 40),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      Text(
                        'Assalamualaikum,',
                        style: GoogleFonts.outfit(
                          fontSize: 28, // Slightly larger
                          fontWeight: FontWeight.w600, // Semi-bold for elegance
                          color: const Color(
                              0xFF1B5E20), // Dark Green for branding
                          letterSpacing: -0.5,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        'Silakan masuk dengan akun anda.',
                        style: GoogleFonts.outfit(
                          fontSize: 15,
                          color:
                              Colors.grey.shade600, // Darker grey for clarity
                          fontWeight: FontWeight.w400,
                        ),
                      ),
                      const SizedBox(height: 32),

                      // Email Field
                      TextFormField(
                        controller: _emailController,
                        style: GoogleFonts.outfit(
                            fontWeight: FontWeight.w500, fontSize: 16),
                        decoration: InputDecoration(
                          hintText: 'Email Address',
                          hintStyle: GoogleFonts.outfit(
                              color: Colors.grey.shade400, fontSize: 14),
                          prefixIcon: Icon(Icons.email_outlined,
                              color: const Color(0xFF1B5E20).withOpacity(0.7)),
                          filled: true,
                          fillColor: Colors.grey.shade50,
                          // Modern Border with slight outline
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: BorderSide(color: Colors.grey.shade200),
                          ),
                          enabledBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: BorderSide(color: Colors.grey.shade200),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: const BorderSide(
                                color: Color(0xFF1B5E20), width: 1.5),
                          ),
                          contentPadding: const EdgeInsets.symmetric(
                              vertical: 20, horizontal: 20),
                        ),
                        validator: (value) {
                          if (value == null || value.isEmpty) {
                            return 'Email wajib diisi';
                          }
                          if (!value.contains('@')) {
                            return 'Format email salah';
                          }
                          return null;
                        },
                      ),
                      const SizedBox(height: 20),

                      // Password Field
                      TextFormField(
                        controller: _passwordController,
                        obscureText: _obscurePassword,
                        style: GoogleFonts.outfit(
                            fontWeight: FontWeight.w500, fontSize: 16),
                        decoration: InputDecoration(
                          hintText: 'Password',
                          hintStyle: GoogleFonts.outfit(
                              color: Colors.grey.shade400, fontSize: 14),
                          prefixIcon: Icon(Icons.lock_outline_rounded,
                              color: const Color(0xFF1B5E20).withOpacity(0.7)),
                          suffixIcon: IconButton(
                            icon: Icon(
                              _obscurePassword
                                  ? Icons.visibility_outlined
                                  : Icons.visibility_off_outlined,
                              color: Colors.grey.shade400,
                            ),
                            onPressed: () => setState(
                                () => _obscurePassword = !_obscurePassword),
                          ),
                          filled: true,
                          fillColor: Colors.grey.shade50,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: BorderSide(color: Colors.grey.shade200),
                          ),
                          enabledBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: BorderSide(color: Colors.grey.shade200),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: const BorderSide(
                                color: Color(0xFF1B5E20), width: 1.5),
                          ),
                          contentPadding: const EdgeInsets.symmetric(
                              vertical: 20, horizontal: 20),
                        ),
                        validator: (value) {
                          if (value?.isEmpty ?? true) {
                            return 'Password Wajib Diisi';
                          }
                          return null;
                        },
                      ),

                      const Spacer(),

                      // Login Button
                      SizedBox(
                        height: 56,
                        child: ElevatedButton(
                          onPressed: _isLoading ? null : _login,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF1B5E20),
                            elevation: 8,
                            shadowColor:
                                const Color(0xFF1B5E20).withOpacity(0.5),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(16),
                            ),
                          ),
                          child: _isLoading
                              ? const SizedBox(
                                  height: 24,
                                  width: 24,
                                  child: CircularProgressIndicator(
                                      color: Colors.white, strokeWidth: 2.5),
                                )
                              : Text(
                                  'Masuk Aplikasi',
                                  style: GoogleFonts.outfit(
                                    fontSize: 16,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.white,
                                    letterSpacing: 1,
                                  ),
                                ),
                        ),
                      ),

                      const SizedBox(height: 24),
                      Center(
                        child: Text(
                          'Dibuat oleh Mahin Utsman Nawawi, S.H',
                          style: GoogleFonts.outfit(
                            fontSize: 11,
                            color: Colors.grey.shade400,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
