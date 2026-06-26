<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \Illuminate\Support\Str::title(\App\Models\Setting::get('store_name') ?? config('app.name', 'Apotek')) }}</title>

        @if($logoPath = \App\Models\Setting::get('store_logo_path'))
            <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $logoPath) }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            function initAlpineStores() {
                // Register stores. We don't use a guard here to ensure 
                // that stores are correctly re-bound if Alpine re-initializes
                // during a Livewire navigation event.
                
                Alpine.store('mobileNav', {
                    open: false, // Ensure it's always false on initial load/nav
                    toggle() {
                        this.open = !this.open;
                    },
                    close() {
                        this.open = false;
                    }
                });

                Alpine.store('sidebar', {
                    collapsed: localStorage.getItem('sidebar_collapsed') === 'true',
                    init() {
                        if (this.collapsed) {
                            document.documentElement.classList.add('sidebar-collapsed');
                        } else {
                            document.documentElement.classList.remove('sidebar-collapsed');
                        }
                    },
                    toggle() {
                        this.collapsed = !this.collapsed;
                        localStorage.setItem('sidebar_collapsed', this.collapsed);
                        if (this.collapsed) {
                            document.documentElement.classList.add('sidebar-collapsed');
                        } else {
                            document.documentElement.classList.remove('sidebar-collapsed');
                        }
                    }
                });

                Alpine.data('money', (model) => ({
                    displayValue: '',
                    value: model,
                    timeout: null,

                    init() {
                        this.$watch('value', (newValue) => {
                            // Only update displayValue if we are not currently typing/debouncing
                            // Compare unformatted values to prevent strict inequality bugs when Livewire passes string numbers
                            let unformattedNew = this.unformat(newValue);
                            let unformattedCurrent = this.unformat(this.displayValue);
                            
                            // Check if the user is currently typing
                            let inputEl = this.$el.tagName === 'INPUT' ? this.$el : this.$el.querySelector('input');
                            let isFocused = inputEl && document.activeElement === inputEl;

                            // Do not format if the user is actively typing (isFocused).
                            // This prevents stale Livewire responses from reverting the input text
                            // and causing the value to jump back (e.g. 10.000 reverting to 1.000).
                            if (!this.timeout && unformattedNew !== unformattedCurrent && !isFocused) {
                                this.formatDisplay();
                            }
                        });
                        
                        if (this.value !== null && this.value !== undefined) {
                            this.formatDisplay();
                        }
                    },

                    formatDisplay() {
                        if (this.value === null || this.value === '' || this.value === undefined) {
                            this.displayValue = '';
                        } else {
                            let parsed = this.unformat(this.value);
                            if (parsed === null) {
                                this.displayValue = '';
                            } else {
                                this.displayValue = new Intl.NumberFormat('id-ID').format(parsed);
                            }
                        }
                        
                        // Sync to DOM manually to avoid x-model cursor conflicts
                        let inputEl = this.$el.tagName === 'INPUT' ? this.$el : this.$el.querySelector('input');
                        if (inputEl && inputEl.value !== this.displayValue) {
                            inputEl.value = this.displayValue;
                        }
                    },

                    unformat(val) {
                        if (val === null || val === undefined || val === '') return null;
                        let cleaned = val.toString().replace(/[^0-9]/g, '');
                        if (cleaned === '') return null;
                        return parseInt(cleaned, 10);
                    },

                    input: {
                        ['@input']($event) {
                            const input = $event.target;
                            const selectionStart = input.selectionStart;
                            const oldLength = input.value.length;
                            
                            // Always clear any pending debounce timeout immediately on any keystroke/deletion
                            if (this.timeout) {
                                clearTimeout(this.timeout);
                                this.timeout = null;
                            }
                            
                            let raw = input.value.replace(/[^0-9]/g, '');
                            if (raw === '') {
                                this.displayValue = '';
                                input.value = '';
                                this.value = null;
                                return;
                            }
                            
                            let number = parseInt(raw, 10);
                            
                            // Format locally immediately for smooth feedback
                            let formatted = new Intl.NumberFormat('id-ID').format(number);
                            this.displayValue = formatted;
                            
                            if (input.value !== formatted) {
                                input.value = formatted;
                                
                                // Adjust cursor position
                                const newLength = formatted.length;
                                const diff = newLength - oldLength;
                                const newPos = Math.max(0, selectionStart + diff);
                                input.setSelectionRange(newPos, newPos);
                            }

                            // Debounce the update to 'value' (Livewire entangle)
                            // This prevents server roundtrips from interrupting typing
                            this.timeout = setTimeout(() => {
                                this.value = number;
                                this.timeout = null;
                            }, 500);

                            // Dispatch price-changed for real-time Margin & Total display
                            const row = input?.dataset?.row;
                            if (row !== undefined && row !== '') {
                                window.dispatchEvent(new CustomEvent('price-changed', { detail: { row } }));
                            }
                        },
                        ['@keydown.down.prevent']($event) {
                            const input = $event.target;
                            const col = input.dataset.col;
                            if (col) {
                                // Try relative DOM traversal (table row siblings)
                                const nextRow = input.closest('tr')?.nextElementSibling;
                                if (nextRow) {
                                    const nextInput = nextRow.querySelector(`input[data-col="${col}"]`);
                                    if (nextInput) {
                                        nextInput.focus();
                                        nextInput.select();
                                        return;
                                    }
                                }
                                // Fallback to legacy numeric row index
                                const row = parseInt(input.dataset.row, 10);
                                if (!isNaN(row)) {
                                    const nextInput = document.querySelector(`input[data-col="${col}"][data-row="${row + 1}"]`);
                                    if (nextInput) {
                                        nextInput.focus();
                                        nextInput.select();
                                    }
                                }
                            }
                        },
                        ['@keydown.up.prevent']($event) {
                            const input = $event.target;
                            const col = input.dataset.col;
                            if (col) {
                                // Try relative DOM traversal (table row siblings)
                                const prevRow = input.closest('tr')?.previousElementSibling;
                                if (prevRow) {
                                    const prevInput = prevRow.querySelector(`input[data-col="${col}"]`);
                                    if (prevInput) {
                                        prevInput.focus();
                                        prevInput.select();
                                        return;
                                    }
                                }
                                // Fallback to legacy numeric row index
                                const row = parseInt(input.dataset.row, 10);
                                if (!isNaN(row) && row > 0) {
                                    const prevInput = document.querySelector(`input[data-col="${col}"][data-row="${row - 1}"]`);
                                    if (prevInput) {
                                        prevInput.focus();
                                        prevInput.select();
                                    }
                                }
                            }
                        },
                        ['@blur']() {
                            let currentUnformatted = this.unformat(this.displayValue);
                            if (this.timeout) {
                                clearTimeout(this.timeout);
                                this.timeout = null;
                                this.value = currentUnformatted;
                            } else if (this.value !== currentUnformatted) {
                                this.value = currentUnformatted;
                            }
                            this.formatDisplay();
                            // Dispatch event so Margin & Total cells update client-side
                            const input = this.$el.tagName === 'INPUT' ? this.$el : this.$el.querySelector('input');
                            const row = input?.dataset?.row;
                            if (row !== undefined && row !== '') {
                                window.dispatchEvent(new CustomEvent('price-changed', { detail: { row } }));
                            }
                        },

                        ['inputmode']: 'numeric',
                        ['placeholder']: '0',
                    }
                }));
            }

            // Register stores immediately if Alpine is already loaded (e.g. after wire:navigate redirect from login)
            if (window.Alpine) {
                initAlpineStores();
            } else {
                document.addEventListener('alpine:init', initAlpineStores);
            }
            
            // Re-initialize if navigating with Livewire
            document.addEventListener('livewire:navigated', () => {
                if (window.Alpine) {
                    initAlpineStores();
                }
            });

            // Auto-scroll to top on save/submit except for Cashier
            let isSubmitting = false;

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('button, input[type="submit"], a');
                if (btn) {
                    const isSubmitType = btn.getAttribute('type') === 'submit';
                    const wireClick = btn.getAttribute('wire:click') || '';
                    const isSaveAction = wireClick.includes('save') || 
                                         wireClick.includes('store') || 
                                         wireClick.includes('update') || 
                                         wireClick.includes('delete') || 
                                         wireClick.includes('submit');
                    
                    if (isSubmitType || isSaveAction) {
                        isSubmitting = true;
                    }
                }
            });

            document.addEventListener('submit', () => {
                isSubmitting = true;
            });

            function registerLivewireScrollHook() {
                if (window.hasRegisteredLivewireScrollHook) return;
                
                if (typeof Livewire !== 'undefined') {
                    Livewire.hook('request', ({ succeed }) => {
                        succeed(() => {
                            const path = window.location.pathname;

                            // Halaman yang sepenuhnya dikecualikan dari auto-scroll
                            // (chart filter changes, pagination, dll tidak boleh scroll ke atas)
                            const isExcludedPage =
                                path === '/' ||
                                path === '/dashboard' ||
                                path.includes('/cashier') ||
                                path.includes('/goods-receipts/create') ||
                                /\/goods-receipts\/\d+\/edit/.test(path);

                            if (isExcludedPage) {
                                isSubmitting = false;
                                return;
                            }

                            // Use a short setTimeout to allow the DOM to fully settle and paint
                            setTimeout(() => {
                                // Hanya cocokkan elemen notifikasi/alert yang sesungguhnya.
                                // JANGAN gunakan .text-red-500/.text-red-600 karena kelas ini
                                // ada di chart, badge, dan ikon — bukan hanya di alert.
                                const hasAlert = document.querySelector(
                                    '[role="alert"], ' +
                                    '[data-alert], ' +
                                    '.livewire-error-message, ' +
                                    '.border-l-4.border-green-500, ' +
                                    '.border-l-4.border-red-500, ' +
                                    '.border-l-4.border-yellow-500'
                                );

                                if (isSubmitting || hasAlert) {
                                    const wrapper = document.querySelector('.main-content-wrapper');
                                    if (wrapper) {
                                        wrapper.scrollTo({ top: 0, behavior: 'smooth' });
                                        wrapper.scrollTop = 0;
                                    }
                                    window.scrollTo({ top: 0, behavior: 'smooth' });
                                    isSubmitting = false;
                                }
                            }, 100);
                        });
                    });
                    window.hasRegisteredLivewireScrollHook = true;
                }
            }

            // Bind hook using multiple lifecycle hooks to guarantee registration
            if (typeof Livewire !== 'undefined') {
                registerLivewireScrollHook();
            } else {
                document.addEventListener('livewire:init', registerLivewireScrollHook);
            }
            document.addEventListener('livewire:navigated', registerLivewireScrollHook);
        </script>
        <style>
            [x-cloak] { display: none !important; }
            @media (max-width: 1279px) {
                .desktop-toggle-btn { display: none !important; }
            }
            
            /* Anti-Bounce Sidebar & Content Layout Constraints */
            @media (min-width: 1280px) {
                html:not(.sidebar-collapsed) .main-content-wrapper {
                    margin-left: 16rem !important; /* xl:ml-64 */
                }
                html.sidebar-collapsed .main-content-wrapper {
                    margin-left: 5rem !important; /* xl:ml-20 */
                }
                html:not(.sidebar-collapsed) .sidebar-nav {
                    width: 16rem !important; /* w-64 */
                }
                html.sidebar-collapsed .sidebar-nav {
                    width: 5rem !important; /* w-20 */
                }
                
                /* Hide submenu items and arrows when sidebar is collapsed on desktop */
                html.sidebar-collapsed .sidebar-nav div[x-collapse] {
                    display: none !important;
                }
                html.sidebar-collapsed .sidebar-nav button svg[x-show*="collapsed"] {
                    display: none !important;
                }
            }
            
            /* Hide page titles in content body on desktop since they are in the navbar */
            @media (min-width: 1280px) {
                .main-content-wrapper.has-navbar > main > div > div:first-child h2,
                .main-content-wrapper.has-navbar > main > div > div:first-child h1 {
                    display: none !important;
                }
                .main-content-wrapper.has-navbar > main > div > .mb-6:first-child:not(:has(a, button, input, select, form)),
                .main-content-wrapper.has-navbar > main > div > .mb-8:first-child:not(:has(a, button, input, select, form)) {
                    margin-bottom: 0 !important;
                    padding-bottom: 0 !important;
                }
            }
        </style>
    <script>
        // Apply sidebar state immediately to prevent layout bounce
        if (localStorage.getItem('sidebar_collapsed') === 'true') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
        // Apply navbar left position before Alpine loads to prevent flash
        (function() {
            var collapsed = localStorage.getItem('sidebar_collapsed') === 'true';
            var style = document.createElement('style');
            style.id = 'navbar-init-style';
            style.textContent = '#desktop-navbar { left: ' + (collapsed ? '5rem' : '16rem') + ' !important; }';
            document.head.appendChild(style);
            
            function removeStyle() {
                var el = document.getElementById('navbar-init-style');
                if (el) el.remove();
            }

            // Remove the override once Alpine has taken control
            if (window.Alpine) {
                // If Alpine is already running (e.g. wire:navigate redirect), remove style on microtask
                setTimeout(removeStyle, 50);
            } else {
                document.addEventListener('alpine:initialized', removeStyle);
            }
            
            // Clean up style on any Livewire navigation event as well
            document.addEventListener('livewire:navigated', removeStyle);
        })();
    </script>
    </head>
    <body class="font-sans antialiased">
        <div x-data="{}" class="h-screen bg-milky-white dark:bg-gray-900 overflow-hidden overflow-x-hidden flex flex-col">
            <livewire:layout.navigation />

            <!-- Main Content Area -->
            <div @class([
                'main-content-wrapper flex-1 flex flex-col overflow-y-auto scrollbar-hide relative pt-12 transition-all duration-300',
                'xl:pt-0' => request()->routeIs('pos.cashier'),
                'xl:pt-12' => !request()->routeIs('pos.cashier'),
                'has-navbar' => !request()->routeIs('pos.cashier'),
            ])>

                <!-- Page Heading -->
                @if (isset($header))
                    <header>
                        <div class="max-w-screen-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
