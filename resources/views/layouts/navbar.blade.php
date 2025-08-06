<!-- ========== HEADER ========== -->
<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 fixed w-full top-0 z-50">
    <div class="flex items-center justify-between px-4 py-3">
        <!-- Logo et Menu Mobile -->
        <div class="flex items-center space-x-4">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 gradient-bg rounded-lg flex items-center justify-center">
                    <i class="fas fa-handshake text-white text-lg"></i>
                </div>
                
                <h1 class="text-xl font-bold text-gray-800 dark:text-white hidden sm:block">{{ config('app.name') }}</h1>
            </div>
        </div>
        
        <!-- Barre de recherche -->
        <div class="flex-1 max-w-md mx-4 hidden md:block">
            <div class="relative">
                <input type="text" 
                        placeholder="Rechercher..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        
        <!-- Profil utilisateur et bascule du mode sombre -->
        <div class="flex items-center space-x-4">
            <!-- Bouton bascule mode sombre -->
            <button @click="toggleTheme()" class="p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                <i x-show="!darkMode" class="fas fa-moon"></i>
                <i x-show="darkMode" class="fas fa-sun"></i>
            </button>
            
            <!-- Notifications -->
            <button class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                <i class="fas fa-bell text-lg"></i>
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
            </button>
            
            <!-- Menu profil -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face" 
                            alt="Profile" 
                            class="w-8 h-8 rounded-full object-cover">
                    <div class="hidden sm:block text-left">
                        <div class="text-sm font-medium text-gray-800 dark:text-white">
                            {{ auth()->user()->name ?? 'Personne connecte' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ auth()->user()->role->name ?? 'Aucun role' }}
                        </div>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-sm" :class="{ 'transform rotate-180': open }"></i>
                </button>
                
                <!-- Dropdown menu -->
                <div x-show="open" 
                     @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-100" 
                     x-transition:enter-start="transform opacity-0 scale-95" 
                     x-transition:enter-end="transform opacity-100 scale-100" 
                     x-transition:leave="transition ease-in duration-75" 
                     x-transition:leave-start="transform opacity-100 scale-100" 
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                    <div class="py-2">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-user mr-2"></i>Mon Profil
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-cog mr-2"></i>Paramètres
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-question-circle mr-2"></i>Support
                        </a>
                        
                        @auth
                            <hr class="my-1 border-gray-200 dark:border-gray-700">
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                                </button>
                            </form>
                        @else
                            <hr class="my-1 border-gray-200 dark:border-gray-700">
                            <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-sign-in-alt mr-2"></i>Connexion
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

