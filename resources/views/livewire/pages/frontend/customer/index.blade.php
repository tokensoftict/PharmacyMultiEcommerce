<?php

use function Livewire\Volt\{state};
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layout.blank')] class extends Component
{

}
?>

<div x-data="landingPage()" class="relative min-h-screen w-full bg-darkBg text-white overflow-hidden" @mousemove="updateGlow($event)">
    
    <!-- Background Animated Mesh Gradient & Floating Orbs -->
    <div class="absolute inset-0 pointer-events-none z-0 overflow-hidden">
        <!-- Main background glow -->
        <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] rounded-full bg-purple-500/10 blur-[120px] animate-pulse-slow"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[65%] h-[65%] rounded-full bg-blue-500/10 blur-[130px] animate-pulse-slow" style="animation-delay: 2s;"></div>
        <div class="absolute top-[40%] left-[50%] -translate-x-1/2 -translate-y-1/2 w-[50%] h-[50%] rounded-full bg-cyan-500/5 blur-[100px] animate-pulse-slow" style="animation-delay: 4s;"></div>
        
        <!-- Floating Accent Orbs -->
        <div class="absolute top-[15%] left-[20%] w-72 h-72 rounded-full bg-gradient-to-tr from-purple-600/20 to-pink-600/10 blur-[60px] animate-orb-float-1"></div>
        <div class="absolute top-[60%] right-[15%] w-[350px] h-[350px] rounded-full bg-gradient-to-br from-blue-600/15 to-cyan-500/10 blur-[80px] animate-orb-float-2"></div>
        <div class="absolute bottom-[20%] left-[10%] w-80 h-80 rounded-full bg-gradient-to-tr from-cyan-600/10 to-purple-600/15 blur-[70px] animate-orb-float-3"></div>
        
        <!-- Particle Grid Overlay -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(120,119,198,0.15),rgba(255,255,255,0))]"></div>
        <div class="absolute inset-0 opacity-[0.03] bg-[linear-gradient(to_right,#808080_1px,transparent_1px),linear-gradient(to_bottom,#808080_1px,transparent_1px)] bg-[size:24px_24px]"></div>
    </div>
    
    <!-- Interactive Mouse-Follow Glow -->
    <div class="absolute pointer-events-none z-10 w-[400px] h-[400px] rounded-full bg-purple-600/5 blur-[100px] -translate-x-1/2 -translate-y-1/2 transition-all duration-300 ease-out hidden md:block"
         :style="'left: ' + mouseX + 'px; top: ' + mouseY + 'px;'"></div>

    <!-- Navigation Header -->
    <header class="relative z-50 max-w-7xl mx-auto px-6 h-24 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-purple-600 to-blue-500 flex items-center justify-center shadow-lg shadow-purple-500/20">
                <i data-lucide="activity" class="w-5 h-5 text-white"></i>
            </div>
            <span class="text-xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-neutral-400">PSGDC</span>
        </div>
        
        <nav class="hidden md:flex items-center gap-8 text-sm text-neutral-400 font-medium">
            <a href="#features" class="hover:text-white transition-colors duration-200">Features</a>
            <a href="#showcase" class="hover:text-white transition-colors duration-200">Showcase</a>
            <a href="#why-choose" class="hover:text-white transition-colors duration-200">Why Us</a>
            <a href="#testimonials" class="hover:text-white transition-colors duration-200">Reviews</a>
            <a href="#faqs" class="hover:text-white transition-colors duration-200">FAQs</a>
        </nav>
        
        <div>
            <a href="#download" class="relative group overflow-hidden px-5 py-2.5 rounded-full text-xs font-semibold tracking-wider uppercase border border-white/10 bg-white/5 hover:border-white/20 transition-all duration-300 block">
                <span class="relative z-10 text-white flex items-center gap-1.5">
                    Get App <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </span>
                <span class="absolute inset-0 bg-gradient-to-r from-purple-600/20 to-blue-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
            </a>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="relative z-20 max-w-7xl mx-auto px-6 pt-12 pb-24 md:py-32 grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
        <!-- Hero Text content -->
        <div class="lg:col-span-7 flex flex-col items-start hero-text">
            <!-- Promo Badge -->
            <div class="mb-6 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-purple-500/30 bg-purple-500/10 text-xs font-semibold text-purple-400 tracking-wide">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-500"></span>
                </span>
                Version 2.0 Now Available
            </div>
            
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight leading-[1.15] mb-6">
                Take Your Business <br/>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-purple-500 via-blue-500 to-cyan-400">
                    Anywhere
                </span>
            </h1>
            
            <p class="text-neutral-400 text-lg md:text-xl leading-relaxed max-w-xl mb-10">
                Manage invoices, payments, reports, customers, and business operations directly from your mobile device.
            </p>
            
            <!-- App Download Buttons -->
            <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto mb-16">
                <!-- App Store -->
                <a href="#" class="relative w-full sm:w-56 group overflow-hidden bg-white text-black py-4 px-6 rounded-2xl flex items-center justify-center gap-3 shadow-2xl shadow-white/5 transition-all duration-300 hover:scale-[1.03] active:scale-[0.98]">
                    <i data-lucide="apple" class="w-7 h-7 fill-black"></i>
                    <div class="text-left">
                        <div class="text-[10px] uppercase font-bold tracking-wider opacity-60 leading-none">Download on the</div>
                        <div class="text-base font-extrabold leading-tight">App Store</div>
                    </div>
                    <!-- Hover Ripple Layer -->
                    <span class="absolute inset-0 bg-neutral-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></span>
                </a>
                
                <!-- Google Play -->
                <a href="#" class="relative w-full sm:w-56 group overflow-hidden bg-neutral-900 border border-neutral-800 text-white py-4 px-6 rounded-2xl flex items-center justify-center gap-3 transition-all duration-300 hover:scale-[1.03] hover:border-neutral-700 active:scale-[0.98] shadow-lg shadow-black/40">
                    <svg class="w-6 h-6 fill-white" viewBox="0 0 24 24">
                        <path d="M5,3.14L15.3,13.43L5,23.73C4.6,23.53 4.3,23.13 4.3,22.6V4.28C4.3,3.75 4.6,3.34 5,3.14M16.71,14.85L19.46,16.43C20.15,16.83 20.15,17.7 19.46,18.1L16.71,19.68L14.16,17.13L16.71,14.85M5.72,2.5L16,12.78L13.43,15.35L5.72,2.5M16,13.07L18.75,14.65C19.44,15.05 19.44,15.92 18.75,16.32L16,17.9L13.72,15.62L16,13.07Z"/>
                    </svg>
                    <div class="text-left">
                        <div class="text-[10px] uppercase font-bold tracking-wider opacity-60 leading-none">Get it on</div>
                        <div class="text-base font-extrabold leading-tight">Google Play</div>
                    </div>
                    <!-- Hover Glow -->
                    <span class="absolute inset-0 bg-gradient-to-r from-purple-600/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></span>
                </a>
            </div>
            
            <!-- Animated Statistics Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 md:gap-10 border-t border-white/5 pt-10 w-full text-left" id="hero-stats">
                <div>
                    <div class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-white to-neutral-400" x-text="stats.downloads + 'k+'">0k+</div>
                    <div class="text-neutral-500 text-xs mt-1 uppercase tracking-wider font-semibold">Downloads</div>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-white to-neutral-400" x-text="stats.rating">0</div>
                    <div class="text-neutral-500 text-xs mt-1 uppercase tracking-wider font-semibold flex items-center gap-1">
                        Rating <i data-lucide="star" class="w-3.5 h-3.5 fill-yellow-500 text-yellow-500"></i>
                    </div>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-white to-neutral-400" x-text="stats.uptime + '%'">0%</div>
                    <div class="text-neutral-500 text-xs mt-1 uppercase tracking-wider font-semibold">Uptime</div>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-white to-neutral-400" x-text="stats.businesses + '+'">0+</div>
                    <div class="text-neutral-500 text-xs mt-1 uppercase tracking-wider font-semibold">Businesses</div>
                </div>
            </div>
        </div>
        
        <!-- Hero Display: 3D floating Mockups -->
        <div class="lg:col-span-5 relative h-[500px] sm:h-[650px] w-full flex items-center justify-center perspective-[1500px] hero-mockups">
            <!-- iOS Mockup (Front) -->
            <div class="absolute z-30 transform -rotate-y-[15deg] rotate-x-[5deg] hover:rotate-y-[5deg] hover:scale-[1.03] transition-all duration-700 ease-out translate-x-[-15%] sm:translate-x-[-25%] translate-y-[-5%]">
                <div class="w-[240px] h-[480px] sm:w-[280px] sm:h-[560px] rounded-[48px] p-3 bg-neutral-900 border-[6px] border-neutral-800 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.8)] relative overflow-hidden ring-1 ring-white/10">
                    <!-- Screen reflection highlight -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-white/0 via-white/5 to-white/10 pointer-events-none z-20"></div>
                    <!-- Notch -->
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-36 h-6 bg-neutral-900 rounded-b-2xl z-50 flex items-center justify-between px-5 py-1">
                        <div class="w-3.5 h-3.5 rounded-full bg-neutral-800"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-neutral-800"></div>
                    </div>
                    <!-- Screen App Content -->
                    <div class="w-full h-full rounded-[38px] bg-[#0C0C0E] overflow-hidden flex flex-col p-4 pt-8 text-left text-xs relative select-none">
                        <!-- App Status Bar -->
                        <div class="flex justify-between items-center text-[10px] text-neutral-400 mb-4 px-2">
                            <span>9:41</span>
                            <div class="flex gap-1">
                                <i data-lucide="wifi" class="w-3 h-3"></i>
                                <i data-lucide="battery" class="w-3.5 h-3.5"></i>
                            </div>
                        </div>
                        
                        <!-- Mini App Interface Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <div class="text-neutral-500 font-semibold text-[9px] uppercase">Welcome Back</div>
                                <div class="text-sm font-bold">Olivia Martin</div>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-purple-500/10 border border-purple-500/20 flex items-center justify-center">
                                <i data-lucide="user" class="w-4 h-4 text-purple-400"></i>
                            </div>
                        </div>
                        
                        <!-- Interactive Balance Widget -->
                        <div class="p-3.5 rounded-2xl bg-gradient-to-tr from-purple-600/30 to-blue-500/10 border border-purple-500/20 mb-4">
                            <span class="text-neutral-400 text-[9px] font-semibold uppercase">Total Balance</span>
                            <div class="text-lg font-black mt-0.5">$48,259.60</div>
                            <div class="text-[9px] text-emerald-400 font-semibold mt-1 flex items-center gap-0.5">
                                <i data-lucide="arrow-up-right" class="w-3 h-3"></i> +12.5% this month
                            </div>
                        </div>
                        
                        <!-- Mini Chart Widget -->
                        <div class="p-3 rounded-2xl bg-neutral-900/60 border border-white/5 mb-4 flex-1 flex flex-col justify-between">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-neutral-400 text-[9px] font-semibold uppercase">Analytics</span>
                                <span class="text-[8px] bg-neutral-800 px-1.5 py-0.5 rounded text-neutral-300">Live</span>
                            </div>
                            <div class="w-full h-16 flex items-end gap-1.5 pt-2">
                                <div class="w-full bg-neutral-800 rounded-t h-[40%]"></div>
                                <div class="w-full bg-neutral-800 rounded-t h-[60%]"></div>
                                <div class="w-full bg-gradient-to-t from-purple-600 to-blue-500 rounded-t h-[85%] animate-pulse"></div>
                                <div class="w-full bg-neutral-800 rounded-t h-[50%]"></div>
                                <div class="w-full bg-neutral-800 rounded-t h-[70%]"></div>
                                <div class="w-full bg-neutral-800 rounded-t h-[95%]"></div>
                            </div>
                        </div>
                        
                        <!-- Nav menu bar -->
                        <div class="flex justify-between pt-2 border-t border-white/5 text-neutral-500 text-[9px]">
                            <div class="flex flex-col items-center gap-0.5 text-purple-400">
                                <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i>
                                <span>Home</span>
                            </div>
                            <div class="flex flex-col items-center gap-0.5">
                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                <span>Invoices</span>
                            </div>
                            <div class="flex flex-col items-center gap-0.5">
                                <i data-lucide="users" class="w-3.5 h-3.5"></i>
                                <span>Clients</span>
                            </div>
                            <div class="flex flex-col items-center gap-0.5">
                                <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                                <span>Settings</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Android Mockup (Back-Offset) -->
            <div class="absolute z-20 transform rotate-y-[15deg] rotate-x-[5deg] hover:rotate-y-[5deg] hover:scale-[1.03] transition-all duration-700 ease-out translate-x-[25%] translate-y-[8%] opacity-70 hover:opacity-100">
                <div class="w-[230px] h-[460px] sm:w-[270px] sm:h-[540px] rounded-[44px] p-3 bg-neutral-900 border-[6px] border-neutral-800 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.8)] relative overflow-hidden ring-1 ring-white/10">
                    <!-- Notch hole -->
                    <div class="absolute top-4 left-1/2 -translate-x-1/2 w-3.5 h-3.5 rounded-full bg-neutral-950 z-50 ring-2 ring-neutral-800"></div>
                    <!-- Screen reflection highlight -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-white/0 via-white/5 to-white/10 pointer-events-none z-20"></div>
                    <!-- Screen App Content -->
                    <div class="w-full h-full rounded-[34px] bg-[#0A0A0B] overflow-hidden flex flex-col p-4 pt-8 text-left text-xs relative select-none">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-bold">Invoices</span>
                            <div class="w-7 h-7 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                                <i data-lucide="plus" class="w-4 h-4 text-blue-400"></i>
                            </div>
                        </div>
                        
                        <!-- Search Bar -->
                        <div class="p-2 rounded-xl bg-neutral-900 border border-white/5 text-[10px] text-neutral-500 flex items-center gap-2 mb-4">
                            <i data-lucide="search" class="w-3.5 h-3.5"></i>
                            <span>Search invoice...</span>
                        </div>
                        
                        <!-- Invoices list -->
                        <div class="flex-1 space-y-2.5 overflow-hidden">
                            <div class="p-2.5 rounded-xl bg-neutral-900/60 border border-white/5 flex justify-between items-center">
                                <div>
                                    <div class="font-semibold text-white">Acme Corp</div>
                                    <div class="text-[9px] text-neutral-500">Invoice #INV-2048</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-white">$1,200.00</div>
                                    <div class="text-[9px] text-emerald-400 font-semibold uppercase">Paid</div>
                                </div>
                            </div>
                            
                            <div class="p-2.5 rounded-xl bg-neutral-900/60 border border-white/5 flex justify-between items-center">
                                <div>
                                    <div class="font-semibold text-white">Stark Industries</div>
                                    <div class="text-[9px] text-neutral-500">Invoice #INV-2047</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-white">$4,850.00</div>
                                    <div class="text-[9px] text-amber-400 font-semibold uppercase">Pending</div>
                                </div>
                            </div>
                            
                            <div class="p-2.5 rounded-xl bg-neutral-900/60 border border-white/5 flex justify-between items-center">
                                <div>
                                    <div class="font-semibold text-white">Wayne Enterprises</div>
                                    <div class="text-[9px] text-neutral-500">Invoice #INV-2046</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-white">$950.00</div>
                                    <div class="text-[9px] text-emerald-400 font-semibold uppercase">Paid</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section id="features" class="relative z-20 max-w-7xl mx-auto px-6 py-24 border-t border-white/5">
        <div class="text-center max-w-2xl mx-auto mb-20 section-header">
            <h2 class="text-xs uppercase font-extrabold tracking-widest text-purple-500 mb-3">Feature Highlights</h2>
            <p class="text-3xl md:text-5xl font-extrabold tracking-tight">Everything you need to scale</p>
            <p class="text-neutral-400 text-base md:text-lg mt-4 leading-relaxed">
                We've built state-of-the-art tools right inside the app to simplify your daily workspace.
            </p>
        </div>
        
        <!-- Feature Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
            <!-- 1. Smart Dashboard (Large 7 columns) -->
            <div class="md:col-span-7 glass-card rounded-3xl p-8 md:p-10 flex flex-col justify-between overflow-hidden relative group glow-border feature-card">
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-purple-500/10 to-transparent blur-3xl rounded-full pointer-events-none"></div>
                <div class="mb-10">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center mb-6">
                        <i data-lucide="layout-dashboard" class="w-6 h-6 text-purple-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Smart Dashboard</h3>
                    <p class="text-neutral-400 leading-relaxed max-w-md">
                        Get direct, real-time insights into your sales volume, outstanding invoices, and customer growth trends in one beautiful control panel.
                    </p>
                </div>
                <!-- Interactive Chart Widget Preview -->
                <div class="bg-neutral-950/60 border border-white/5 rounded-2xl p-6 relative w-full overflow-hidden self-end">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex gap-2 items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                            <span class="text-xs font-semibold">Monthly Sales Volume</span>
                        </div>
                        <span class="text-xs font-bold text-neutral-400">$24,950</span>
                    </div>
                    <!-- Chart line -->
                    <div class="w-full h-32 flex items-end justify-between gap-2">
                        <div class="w-full bg-neutral-900 rounded-t h-[20%] transition-all duration-500 group-hover:h-[40%]"></div>
                        <div class="w-full bg-neutral-900 rounded-t h-[35%] transition-all duration-500 group-hover:h-[55%]"></div>
                        <div class="w-full bg-neutral-900 rounded-t h-[30%] transition-all duration-500 group-hover:h-[45%]"></div>
                        <div class="w-full bg-gradient-to-t from-purple-600/40 to-purple-500 rounded-t h-[75%] transition-all duration-500 group-hover:h-[85%]"></div>
                        <div class="w-full bg-neutral-900 rounded-t h-[50%] transition-all duration-500 group-hover:h-[65%]"></div>
                        <div class="w-full bg-gradient-to-t from-purple-600/40 to-blue-500 rounded-t h-[90%] transition-all duration-500 group-hover:h-[95%]"></div>
                    </div>
                </div>
            </div>

            <!-- 2. Invoice Management (5 columns) -->
            <div class="md:col-span-5 glass-card rounded-3xl p-8 md:p-10 flex flex-col justify-between overflow-hidden relative group glow-border feature-card">
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-blue-500/10 to-transparent blur-3xl rounded-full pointer-events-none"></div>
                <div class="mb-10">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center mb-6">
                        <i data-lucide="file-spreadsheet" class="w-6 h-6 text-blue-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Invoice Management</h3>
                    <p class="text-neutral-400 leading-relaxed">
                        Create, format, and deliver professional receipts and invoices directly from customer profiles in seconds.
                    </p>
                </div>
                <!-- Mini invoice widget -->
                <div class="bg-neutral-950/60 border border-white/5 rounded-2xl p-5 w-full mt-4 transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                    <div class="flex justify-between items-center text-xs font-semibold pb-3 border-b border-white/5 mb-3 text-neutral-400">
                        <span>Invoice #INV-928</span>
                        <span class="text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded-full text-[10px]">Active</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-[10px] text-neutral-500 block uppercase">Client</span>
                            <span class="text-xs font-bold">Stark Labs</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-neutral-500 block uppercase">Total Due</span>
                            <span class="text-xs font-bold text-white">$450.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Instant Notifications (5 columns) -->
            <div class="md:col-span-5 glass-card rounded-3xl p-8 md:p-10 flex flex-col justify-between overflow-hidden relative group glow-border feature-card">
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-cyan-500/10 to-transparent blur-3xl rounded-full pointer-events-none"></div>
                <div class="mb-10">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center mb-6">
                        <i data-lucide="bell" class="w-6 h-6 text-cyan-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Instant Notifications</h3>
                    <p class="text-neutral-400 leading-relaxed">
                        Stay up to date with customized alerts. Get pinged the moment an invoice gets paid or when inventory drops.
                    </p>
                </div>
                <!-- Push animation simulation -->
                <div class="space-y-2 mt-4">
                    <div class="bg-neutral-950/60 border border-white/5 rounded-xl p-3 flex items-center gap-3 transform translate-x-4 group-hover:translate-x-0 transition-transform duration-300">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold">Payment Received</div>
                            <div class="text-[10px] text-neutral-400">Olivia paid invoice #INV-927</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Reports & Analytics (7 columns) -->
            <div class="md:col-span-7 glass-card rounded-3xl p-8 md:p-10 flex flex-col justify-between overflow-hidden relative group glow-border feature-card">
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-purple-500/10 to-transparent blur-3xl rounded-full pointer-events-none"></div>
                <div class="mb-10">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center mb-6">
                        <i data-lucide="trending-up" class="w-6 h-6 text-purple-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Reports & Analytics</h3>
                    <p class="text-neutral-400 leading-relaxed max-w-md">
                        Generate comprehensive PDF or CSV summaries right inside the app, and filter reports by product, client, or date ranges.
                    </p>
                </div>
                <!-- Data visualization simulation -->
                <div class="bg-neutral-950/60 border border-white/5 rounded-2xl p-6 grid grid-cols-2 gap-4 items-center mt-4 w-full">
                    <div>
                        <div class="text-[10px] text-neutral-500 uppercase font-semibold">User Retention</div>
                        <div class="text-xl font-black mt-1">98.2%</div>
                        <div class="text-[9px] text-emerald-400 font-semibold mt-1">+2.4% vs last week</div>
                    </div>
                    <div class="h-14 flex items-end justify-end gap-1">
                        <div class="w-3 bg-purple-500/30 rounded h-[30%]"></div>
                        <div class="w-3 bg-purple-500/50 rounded h-[50%]"></div>
                        <div class="w-3 bg-purple-500/70 rounded h-[70%]"></div>
                        <div class="w-3 bg-purple-500 rounded h-[90%]"></div>
                    </div>
                </div>
            </div>

            <!-- 5. Offline Access (Full 12 columns width for premium callout) -->
            <div class="md:col-span-12 glass-card rounded-3xl p-8 md:p-12 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center overflow-hidden relative group glow-border feature-card">
                <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-bl from-cyan-500/10 to-transparent blur-3xl rounded-full pointer-events-none"></div>
                <div>
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center mb-6">
                        <i data-lucide="cloud-off" class="w-6 h-6 text-cyan-400"></i>
                    </div>
                    <h3 class="text-2xl md:text-3xl font-bold mb-3">Offline Access</h3>
                    <p class="text-neutral-400 leading-relaxed">
                        Don't worry about bad cellular network coverage. View customer files, draft invoices, and track workflows locally. The app automatically syncs once you're back online.
                    </p>
                </div>
                <div class="flex items-center justify-center relative">
                    <!-- Rotating Sync / Offline Visual Widget -->
                    <div class="w-48 h-48 rounded-full border border-dashed border-white/10 flex items-center justify-center animate-spin" style="animation-duration: 20s;">
                        <div class="w-36 h-36 rounded-full border border-white/5 flex items-center justify-center"></div>
                    </div>
                    <div class="absolute w-24 h-24 rounded-3xl bg-neutral-900 border border-white/10 flex flex-col items-center justify-center shadow-2xl">
                        <i data-lucide="wifi-off" class="w-8 h-8 text-neutral-400 mb-1 animate-pulse"></i>
                        <span class="text-[9px] text-neutral-400 font-bold uppercase">Offline Mode</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- APP SCREEN SHOWCASE (CAROUSEL) -->
    <section id="showcase" class="relative z-20 py-24 bg-gradient-to-b from-transparent via-neutral-950/20 to-transparent">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-20 section-header">
                <h2 class="text-xs uppercase font-extrabold tracking-widest text-blue-500 mb-3">App Showcase</h2>
                <p class="text-3xl md:text-5xl font-extrabold tracking-tight">Designed for the palm of your hand</p>
                <p class="text-neutral-400 text-base md:text-lg mt-4 leading-relaxed">
                    Take a tour through our clean, pixel-perfect user interface designs.
                </p>
            </div>
            
            <!-- Carousel Container (Alpine.js) -->
            <div class="relative max-w-md mx-auto" x-data="carousel()" x-init="startAutoplay()" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
                <!-- Phone Frame Wrapper -->
                <div class="w-[280px] sm:w-[320px] h-[560px] sm:h-[640px] rounded-[48px] p-3 bg-neutral-900 border-[6px] border-neutral-800 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.8)] relative overflow-hidden ring-1 ring-white/10 mx-auto">
                    <!-- Notch -->
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-36 h-6 bg-neutral-900 rounded-b-2xl z-50"></div>
                    <!-- Screen reflection highlight -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-white/0 via-white/5 to-white/10 pointer-events-none z-20"></div>
                    
                    <!-- Carousel Slides Container -->
                    <div class="w-full h-full rounded-[38px] bg-[#0A0A0C] overflow-hidden relative">
                        <!-- Slides -->
                        <template x-for="(slide, index) in slides" :key="index">
                            <div x-show="activeSlide === index" 
                                 x-transition:enter="transition ease-out duration-500 transform"
                                 x-transition:enter-start="opacity-0 translate-x-full scale-95"
                                 x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                                 x-transition:leave="transition ease-in duration-400 transform absolute top-0 left-0 w-full h-full"
                                 x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                                 x-transition:leave-end="opacity-0 -translate-x-full scale-95"
                                 class="w-full h-full p-6 pt-10 flex flex-col text-left justify-between select-none">
                                
                                <div>
                                    <!-- Screen Title -->
                                    <div class="flex items-center justify-between mb-6">
                                        <span class="text-[10px] tracking-wider uppercase font-bold text-neutral-500" x-text="slide.subtitle">Subtitle</span>
                                        <div class="w-7 h-7 rounded-lg bg-white/5 flex items-center justify-center">
                                            <i :class="'w-4.5 h-4.5 text-' + slide.color" :data-lucide="slide.icon"></i>
                                        </div>
                                    </div>
                                    
                                    <h4 class="text-xl font-bold mb-2 text-white" x-text="slide.title">Title</h4>
                                    <p class="text-neutral-400 text-xs leading-relaxed" x-text="slide.desc">Description</p>
                                </div>
                                
                                <!-- Screen Mock Visual Content -->
                                <div class="bg-neutral-900/60 border border-white/5 rounded-2xl p-4 flex-1 mt-6 flex flex-col justify-center">
                                    <!-- Render specific design components based on current slide -->
                                    <!-- Slide 0: Dashboard -->
                                    <template x-if="index === 0">
                                        <div class="space-y-3">
                                            <div class="bg-purple-600/10 border border-purple-500/20 rounded-xl p-3 flex justify-between items-center">
                                                <div>
                                                    <span class="text-[8px] text-purple-300 font-bold block uppercase">Net Profit</span>
                                                    <span class="text-sm font-bold">$12,490.00</span>
                                                </div>
                                                <i data-lucide="arrow-up-right" class="w-4 h-4 text-purple-400"></i>
                                            </div>
                                            <div class="bg-white/5 border border-white/5 rounded-xl p-3 flex justify-between items-center">
                                                <div>
                                                    <span class="text-[8px] text-neutral-400 block uppercase">Active Users</span>
                                                    <span class="text-sm font-bold">1,824</span>
                                                </div>
                                                <i data-lucide="users" class="w-4 h-4 text-neutral-400"></i>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <!-- Slide 1: Invoice -->
                                    <template x-if="index === 1">
                                        <div class="space-y-2.5">
                                            <div class="flex justify-between items-center text-[10px] border-b border-white/5 pb-2">
                                                <span class="text-neutral-400">Invoice #INV-2900</span>
                                                <span class="text-amber-400 font-bold">Pending</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-neutral-500 text-[9px]">To: Banner Inc.</span>
                                                <span class="text-sm font-bold text-white">$2,500.00</span>
                                            </div>
                                            <div class="w-full bg-blue-600 py-2 rounded-xl text-center text-[10px] font-bold text-white mt-2 cursor-pointer hover:bg-blue-500 transition-colors">
                                                Send Reminder
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <!-- Slide 2: Customers -->
                                    <template x-if="index === 2">
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-2.5 p-2 bg-white/5 rounded-xl">
                                                <div class="w-8 h-8 rounded-full bg-cyan-600/20 flex items-center justify-center text-cyan-400 font-bold text-xs">JD</div>
                                                <div>
                                                    <div class="text-[10px] font-bold">John Doe</div>
                                                    <div class="text-[8px] text-neutral-500">john@example.com</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2.5 p-2 bg-white/5 rounded-xl">
                                                <div class="w-8 h-8 rounded-full bg-purple-600/20 flex items-center justify-center text-purple-400 font-bold text-xs">AS</div>
                                                <div>
                                                    <div class="text-[10px] font-bold">Alice Smith</div>
                                                    <div class="text-[8px] text-neutral-500">alice@example.com</div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <!-- Slide 3: Reports -->
                                    <template x-if="index === 3">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 rounded-full border-[6px] border-purple-500/20 border-t-purple-600 flex items-center justify-center mb-2 animate-spin" style="animation-duration: 3s;">
                                                <i data-lucide="trending-up" class="w-5 h-5 text-purple-400 transform -rotate-45"></i>
                                            </div>
                                            <span class="text-[9px] text-neutral-400 uppercase font-bold">Exporting CSV...</span>
                                        </div>
                                    </template>
                                    
                                    <!-- Slide 4: Settings -->
                                    <template x-if="index === 4">
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between p-2 bg-neutral-900 border border-white/5 rounded-xl">
                                                <span class="text-[9px] font-semibold text-neutral-300">Biometric Login</span>
                                                <div class="w-8 h-4 rounded-full bg-purple-600 flex items-center justify-end px-0.5">
                                                    <div class="w-3 h-3 bg-white rounded-full"></div>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between p-2 bg-neutral-900 border border-white/5 rounded-xl">
                                                <span class="text-[9px] font-semibold text-neutral-300">Secure Backup</span>
                                                <div class="w-8 h-4 rounded-full bg-purple-600 flex items-center justify-end px-0.5">
                                                    <div class="w-3 h-3 bg-white rounded-full"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                
                                <!-- Decorative UI indicators -->
                                <div class="flex justify-between items-center text-[10px] text-neutral-500 pt-4 border-t border-white/5">
                                    <span>Swipe / Tap dots</span>
                                    <span class="font-semibold text-neutral-400" x-text="(index + 1) + ' of ' + slides.length"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Left/Right Arrow Buttons (Absolute position layout outside mockup) -->
                <button @click="prevSlide()" class="absolute left-[-50px] top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/5 border border-white/10 hover:border-white/20 flex items-center justify-center text-white/75 hover:text-white transition-all duration-200">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </button>
                <button @click="nextSlide()" class="absolute right-[-50px] top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/5 border border-white/10 hover:border-white/20 flex items-center justify-center text-white/75 hover:text-white transition-all duration-200">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </button>
                
                <!-- Slide Indicators (Dots) -->
                <div class="flex justify-center gap-2.5 mt-8">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button @click="setSlide(index)"
                                class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                                :class="activeSlide === index ? 'bg-purple-500 w-8' : 'bg-neutral-800 hover:bg-neutral-700'"></button>
                    </template>
                </div>
            </div>
        </div>
    </section>

    <!-- WHY CHOOSE OUR APP -->
    <section id="why-choose" class="relative z-20 max-w-7xl mx-auto px-6 py-24 border-t border-white/5">
        <div class="text-center max-w-2xl mx-auto mb-20 section-header">
            <h2 class="text-xs uppercase font-extrabold tracking-widest text-cyan-500 mb-3">Why Choose Us</h2>
            <p class="text-3xl md:text-5xl font-extrabold tracking-tight">Built to outperform</p>
            <p class="text-neutral-400 text-base md:text-lg mt-4 leading-relaxed">
                We design and build features that solve your workflows directly, keeping page loads light and performance fast.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1: Offline Mode -->
            <div class="glass-card rounded-3xl p-8 hover:translate-y-[-4px] transition-transform duration-300 select-none glow-border flex items-start gap-5 why-card">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center flex-shrink-0 text-purple-400">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-2">Offline Mode</h4>
                    <p class="text-neutral-400 text-sm leading-relaxed">Draft reports and access business information anywhere, even without cellular connection.</p>
                </div>
            </div>
            
            <!-- Feature 2: Real-time Sync -->
            <div class="glass-card rounded-3xl p-8 hover:translate-y-[-4px] transition-transform duration-300 select-none glow-border flex items-start gap-5 why-card">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center flex-shrink-0 text-blue-400">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-2">Real-time Sync</h4>
                    <p class="text-neutral-400 text-sm leading-relaxed">All transaction history and changes update instantly across devices as soon as you connect.</p>
                </div>
            </div>
            
            <!-- Feature 3: Advanced Reports -->
            <div class="glass-card rounded-3xl p-8 hover:translate-y-[-4px] transition-transform duration-300 select-none glow-border flex items-start gap-5 why-card">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center flex-shrink-0 text-cyan-400">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-2">Advanced Reports</h4>
                    <p class="text-neutral-400 text-sm leading-relaxed">Export beautiful charts and CSV data with highly customizable search parameter filter sets.</p>
                </div>
            </div>
            
            <!-- Feature 4: Multi-device Access -->
            <div class="glass-card rounded-3xl p-8 hover:translate-y-[-4px] transition-transform duration-300 select-none glow-border flex items-start gap-5 why-card">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center flex-shrink-0 text-cyan-400">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-2">Multi-device Access</h4>
                    <p class="text-neutral-400 text-sm leading-relaxed">Runs perfectly and coordinates across both iPhones, iPads, and Android devices natively.</p>
                </div>
            </div>
            
            <!-- Feature 5: Fast Performance -->
            <div class="glass-card rounded-3xl p-8 hover:translate-y-[-4px] transition-transform duration-300 select-none glow-border flex items-start gap-5 why-card">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center flex-shrink-0 text-purple-400">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-2">Fast Performance</h4>
                    <p class="text-neutral-400 text-sm leading-relaxed">Optimized to render screens rapidly under minimal local memory load footprint.</p>
                </div>
            </div>
            
            <!-- Feature 6: Secure Cloud Backup -->
            <div class="glass-card rounded-3xl p-8 hover:translate-y-[-4px] transition-transform duration-300 select-none glow-border flex items-start gap-5 why-card">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center flex-shrink-0 text-blue-400">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-2">Secure Cloud Backup</h4>
                    <p class="text-neutral-400 text-sm leading-relaxed">Rest assured that customer balances and transactions are heavily encrypted and backed up.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS SECTION (Marquee reviews) -->
    <section id="testimonials" class="relative z-20 py-24 border-t border-white/5 overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 mb-16 text-center section-header">
            <h2 class="text-xs uppercase font-extrabold tracking-widest text-purple-500 mb-3">Customer Reviews</h2>
            <p class="text-3xl md:text-5xl font-extrabold tracking-tight">Loved by builders worldwide</p>
        </div>
        
        <!-- Infinite Marquee Scroll wrapper -->
        <div class="relative w-full flex items-center overflow-hidden py-4">
            <!-- Left & Right gradients to fade reviews in and out -->
            <div class="absolute left-0 top-0 bottom-0 w-24 bg-gradient-to-r from-darkBg to-transparent z-10 pointer-events-none"></div>
            <div class="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-darkBg to-transparent z-10 pointer-events-none"></div>
            
            <!-- Double list for infinite loop effect -->
            <div class="flex gap-6 animate-marquee whitespace-nowrap hover:[animation-play-state:paused]">
                <!-- Item 1 -->
                <div class="glass-card rounded-2xl p-6 w-[350px] inline-flex flex-col justify-between whitespace-normal select-none">
                    <div class="flex gap-1 mb-4">
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                    </div>
                    <p class="text-neutral-300 text-sm leading-relaxed mb-6">
                        "The offline support on this application is a absolute lifesaver. I can work on clients in rural areas without losing any data."
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-purple-600/20 flex items-center justify-center font-bold text-xs text-purple-400">AM</div>
                        <div>
                            <div class="text-xs font-bold text-white">Alexander Miller</div>
                            <div class="text-[9px] text-neutral-500">CEO, Miller Logistics</div>
                        </div>
                    </div>
                </div>
                
                <!-- Item 2 -->
                <div class="glass-card rounded-2xl p-6 w-[350px] inline-flex flex-col justify-between whitespace-normal select-none">
                    <div class="flex gap-1 mb-4">
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                    </div>
                    <p class="text-neutral-300 text-sm leading-relaxed mb-6">
                        "Invoicing takes less than 10 seconds now. Highly recommended for any growing digital or local business storefront."
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-blue-600/20 flex items-center justify-center font-bold text-xs text-blue-400">SL</div>
                        <div>
                            <div class="text-xs font-bold text-white">Sarah Lopez</div>
                            <div class="text-[9px] text-neutral-500">Founder, Spark Labs</div>
                        </div>
                    </div>
                </div>
                
                <!-- Item 3 -->
                <div class="glass-card rounded-2xl p-6 w-[350px] inline-flex flex-col justify-between whitespace-normal select-none">
                    <div class="flex gap-1 mb-4">
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                    </div>
                    <p class="text-neutral-300 text-sm leading-relaxed mb-6">
                        "Stunning UX layout design. Transition animations are incredibly premium and smooth. Flows like butter."
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-cyan-600/20 flex items-center justify-center font-bold text-xs text-cyan-400">DK</div>
                        <div>
                            <div class="text-xs font-bold text-white">Daniel Kim</div>
                            <div class="text-[9px] text-neutral-500">Product Lead, WebFlow</div>
                        </div>
                    </div>
                </div>

                <!-- Duplicate Items for Seamless Loop -->
                <!-- Item 1 duplicate -->
                <div class="glass-card rounded-2xl p-6 w-[350px] inline-flex flex-col justify-between whitespace-normal select-none">
                    <div class="flex gap-1 mb-4">
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                    </div>
                    <p class="text-neutral-300 text-sm leading-relaxed mb-6">
                        "The offline support on this application is a absolute lifesaver. I can work on clients in rural areas without losing any data."
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-purple-600/20 flex items-center justify-center font-bold text-xs text-purple-400">AM</div>
                        <div>
                            <div class="text-xs font-bold text-white">Alexander Miller</div>
                            <div class="text-[9px] text-neutral-500">CEO, Miller Logistics</div>
                        </div>
                    </div>
                </div>
                
                <!-- Item 2 duplicate -->
                <div class="glass-card rounded-2xl p-6 w-[350px] inline-flex flex-col justify-between whitespace-normal select-none">
                    <div class="flex gap-1 mb-4">
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                    </div>
                    <p class="text-neutral-300 text-sm leading-relaxed mb-6">
                        "Invoicing takes less than 10 seconds now. Highly recommended for any growing digital or local business storefront."
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-blue-600/20 flex items-center justify-center font-bold text-xs text-blue-400">SL</div>
                        <div>
                            <div class="text-xs font-bold text-white">Sarah Lopez</div>
                            <div class="text-[9px] text-neutral-500">Founder, Spark Labs</div>
                        </div>
                    </div>
                </div>
                
                <!-- Item 3 duplicate -->
                <div class="glass-card rounded-2xl p-6 w-[350px] inline-flex flex-col justify-between whitespace-normal select-none">
                    <div class="flex gap-1 mb-4">
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                        <i data-lucide="star" class="w-4 h-4 fill-yellow-500 text-yellow-500"></i>
                    </div>
                    <p class="text-neutral-300 text-sm leading-relaxed mb-6">
                        "Stunning UX layout design. Transition animations are incredibly premium and smooth. Flows like butter."
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-cyan-600/20 flex items-center justify-center font-bold text-xs text-cyan-400">DK</div>
                        <div>
                            <div class="text-xs font-bold text-white">Daniel Kim</div>
                            <div class="text-[9px] text-neutral-500">Product Lead, WebFlow</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- APP RATINGS SECTION -->
    <section class="relative z-20 max-w-7xl mx-auto px-6 py-24 border-t border-white/5">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 text-center ratings-section">
            <!-- App Store Rating -->
            <div class="glass-card rounded-3xl p-8 flex flex-col items-center justify-center">
                <!-- SVG Circle Indicator -->
                <div class="relative w-28 h-28 flex items-center justify-center mb-4">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="56" cy="56" r="46" stroke="rgba(255,255,255,0.05)" stroke-width="8" fill="transparent"/>
                        <circle cx="56" cy="56" r="46" stroke="#7C3AED" stroke-width="8" fill="transparent"
                                stroke-dasharray="289" stroke-dashoffset="5.7" class="transition-all duration-1000 ease-out"/>
                    </svg>
                    <span class="absolute text-xl font-bold text-white">4.9</span>
                </div>
                <h4 class="text-sm font-semibold text-neutral-400 uppercase tracking-wider">App Store Rating</h4>
                <div class="flex gap-0.5 mt-2">
                    <i data-lucide="star" class="w-4 h-4 fill-purple-500 text-purple-500"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-purple-500 text-purple-500"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-purple-500 text-purple-500"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-purple-500 text-purple-500"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-purple-500 text-purple-500"></i>
                </div>
            </div>
            
            <!-- Google Play Rating -->
            <div class="glass-card rounded-3xl p-8 flex flex-col items-center justify-center">
                <div class="relative w-28 h-28 flex items-center justify-center mb-4">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="56" cy="56" r="46" stroke="rgba(255,255,255,0.05)" stroke-width="8" fill="transparent"/>
                        <circle cx="56" cy="56" r="46" stroke="#2563EB" stroke-width="8" fill="transparent"
                                stroke-dasharray="289" stroke-dashoffset="11.5" class="transition-all duration-1000 ease-out"/>
                    </svg>
                    <span class="absolute text-xl font-bold text-white">4.8</span>
                </div>
                <h4 class="text-sm font-semibold text-neutral-400 uppercase tracking-wider">Google Play Rating</h4>
                <div class="flex gap-0.5 mt-2">
                    <i data-lucide="star" class="w-4 h-4 fill-blue-500 text-blue-500"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-blue-500 text-blue-500"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-blue-500 text-blue-500"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-blue-500 text-blue-500"></i>
                    <i data-lucide="star" class="w-4 h-4 fill-blue-500 text-blue-500"></i>
                </div>
            </div>
            
            <!-- Total Downloads -->
            <div class="glass-card rounded-3xl p-8 flex flex-col items-center justify-center">
                <div class="relative w-28 h-28 flex items-center justify-center mb-4">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="56" cy="56" r="46" stroke="rgba(255,255,255,0.05)" stroke-width="8" fill="transparent"/>
                        <circle cx="56" cy="56" r="46" stroke="#06B6D4" stroke-width="8" fill="transparent"
                                stroke-dasharray="289" stroke-dashoffset="0" class="transition-all duration-1000 ease-out"/>
                    </svg>
                    <span class="absolute text-xl font-bold text-white">50k+</span>
                </div>
                <h4 class="text-sm font-semibold text-neutral-400 uppercase tracking-wider">Total Downloads</h4>
                <span class="text-xs text-neutral-500 mt-2">Active installations</span>
            </div>
            
            <!-- User Satisfaction -->
            <div class="glass-card rounded-3xl p-8 flex flex-col items-center justify-center">
                <div class="relative w-28 h-28 flex items-center justify-center mb-4">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="56" cy="56" r="46" stroke="rgba(255,255,255,0.05)" stroke-width="8" fill="transparent"/>
                        <circle cx="56" cy="56" r="46" stroke="#7C3AED" stroke-width="8" fill="transparent"
                                stroke-dasharray="289" stroke-dashoffset="2.9" class="transition-all duration-1000 ease-out"/>
                    </svg>
                    <span class="absolute text-xl font-bold text-white">99%</span>
                </div>
                <h4 class="text-sm font-semibold text-neutral-400 uppercase tracking-wider">User Satisfaction</h4>
                <span class="text-xs text-neutral-500 mt-2">Five-star reviews</span>
            </div>
        </div>
    </section>

    <!-- FAQ SECTION (MUTUALLY EXCLUSIVE DETAILS TAGS) -->
    <section id="faqs" class="relative z-20 max-w-4xl mx-auto px-6 py-24 border-t border-white/5">
        <div class="text-center mb-16 section-header">
            <h2 class="text-xs uppercase font-extrabold tracking-widest text-purple-500 mb-3">Frequently Asked Questions</h2>
            <p class="text-3xl md:text-5xl font-extrabold tracking-tight">Got Questions?</p>
        </div>
        
        <!-- FAQ Accordion List -->
        <div class="space-y-4">
            <!-- Q1 -->
            <details name="faq-accordion" class="group glass-card rounded-2xl overflow-hidden transition-all duration-300">
                <summary class="flex justify-between items-center p-6 text-base font-bold cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                    <span>What platforms are supported by the mobile app?</span>
                    <span class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center border border-white/5 group-open:rotate-180 transition-transform duration-300">
                        <i data-lucide="chevron-down" class="w-4 h-4 text-neutral-400"></i>
                    </span>
                </summary>
                <div class="px-6 pb-6 text-sm text-neutral-400 leading-relaxed border-t border-white/5 pt-4">
                    We fully support iOS (v15.0 or later) and Android (v8.0 Oreo or later) mobile operating systems. Tablet views for iPads and Android tablets are also optimized and available.
                </div>
            </details>
            
            <!-- Q2 -->
            <details name="faq-accordion" class="group glass-card rounded-2xl overflow-hidden transition-all duration-300">
                <summary class="flex justify-between items-center p-6 text-base font-bold cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                    <span>Does the app work without internet access?</span>
                    <span class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center border border-white/5 group-open:rotate-180 transition-transform duration-300">
                        <i data-lucide="chevron-down" class="w-4 h-4 text-neutral-400"></i>
                    </span>
                </summary>
                <div class="px-6 pb-6 text-sm text-neutral-400 leading-relaxed border-t border-white/5 pt-4">
                    Yes! The app saves key user records, customer databases, and draft invoices locally. Once your device reconnects to Wi-Fi or cellular networks, your updates will sync automatically to the cloud.
                </div>
            </details>
            
            <!-- Q3 -->
            <details name="faq-accordion" class="group glass-card rounded-2xl overflow-hidden transition-all duration-300">
                <summary class="flex justify-between items-center p-6 text-base font-bold cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                    <span>How secure is my business data?</span>
                    <span class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center border border-white/5 group-open:rotate-180 transition-transform duration-300">
                        <i data-lucide="chevron-down" class="w-4 h-4 text-neutral-400"></i>
                    </span>
                </summary>
                <div class="px-6 pb-6 text-sm text-neutral-400 leading-relaxed border-t border-white/5 pt-4">
                    Data protection is our absolute top priority. All database queries, sync states, and document storage use standard AES-256 end-to-end encryption schemas.
                </div>
            </details>
            
            <!-- Q4 -->
            <details name="faq-accordion" class="group glass-card rounded-2xl overflow-hidden transition-all duration-300">
                <summary class="flex justify-between items-center p-6 text-base font-bold cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                    <span>Is there a web platform sync option?</span>
                    <span class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center border border-white/5 group-open:rotate-180 transition-transform duration-300">
                        <i data-lucide="chevron-down" class="w-4 h-4 text-neutral-400"></i>
                    </span>
                </summary>
                <div class="px-6 pb-6 text-sm text-neutral-400 leading-relaxed border-t border-white/5 pt-4">
                    Absolutely. Your account syncs seamlessly with our main PSGDC website dashboard, so you can manage your operations from both the desktop web version and your mobile app simultaneously.
                </div>
            </details>
        </div>
    </section>

    <!-- CALL TO ACTION (CTA) SECTION -->
    <section id="download" class="relative z-20 max-w-6xl mx-auto px-6 py-24 cta-section">
        <div class="glass-card rounded-[40px] p-8 md:p-20 text-center relative overflow-hidden glow-border">
            <!-- Glow background overlay -->
            <div class="absolute inset-0 bg-gradient-to-tr from-purple-600/10 via-blue-600/10 to-transparent pointer-events-none"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-purple-500/10 rounded-full blur-[100px] pointer-events-none"></div>
            
            <h2 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6">
                Download Today. <br/>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-purple-500 via-blue-500 to-cyan-400">
                    Grow Faster Tomorrow.
                </span>
            </h2>
            
            <p class="text-neutral-400 text-base md:text-lg max-w-lg mx-auto mb-12 leading-relaxed">
                Available now on iPhone and Android. Create your account and get started in less than two minutes.
            </p>
            
            <!-- App Store & Google Play buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 max-w-md mx-auto">
                <a href="#" class="w-full sm:w-52 py-3.5 px-6 rounded-2xl bg-white text-black font-extrabold flex items-center justify-center gap-3 transition-transform duration-200 hover:scale-[1.03] active:scale-[0.98]">
                    <i data-lucide="apple" class="w-6 h-6 fill-black"></i>
                    <span>App Store</span>
                </a>
                <a href="#" class="w-full sm:w-52 py-3.5 px-6 rounded-2xl bg-neutral-900 border border-neutral-800 text-white font-extrabold flex items-center justify-center gap-3 transition-all duration-200 hover:scale-[1.03] hover:border-neutral-700 active:scale-[0.98]">
                    <i data-lucide="play" class="w-5 h-5 fill-white text-white"></i>
                    <span>Google Play</span>
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="relative z-20 border-t border-white/5 bg-neutral-950/40">
        <div class="max-w-7xl mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-4 gap-10">
            <!-- Column 1: Logo & Tagline -->
            <div class="flex flex-col items-start gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-purple-600 to-blue-500 flex items-center justify-center">
                        <i data-lucide="activity" class="w-4.5 h-4.5 text-white"></i>
                    </div>
                    <span class="text-lg font-bold tracking-tight">PSGDC</span>
                </div>
                <p class="text-neutral-500 text-xs leading-relaxed max-w-[200px]">
                    Manage invoices, operations, and customers from the palm of your hand.
                </p>
            </div>
            
            <!-- Column 2: Product links -->
            <div>
                <h5 class="text-xs uppercase font-extrabold tracking-wider text-neutral-400 mb-4">Product</h5>
                <ul class="space-y-2.5 text-xs text-neutral-500">
                    <li><a href="#features" class="hover:text-white transition-colors duration-200">Features</a></li>
                    <li><a href="#showcase" class="hover:text-white transition-colors duration-200">Showcase</a></li>
                    <li><a href="#download" class="hover:text-white transition-colors duration-200">Download app</a></li>
                </ul>
            </div>
            
            <!-- Column 3: Resources -->
            <div>
                <h5 class="text-xs uppercase font-extrabold tracking-wider text-neutral-400 mb-4">Support</h5>
                <ul class="space-y-2.5 text-xs text-neutral-500">
                    <li><a href="#" class="hover:text-white transition-colors duration-200">Documentation</a></li>
                    <li><a href="#" class="hover:text-white transition-colors duration-200">Contact Help</a></li>
                    <li><a href="#" class="hover:text-white transition-colors duration-200">System Status</a></li>
                </ul>
            </div>
            
            <!-- Column 4: Legal & Socials -->
            <div>
                <h5 class="text-xs uppercase font-extrabold tracking-wider text-neutral-400 mb-4">Legal</h5>
                <ul class="space-y-2.5 text-xs text-neutral-500 mb-6">
                    <li><a href="/coupon-terms" class="hover:text-white transition-colors duration-200">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-white transition-colors duration-200">Privacy Policy</a></li>
                </ul>
                <div class="flex gap-4">
                    <a href="#" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-neutral-400 hover:text-white transition-colors">
                        <i data-lucide="twitter" class="w-4 h-4"></i>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-neutral-400 hover:text-white transition-colors">
                        <i data-lucide="instagram" class="w-4 h-4"></i>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-neutral-400 hover:text-white transition-colors">
                        <i data-lucide="linkedin" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto px-6 py-6 border-t border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4 text-[10px] text-neutral-600">
            <span>&copy; 2026 PS GENERAL DRUGS CENTRE PHARMACY. All rights reserved.</span>
            <span>Made with precision for mobile excellence.</span>
        </div>
    </footer>
