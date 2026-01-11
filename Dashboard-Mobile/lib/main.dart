import 'package:device_preview/device_preview.dart';
import 'package:flutter/foundation.dart'; // For kReleaseMode
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'features/auth/screens/login_screen.dart';

void main() {
  runApp(
    DevicePreview(
      enabled: !kReleaseMode, // Enable in debug mode
      builder: (context) => const RiyadlulHudaApp(),
    ),
  );
}

class RiyadlulHudaApp extends StatelessWidget {
  const RiyadlulHudaApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Management Riyadlul Huda',
      debugShowCheckedModeBanner: false,
      locale: DevicePreview.locale(context), // Required for DevicePreview
      builder: DevicePreview.appBuilder, // Required for DevicePreview
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF1B5E20), // Green for Riyadlul Huda
          primary: const Color(0xFF1B5E20),
          secondary: const Color(0xFF4CAF50),
        ),
        useMaterial3: true,
        fontFamily: GoogleFonts.outfit().fontFamily,
        scaffoldBackgroundColor: const Color(0xFFF3F4F6),
        appBarTheme: const AppBarTheme(
          backgroundColor: Colors.white,
          elevation: 0,
          centerTitle: true,
          iconTheme: IconThemeData(color: Color(0xFF1F2937)),
          titleTextStyle: TextStyle(
            color: Color(0xFF1F2937),
            fontSize: 18,
            fontWeight: FontWeight.w600,
          ),
        ),
      ),
      home: const LoginScreen(),
    );
  }
}
