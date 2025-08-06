<!-- ========== SIDEBAR ========== -->
<aside id="sidebar" 
       x-show="sidebarOpen || !isMobile"
       :class="{ '-translate-x-full': isMobile && !sidebarOpen, 'translate-x-0': !isMobile || sidebarOpen }"
       class="fixed left-0 top-16 w-64 h-full bg-white dark:bg-gray-800 shadow-lg border-r border-gray-200 dark:border-gray-700 sidebar-transition z-40">
    <div class="p-4 custom-scrollbar overflow-y-auto h-full">
        <nav class="space-y-2">
            <!-- Dashboard -->
            <div class="mb-6">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600  text-white">
                    <i class="fas fa-solid fa-house text-lg"></i>
                    <span class="font-medium">{{ __('Dashboard') }}</span>
                </a>
            </div>

            <!-- Projects -->
            <div class="mb-4">
                <a href="{{ route('project.list') }}" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text- @if(Route::is('accomodation*')) rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 @endif">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-folder text-gray-600 dark:text-gray-300"></i>
                        <span class="font-medium text-gray-800 dark:text-white">{{ __('Project') }}</span>
                    </div>
                </a>
            </div>

            <!-- Project Design -->
            <div class="mb-4" x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left @if(Route::is('booking.host*')) rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 @endif">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-lightbulb text-gray-600 dark:text-gray-300"></i>
                        <span class="font-medium text-gray-800 dark:text-white">{{ __('Administrator') }}</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 transition-transform" :class="{ 'transform rotate-90': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ml-6 mt-2 space-y-1">
                    <a href="{{ route('admin.it.type.of.project') }}" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Type de Projet') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Environmental analysis') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Identification of stakeholders') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Problem Analysis') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Definition of Strategy') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Goals and Objectives') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Expected results') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Activities') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Risk analysis') }}
                    </a>
                </div>
            </div>
            
            
            <!-- Project Design -->
            <div class="mb-4" x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left @if(Route::is('booking.host*')) rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 @endif">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-lightbulb text-gray-600 dark:text-gray-300"></i>
                        <span class="font-medium text-gray-800 dark:text-white">{{ __('Project Design') }}</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 transition-transform" :class="{ 'transform rotate-90': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ml-6 mt-2 space-y-1">
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('AI Context') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Environmental analysis') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Identification of stakeholders') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Problem Analysis') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Definition of Strategy') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Goals and Objectives') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Expected results') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Activities') }}
                    </a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('Risk analysis') }}
                    </a>
                </div>
            </div>
            
            <!-- Clients -->
            <div class="mb-4" x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left @if(Route::is('room.host*')) rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 @endif">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-users text-gray-600 dark:text-gray-300"></i>
                        <span class="font-medium text-gray-800 dark:text-white">{{ __('Project Info') }}</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 transition-transform" :class="{ 'transform rotate-90': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ml-6 mt-2 space-y-1">
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">{{ __('General Description') }}</a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">{{ __('Target Groups') }}</a>
                    <a href="" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">{{ __('Deadlines Monitoring') }}</a>
                </div>
            </div>
            
            <!-- Marketing -->
            <div class="mb-4">
                <a href=# class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text- @if(Route::is('accomodation*')) rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 @endif">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-folder text-gray-600 dark:text-gray-300"></i>
                        <span class="font-medium text-gray-800 dark:text-white">{{ __('Logical Framework (CaLo)') }}</span>
                    </div>
                </a>
            </div>

            {{-- Planning --}}
            <div class="mb-4">
                <a href=# class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text- @if(Route::is('accomodation*')) rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 @endif">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-folder text-gray-600 dark:text-gray-300"></i>
                        <span class="font-medium text-gray-800 dark:text-white">{{ __('Planning') }}</span>
                    </div>
                </a>
            </div>

            {{-- Budget --}}
            <div class="mb-4">
                <a href=# class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text- @if(Route::is('accomodation*')) rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 @endif">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-folder text-gray-600 dark:text-gray-300"></i>
                        <span class="font-medium text-gray-800 dark:text-white">{{ __('Budget)') }}</span>
                    </div>
                </a>
            </div>


            
            <!-- Reports -->
            <div class="mb-4">
                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-wallet text-gray-600 dark:text-gray-300"></i>
                    <span class="font-medium text-gray-800 dark:text-white">{{ __('Reports') }}</span>
                </a>
            </div>

            {{-- Progress Tracker --}}
            <div class="mb-4">
                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-wallet text-gray-600 dark:text-gray-300"></i>
                    <span class="font-medium text-gray-800 dark:text-white">{{ __('Progress Tracker') }}</span>
                </a>
            </div>
            
            <!-- Settings -->
            <div class="mb-4" x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-tools text-gray-600 dark:text-gray-300"></i>
                        <span class="font-medium text-gray-800 dark:text-white">{{ __('Settings') }}</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 transition-transform" :class="{ 'transform rotate-90': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ml-6 mt-2 space-y-1">
                    <a href="#" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Intégrations</a>
                    <a href="#" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">API</a>
                    <a href="#" class="block p-2 rounded text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Webhooks</a>
                </div>
            </div>
        </nav>
    </div>
</aside>

