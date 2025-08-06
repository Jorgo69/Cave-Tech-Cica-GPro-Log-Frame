<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark" x-data="appData()" x-init="initTheme()">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Animations personnalisées */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .dark .card-hover:hover {
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
        
        /* Scrollbar personnalisée */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .dark .custom-scrollbar::-webkit-scrollbar-track {
            background: #1f2937;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
        
        /* Transition fluide pour le mode sombre */
        body, header, aside, .card, .hover\:bg-gray-100, .bg-white, .bg-gray-50 {
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
    </style>

    <title>{{ $title ?? config('app.name') }}</title>
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
    

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- @livewireStyles --}}
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans">
    
        @include('layouts.navbar')
        <!-- Sidebar -->
        @include('layouts.sidebar')
    
    
    <div x-show="isMobile && sidebarOpen" @click="sidebarOpen = false" 
         class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden" x-transition></div>

    {{ $slot }}
    
    <script>
        function appData() {
            return {
                sidebarOpen: false,
                isMobile: false,
                profileDropdownOpen: false,
                darkMode: localStorage.getItem('darkMode') === 'true',
                
                init() {
                    this.initTheme();
                    this.initSidebar();
                },
                
                initTheme() {
                    if (localStorage.getItem('darkMode') === 'true' || 
                        (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                        document.documentElement.classList.add('dark');
                        this.darkMode = true;
                    } else {
                        document.documentElement.classList.remove('dark');
                        this.darkMode = false;
                    }
                },
                
                initSidebar() {
                    this.checkScreenSize();
                    window.addEventListener('resize', () => this.checkScreenSize());
                    // Ouvrir par défaut sur grand écran
                    if (!this.isMobile) {
                        this.sidebarOpen = true;
                    }
                },
                
                checkScreenSize() {
                    this.isMobile = window.innerWidth < 1024; // lg breakpoint
                    // Ajuster l'état du sidebar en fonction de la taille
                    if (this.isMobile) {
                        this.sidebarOpen = false;
                    } else {
                        this.sidebarOpen = true;
                    }
                },
                
                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('darkMode', 'true');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('darkMode', 'false');
                    }
                }
            }
        }
    </script>
    
    <script>
document.addEventListener('livewire:init', () => {
    Livewire.on('notify', (data) => {
        alert(data.message);
    });
});
</script>
    {{-- @livewireScripts --}}
</body>
</html>