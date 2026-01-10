<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Riyadlul Huda</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navigation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Turbo Drive - DISABLED to prevent JavaScript conflicts -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js"></script> -->
    
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    
    <style>
        :root {
            --font-family-base: 'Outfit', sans-serif;
            --sidebar-width: 280px;
            --header-height: 70px;
        }
        
        body {
            font-family: var(--font-family-base);
            background-color: #f3f4f6;
            overflow-x: hidden;
        }

        /* Hide scrollbars globally but keep scroll functionality */
        * {
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }
        *::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        /* Gradient Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1a202c 0%, #2d3748 100%);
            box-shadow: 4px 0 24px rgba(0,0,0,0.05);
            border-right: none;
        }
        
        .sidebar-logo {
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding: 24px;
        }
        
        .sidebar-logo-text {
            font-weight: 700;
            background: linear-gradient(to right, #fff, #a0aec0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.25rem;
        }

        .sidebar-menu-link {
            border-radius: 12px;
            margin: 4px 16px;
            padding: 12px 16px;
            transition: all 0.3s ease;
            font-weight: 500;
            color: #a0aec0;
        }
        
        .sidebar-menu-link:hover {
            background: rgba(255,255,255,0.05);
            color: white;
            transform: translateX(4px);
        }
        
        .sidebar-menu-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(118, 75, 162, 0.3);
        }

        /* Glass Header */
        .page-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            padding: 0 32px;
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 40;
            margin-bottom: 32px;
        }
        
        .page-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        /* Hide mobile hamburger on desktop */
        .header-hamburger {
            display: none;
        }

        .realtime-clock {
            font-variant-numeric: tabular-nums;
            font-weight: 600;
            color: #4b5563;
        }

        /* Backup Button */
        .backup-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            color: #a0aec0;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            margin: 0 16px;
        }
        
        .backup-btn:hover {
            background: rgba(255,255,255,0.15);
            color: white;
        }

        /* Main Content Area */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            :root {
                --sidebar-width: 0px;
            }
            .sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                width: 280px;
                height: 100vh;
                z-index: 100;
                transition: left 0.3s ease;
                background: linear-gradient(180deg, #1e3a5f 0%, #0d1b2a 100%);
            }
            .sidebar.open {
                left: 0 !important;
            }
            .main-content {
                margin-left: 0;
            }
            .page-header {
                padding: 0 16px;
            }
            .page-title {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 640px) {
            .page-header {
                padding: 0 12px !important;
            }
            .page-header-actions {
                gap: 12px !important;
            }
            .page-header-actions > div:not(:last-child):not(.notification-bell) {
                display: none;
            }
            .page-header-actions > div:last-child {
                padding-left: 12px !important;
                border-left: none !important;
            }
        }
    </style>
    
    @stack('styles')
    <!-- Currency Formatter Script -->
    <script>
        document.addEventListener('turbo:load', function() {
            const formatRupiah = (angka, prefix) => {
                let number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
            };

            document.querySelectorAll('.format-rupiah').forEach(function(input) {
                // Initialize value on load
                if (input.value) {
                   input.value = formatRupiah(input.value);
                }

                input.addEventListener('keyup', function(e) {
                    this.value = formatRupiah(this.value);
                });
            });
        });
    </script>