</div>

<!-- Alpine.js Page Interactivity Controller -->
<script>
    function landingPage() {
        return {
            mouseX: 0,
            mouseY: 0,
            
            // Starting stats states
            stats: {
                downloads: 0,
                rating: 0.0,
                uptime: 0.0,
                businesses: 0
            },
            
            updateGlow(e) {
                const rect = e.currentTarget.getBoundingClientRect();
                this.mouseX = e.clientX - rect.left;
                this.mouseY = e.clientY - rect.top;
            },
            
            init() {
                // Animate stats counter using JS interval triggers when visible
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.animateCountUp();
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1 });
                
                const statsEl = document.getElementById('hero-stats');
                if (statsEl) observer.observe(statsEl);
                
                // Initialize GSAP reveals
                this.initGSAP();
            },
            
            animateCountUp() {
                let start = 0;
                const duration = 2000;
                const steps = 60;
                const stepTime = duration / steps;
                
                const timer = setInterval(() => {
                    start += 1;
                    const progress = start / steps;
                    
                    this.stats.downloads = Math.floor(progress * 50);
                    this.stats.rating = (progress * 4.9).toFixed(1);
                    this.stats.uptime = (progress * 99.9).toFixed(1);
                    this.stats.businesses = Math.floor(progress * 10) + 'k';
                    
                    if (start >= steps) {
                        clearInterval(timer);
                        this.stats.downloads = 50;
                        this.stats.rating = 4.9;
                        this.stats.uptime = 99.9;
                        this.stats.businesses = '10k';
                    }
                }, stepTime);
            },
            
            initGSAP() {
                if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
                
                gsap.registerPlugin(ScrollTrigger);
                
                // Hero elements reveals
                gsap.from(".hero-text", {
                    opacity: 0,
                    y: 40,
                    duration: 1.2,
                    ease: "power3.out"
                });
                
                gsap.from(".hero-mockups", {
                    opacity: 0,
                    scale: 0.9,
                    duration: 1.5,
                    delay: 0.3,
                    ease: "power2.out"
                });
                
                // Scroll triggered reveals for section headers
                gsap.utils.toArray(".section-header").forEach(section => {
                    gsap.from(section, {
                        scrollTrigger: {
                            trigger: section,
                            start: "top 80%",
                            toggleActions: "play none none none"
                        },
                        opacity: 0,
                        y: 30,
                        duration: 1,
                        ease: "power2.out"
                    });
                });
                
                // Feature cards staggered reveal
                gsap.from(".feature-card", {
                    scrollTrigger: {
                        trigger: "#features",
                        start: "top 70%"
                    },
                    opacity: 0,
                    y: 40,
                    stagger: 0.15,
                    duration: 1,
                    ease: "power3.out"
                });
                
                // Why Cards reveal
                gsap.from(".why-card", {
                    scrollTrigger: {
                        trigger: "#why-choose",
                        start: "top 75%"
                    },
                    opacity: 0,
                    scale: 0.95,
                    stagger: 0.1,
                    duration: 0.8,
                    ease: "power2.out"
                });
                
                // Ratings section reveal
                gsap.from(".ratings-section > div", {
                    scrollTrigger: {
                        trigger: ".ratings-section",
                        start: "top 80%"
                    },
                    opacity: 0,
                    y: 35,
                    stagger: 0.15,
                    duration: 0.9,
                    ease: "power2.out"
                });
            }
        };
    }
    
    function carousel() {
        return {
            activeSlide: 0,
            autoplayInterval: null,
            
            slides: [
                {
                    subtitle: "Smart Dashboard",
                    title: "Real-time Operations",
                    desc: "Analyze business reports, balances, and customer activities directly from your main feeds screen.",
                    icon: "layout-dashboard",
                    color: "purple-400"
                },
                {
                    subtitle: "Invoice Management",
                    title: "Easy Billing Control",
                    desc: "Send and schedule customer receipts, bills, and payment links with just a few simple taps.",
                    icon: "file-spreadsheet",
                    color: "blue-400"
                },
                {
                    subtitle: "Customer Directory",
                    title: "Secure Client Database",
                    desc: "Store detailed customer profiles, histories, balances, and transaction logs in secure vaults.",
                    icon: "users",
                    color: "cyan-400"
                },
                {
                    subtitle: "Reports & Analytics",
                    title: "Detailed PDF Summaries",
                    desc: "Export transactional metrics or visual profit outlines to CSV in real-time.",
                    icon: "trending-up",
                    color: "purple-400"
                },
                {
                    subtitle: "Personal Settings",
                    title: "Secure Biometric Control",
                    desc: "Enable Face ID, fingerprint scanning, and encrypted cloud synchronization instantly.",
                    icon: "settings",
                    color: "blue-400"
                }
            ],
            
            nextSlide() {
                this.activeSlide = (this.activeSlide + 1) % this.slides.length;
            },
            
            prevSlide() {
                this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length;
            },
            
            setSlide(index) {
                this.activeSlide = index;
            },
            
            startAutoplay() {
                this.autoplayInterval = setInterval(() => {
                    this.nextSlide();
                }, 4000);
            },
            
            stopAutoplay() {
                if (this.autoplayInterval) {
                    clearInterval(this.autoplayInterval);
                }
            }
        };
    }
</script>