</head>
<body>
    <!-- Sidebar Navigation (Desktop & Tablet) -->
    <aside class="sidebar">
        <div class="sidebar-logo" style="display: flex; align-items: center; gap: 12px;">
            <img src="{{ asset('images/logo-yayasan.png') }}" alt="Logo" style="width: 32px; height: 32px;">
            <span class="sidebar-logo-text">Riyadlul Huda</span>
        </div>
        
        <div style="padding: 16px 0;">
            <a href="{{ route('backup.download') }}" class="backup-btn" title="Download Database Backup (Realtime)">
                <i data-feather="database" style="width: 14px; height: 14px;"></i>
                <span>Backup Data</span>
            </a>
        </div>
        
        <ul class="sidebar-menu" style="list-style: none; padding: 0;">
            @yield('sidebar-menu')
        </ul>
        
        <!-- Bottom Sidebar Info -->
        <div style="position: absolute; bottom: 0; width: 100%; padding: 24px; border-top: 1px solid rgba(255,255,255,0.05);">
            <div style="font-size: 12px; color: #718096;">
                &copy; {{ date('Y') }} Riyadlul Huda
                <br>
                <span style="opacity: 0.6;">Ver 2.0.0 (Aesthetic)</span>
            </div>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <!-- Mobile Hamburger (inside header) -->
            <button class="header-hamburger" onclick="toggleSidebar()">
                <i data-feather="menu" style="width: 20px; height: 20px; color: #64748b;"></i>
            </button>
            
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            
            <div class="page-header-actions" style="display: flex; align-items: center; gap: 24px;">
                @if(isset($showSearch) && $showSearch)
                <div class="search-bar" style="background: white; border-radius: 20px; padding: 6px 16px; border: 1px solid #e5e7eb; display: flex; align-items: center; gap: 8px;">
                    <i data-feather="search" style="width: 14px; height: 14px; color: #9ca3af;"></i>
                    <input type="text" placeholder="Cari..." style="border: none; outline: none; font-size: 13px; width: 150px;">
                </div>
                @endif

                <div style="text-align: right;">
                    <div class="realtime-clock" id="realtime-clock" style="font-size: 14px; font-weight: 700;">00:00:00</div>
                    <div class="realtime-date" id="realtime-date" style="font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Loading...</div>
                </div>
                
                <div class="notification-bell" style="position: relative;" id="notification-container">
                    <div onclick="toggleNotifications()" style="background: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.05); cursor: pointer;">
                        <i data-feather="bell" style="width: 16px; height: 16px; color: #64748b;"></i>
                    </div>
                    <span class="notification-badge" id="notification-count" style="position: absolute; top: 0; right: 0; background: #ef4444; min-width: 10px; height: 10px; border-radius: 50%; border: 2px solid white; font-size: 8px; display: none;"></span>
                    
                    <!-- Notification Dropdown -->
                    <div id="notification-dropdown" style="display: none; position: absolute; top: 45px; right: 0; width: 320px; max-height: 400px; background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); z-index: 1000; overflow: hidden;">
                        <div style="padding: 16px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; color: #1f2937;">Notifikasi</span>
                            <button onclick="markAllAsRead()" style="background: none; border: none; color: #3b82f6; font-size: 12px; cursor: pointer;">Tandai semua dibaca</button>
                        </div>
                        <div id="notification-list" style="max-height: 300px; overflow-y: auto;">
                            <div style="padding: 24px; text-align: center; color: #9ca3af;">
                                <i data-feather="bell-off" style="width: 32px; height: 32px; margin-bottom: 8px;"></i>
                                <p style="margin: 0; font-size: 13px;">Tidak ada notifikasi</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 10px; padding-left: 24px; border-left: 1px solid #e2e8f0;">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px;">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Page Content -->
        <div style="padding: 0 32px 32px 32px;">
            @yield('content')
            
            <!-- Watermark -->
            <div style="text-align: center; margin-top: 48px; color: #9ca3af; font-size: 11px; opacity: 0.6;">
                Dibuat oleh Mahin Utsman Nawawi, S.H
            </div>
        </div>
    </main>
    
    <!-- Mobile Bottom Navigation (Android-style) -->
    <style>
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.08);
            z-index: 100;
            padding: 8px 0 max(8px, env(safe-area-inset-bottom));
        }
        
        @media (max-width: 768px) {
            .mobile-bottom-nav {
                display: block;
            }
            .main-content {
                padding-bottom: 80px;
            }
        }
        
        .bottom-nav-items {
            display: flex;
            justify-content: space-around;
            align-items: center;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .bottom-nav-item {
            flex: 1;
            text-align: center;
        }
        
        .bottom-nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 8px 12px;
            color: #9ca3af;
            text-decoration: none;
            transition: all 0.2s;
            border-radius: 12px;
        }
        
        .bottom-nav-link:active {
            transform: scale(0.95);
        }
        
        .bottom-nav-link.active {
            color: #3b82f6;
        }
        
        .bottom-nav-link.active .bottom-nav-icon {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .bottom-nav-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            transition: all 0.2s;
        }
        
        .bottom-nav-label {
            font-size: 11px;
            font-weight: 500;
        }
        
        /* Hamburger Button for Sidebar */
        .hamburger-btn {
            display: none;
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 60;
            background: white;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            cursor: pointer;
        }
        
        /* Show hamburger on mobile and tablet */
        @media (max-width: 1024px) {
            .hamburger-btn {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
        
        .hamburger-icon {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .hamburger-icon span {
            width: 20px;
            height: 2px;
            background: #1f2937;
            border-radius: 2px;
            transition: all 0.3s;
        }
        
        .hamburger-btn.active .hamburger-icon span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        
        .hamburger-btn.active .hamburger-icon span:nth-child(2) {
            opacity: 0;
        }
        
        .hamburger-btn.active .hamburger-icon span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }
        
        /* Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 45;
        }
        
        @media (max-width: 1024px) {
            .sidebar-overlay.active {
                display: block;
            }
        }
    </style>
    
    <nav class="mobile-bottom-nav">
        <ul class="bottom-nav-items">
            @yield('bottom-nav')
        </ul>
    </nav>
    
    <!-- Hamburger Button -->
    <button class="hamburger-btn" id="hamburger-btn">
        <div class="hamburger-icon">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </button>
    
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    
    <script>
        // Global toggleSidebar function for header hamburger
        function toggleSidebar() {
            console.log('toggleSidebar called');
            const sidebarEl = document.querySelector('.sidebar');
            const overlayEl = document.getElementById('sidebar-overlay');
            
            console.log('sidebar:', sidebarEl);
            console.log('overlay:', overlayEl);
            
            if (sidebarEl && overlayEl) {
                sidebarEl.classList.toggle('open');
                overlayEl.classList.toggle('active');
                console.log('sidebar.open:', sidebarEl.classList.contains('open'));
            } else {
                console.error('Sidebar or overlay not found!');
            }
        }
        
        // Close sidebar when clicking overlay
        document.addEventListener('DOMContentLoaded', function() {
            const overlayEl = document.getElementById('sidebar-overlay');
            if (overlayEl) {
                overlayEl.addEventListener('click', function() {
                    const sidebarEl = document.querySelector('.sidebar');
                    sidebarEl.classList.remove('open');
                    overlayEl.classList.remove('active');
                });
            }
        });
    </script>
    
    <!-- JavaScript -->
    <script src="{{ asset('js/realtime.js') }}"></script>
    <script src="{{ asset('js/modal.js') }}"></script>
    <script src="{{ asset('js/rupiah-format.js') }}"></script>
    <script>
        feather.replace();
        document.addEventListener('turbo:load', function() {
            feather.replace();
        });
        document.addEventListener('turbo:before-visit', function() {
            document.body.style.cursor = 'wait';
        });
        document.addEventListener('turbo:load', function() {
            document.body.style.cursor = 'default';
        });
    </script>
    

    
    <!-- Global Bottom Navigation (Auto-detect context) -->
    @php
        $currentRoute = Route::currentRouteName();
        $context = 'admin';
        $active = 'dashboard';
        
        // Detect context from route
        if (str_starts_with($currentRoute, 'pendidikan')) {
            $context = 'pendidikan';
            if (str_contains($currentRoute, 'nilai')) $active = 'nilai';
            elseif (str_contains($currentRoute, 'laporan')) $active = 'rapor';
            elseif (str_contains($currentRoute, 'ijazah')) $active = 'ijazah';
            elseif (str_contains($currentRoute, 'absensi')) $active = 'absensi';
            else $active = 'dashboard';
        } elseif (str_starts_with($currentRoute, 'sekretaris')) {
            $context = 'sekretaris';
            if (str_contains($currentRoute, 'data-santri')) $active = 'santri';
            elseif (str_contains($currentRoute, 'mutasi')) $active = 'mutasi';
            else $active = 'dashboard';
        } elseif (str_starts_with($currentRoute, 'bendahara')) {
            $context = 'bendahara';
            if (str_contains($currentRoute, 'syahriah')) $active = 'syahriah';
            elseif (str_contains($currentRoute, 'pemasukan')) $active = 'pemasukan';
            else $active = 'dashboard';
        }
    @endphp
    
    @include('components.bottom-nav', ['active' => $active, 'context' => $context])
    
    @stack('scripts')
    
    <!-- Notification JavaScript -->
    <script>
        let notificationDropdownOpen = false;
        
        function toggleNotifications() {
            const dropdown = document.getElementById('notification-dropdown');
            notificationDropdownOpen = !notificationDropdownOpen;
            dropdown.style.display = notificationDropdownOpen ? 'block' : 'none';
            
            if (notificationDropdownOpen) {
                fetchNotifications();
            }
            
            // Re-render feather icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('notification-container');
            if (container && !container.contains(e.target)) {
                document.getElementById('notification-dropdown').style.display = 'none';
                notificationDropdownOpen = false;
            }
        });
        
        function fetchNotifications() {
            fetch('/api/notifications')
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('notification-list');
                    const badge = document.getElementById('notification-count');
                    
                    if (data.unread_count > 0) {
                        badge.style.display = 'block';
                        badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                    } else {
                        badge.style.display = 'none';
                    }
                    
                    if (data.notifications.length === 0) {
                        list.innerHTML = `
                            <div style="padding: 24px; text-align: center; color: #9ca3af;">
                                <i data-feather="bell-off" style="width: 32px; height: 32px; margin-bottom: 8px;"></i>
                                <p style="margin: 0; font-size: 13px;">Tidak ada notifikasi</p>
                            </div>
                        `;
                    } else {
                        list.innerHTML = data.notifications.map(n => `
                            <div onclick="markAsRead(${n.id})" style="padding: 12px 16px; border-bottom: 1px solid #f1f5f9; cursor: pointer; background: ${n.is_read ? 'white' : '#f8fafc'};">
                                <div style="display: flex; gap: 12px;">
                                    <div style="width: 32px; height: 32px; background: ${n.color}20; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i data-feather="${n.icon}" style="width: 16px; height: 16px; color: ${n.color};"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-size: 13px; font-weight: 600; color: #1f2937;">${n.title}</div>
                                        <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">${n.message}</div>
                                        <div style="font-size: 10px; color: #9ca3af; margin-top: 4px;">${new Date(n.created_at).toLocaleDateString('id-ID')}</div>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    }
                    
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                })
                .catch(err => console.log('Notification fetch error:', err));
        }
        
        function markAsRead(id) {
            fetch(`/api/notifications/${id}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .then(() => fetchNotifications());
        }
        
        function markAllAsRead() {
            fetch('/api/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .then(() => fetchNotifications());
        }
        
        // Poll for new notifications every 30 seconds
        setInterval(fetchNotifications, 30000);
        
        // Initial fetch
        document.addEventListener('DOMContentLoaded', fetchNotifications);
    </script>
    
    <!-- Developer Profile Component -->
    @include('components.developer-profile')
    
    <!-- Help/FAQ Component -->
    @include('components.help-faq')
    

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Global SweetAlert2 Helpers
        
        // Use for Form Delete Confirmation (onsubmit="return confirmDelete(event)")
        function confirmDelete(event, message = 'Data yang dihapus tidak dapat dikembalikan!', title = 'Yakin ingin menghapus?') {
            event.preventDefault();
            const form = event.target;

            Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Red
                cancelButtonColor: '#6b7280', // Gray
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }

        // Use for General Action Confirmation (onclick="confirmAction('form-id', 'Message')")
        function confirmAction(formId, message = 'Apakah Anda yakin?', title = 'Konfirmasi', btnText = 'Ya, Lanjutkan!', btnColor = '#6366f1') {
            Swal.fire({
                title: title,
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: btnColor,
                cancelButtonColor: '#6b7280',
                confirmButtonText: btnText,
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
</body>
</html>
