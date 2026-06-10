<?php

use function Livewire\Volt\{state};
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layout.blank')] class extends Component
{

}
?>

<div x-data="landingPage()" 
     class="relative min-h-screen w-full bg-white text-neutral-900 overflow-hidden" 
     @mousemove="updateGlow($event)">
    
    <!-- Background Animated Mesh Gradient & Floating Orbs -->
    <div class="absolute inset-0 pointer-events-none z-0 overflow-hidden">
        <!-- Main background glow -->
        <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] rounded-full bg-brandRed-100/40 blur-[120px] animate-pulse-slow"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[65%] h-[65%] rounded-full bg-red-100/40 blur-[130px] animate-pulse-slow" style="animation-delay: 2s;"></div>
        <div class="absolute top-[40%] left-[50%] -translate-x-1/2 -translate-y-1/2 w-[50%] h-[50%] rounded-full bg-red-50/30 blur-[100px] animate-pulse-slow" style="animation-delay: 4s;"></div>
        
        <!-- Floating Accent Orbs -->
        <div class="absolute top-[15%] left-[20%] w-72 h-72 rounded-full bg-gradient-to-tr from-brandRed-200/30 to-red-200/20 blur-[60px] animate-orb-float-1"></div>
        <div class="absolute top-[60%] right-[15%] w-[350px] h-[350px] rounded-full bg-gradient-to-br from-red-200/25 to-rose-200/20 blur-[80px] animate-orb-float-2"></div>
        <div class="absolute bottom-[20%] left-[10%] w-80 h-80 rounded-full bg-gradient-to-tr from-rose-200/20 to-brandRed-200/30 blur-[70px] animate-orb-float-3"></div>
        
        <!-- Particle Grid Overlay -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(220,38,38,0.08),rgba(255,255,255,0))]"></div>
        <div class="absolute inset-0 opacity-[0.02] bg-[linear-gradient(to_right,#000_1px,transparent_1px),linear-gradient(to_bottom,#000_1px,transparent_1px)] bg-[size:24px_24px]"></div>
    </div>
    
    <!-- Interactive Mouse-Follow Glow -->
    <div class="absolute pointer-events-none z-10 w-[400px] h-[400px] rounded-full bg-brandRed-500/5 blur-[100px] -translate-x-1/2 -translate-y-1/2 transition-all duration-300 ease-out hidden md:block"
         :style="'left: ' + mouseX + 'px; top: ' + mouseY + 'px;'"></div>

    <!-- Navigation Header -->
    <header class="relative z-50 max-w-7xl mx-auto px-6 h-24 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="#" class="flex items-center gap-2">
                <img src="{{ asset('logo/logo.png') }}" alt="PSGDC Logo" class="h-16 w-auto object-contain">
            </a>
        </div>
        
        <nav class="hidden md:flex items-center gap-8 text-sm text-neutral-600 font-medium">
            <a href="#features" class="hover:text-black transition-colors duration-200">Features</a>
            <a href="#about-us" class="hover:text-black transition-colors duration-200">About Us</a>
            <a href="#contact-us" class="hover:text-black transition-colors duration-200">Contact Us</a>
            <a href="#privacy-policy" class="hover:text-black transition-colors duration-200">Privacy Policy</a>
        </nav>
        
        <div>
            <a href="#download" class="relative group overflow-hidden px-5 py-2.5 rounded-full text-xs font-semibold tracking-wider uppercase border border-neutral-200 bg-neutral-50 hover:bg-neutral-100 transition-all duration-300 block">
                <span class="relative z-10 text-neutral-900 flex items-center gap-1.5">
                    Download App <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </span>
            </a>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="relative z-20 max-w-7xl mx-auto px-6 pt-12 pb-24 md:py-32 grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
        <!-- Hero Text content -->
        <div class="lg:col-span-7 flex flex-col items-start hero-text">
            <!-- Promo Badge -->
            <div class="mb-6 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-brandRed-200 bg-red-50 text-xs font-semibold text-brandRed-600 tracking-wide">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-brandRed-600"></span>
                </span>
                PSGDC Pharmacy & Supermarket App
            </div>
            
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight leading-[1.15] mb-6 text-neutral-950">
                Your Pharmacy & <br/>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-brandRed-600 via-rose-500 to-red-700">
                    Supermarket, Delivered
                </span>
            </h1>
            
            <p class="text-neutral-600 text-lg md:text-xl leading-relaxed max-w-xl mb-10">
                Shop 20,000+ groceries, OTC drugs, prescription medications, provisions, and clinical supplies. Get same-day delivery right to your doorstep.
            </p>
            
            <!-- App Download Buttons (Premium Dark Buttons) -->
            <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto mb-16">
                <!-- App Store -->
                <a href="#" class="relative w-full sm:w-56 group overflow-hidden bg-black text-white py-4 px-6 rounded-2xl flex items-center justify-center gap-3 shadow-xl hover:scale-[1.03] active:scale-[0.98] transition-all duration-300">
                    <i data-lucide="apple" class="w-7 h-7 fill-white"></i>
                    <div class="text-left">
                        <div class="text-[10px] uppercase font-bold tracking-wider opacity-60 leading-none">Download on the</div>
                        <div class="text-base font-extrabold leading-tight">App Store</div>
                    </div>
                </a>
                
                <!-- Google Play -->
                <a href="#" class="relative w-full sm:w-56 group overflow-hidden bg-neutral-900 text-white py-4 px-6 rounded-2xl flex items-center justify-center gap-3 shadow-xl hover:scale-[1.03] active:scale-[0.98] transition-all duration-300">
                    <svg class="w-6 h-6 fill-white" viewBox="0 0 24 24">
                        <path d="M5,3.14L15.3,13.43L5,23.73C4.6,23.53 4.3,23.13 4.3,22.6V4.28C4.3,3.75 4.6,3.34 5,3.14M16.71,14.85L19.46,16.43C20.15,16.83 20.15,17.7 19.46,18.1L16.71,19.68L14.16,17.13L16.71,14.85M5.72,2.5L16,12.78L13.43,15.35L5.72,2.5M16,13.07L18.75,14.65C19.44,15.05 19.44,15.92 18.75,16.32L16,17.9L13.72,15.62L16,13.07Z"/>
                    </svg>
                    <div class="text-left">
                        <div class="text-[10px] uppercase font-bold tracking-wider opacity-60 leading-none">Get it on</div>
                        <div class="text-base font-extrabold leading-tight">Google Play</div>
                    </div>
                </a>
            </div>
            
            <!-- Animated Statistics Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 md:gap-10 border-t border-neutral-100 pt-10 w-full text-left" id="hero-stats">
                <div>
                    <div class="text-3xl md:text-4xl font-extrabold text-neutral-900" x-text="stats.downloads + 'k+'">0k+</div>
                    <div class="text-neutral-500 text-xs mt-1 uppercase tracking-wider font-semibold">Downloads</div>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-extrabold text-neutral-900" x-text="stats.products + 'k+'">0k+</div>
                    <div class="text-neutral-500 text-xs mt-1 uppercase tracking-wider font-semibold">Products</div>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-extrabold text-neutral-900" x-text="stats.uptime">0</div>
                    <div class="text-neutral-500 text-xs mt-1 uppercase tracking-wider font-semibold flex items-center gap-1">
                        Rating <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-500 text-amber-500"></i>
                    </div>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-extrabold text-neutral-900" x-text="stats.satisfaction + '%'">0%</div>
                    <div class="text-neutral-500 text-xs mt-1 uppercase tracking-wider font-semibold">Satisfaction</div>
                </div>
            </div>
        </div>
        
        <!-- Hero Display: 3D floating Mockups (Pharmacy App Views) -->
        <div class="lg:col-span-5 relative h-[500px] sm:h-[650px] w-full flex items-center justify-center perspective-[1500px] hero-mockups">
            <!-- iOS Mockup (Front) - Product Catalog -->
            <div class="absolute z-30 transform -rotate-y-[15deg] rotate-x-[5deg] hover:rotate-y-[5deg] hover:scale-[1.03] transition-all duration-700 ease-out translate-x-[-15%] sm:translate-x-[-25%] translate-y-[-5%]">
                <div class="w-[240px] h-[480px] sm:w-[280px] sm:h-[560px] rounded-[48px] p-3 bg-neutral-950 border-[6px] border-neutral-800 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.3)] relative overflow-hidden ring-1 ring-white/10">
                    <!-- Notch -->
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-36 h-6 bg-neutral-950 rounded-b-2xl z-50 flex items-center justify-between px-5 py-1">
                        <div class="w-3.5 h-3.5 rounded-full bg-neutral-900"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-neutral-900"></div>
                    </div>
                    <!-- Screen App Content -->
                    <div class="w-full h-full rounded-[38px] bg-[#0C0C0E] overflow-hidden flex flex-col p-4 pt-8 text-left text-xs relative select-none text-white">
                        <div class="flex justify-between items-center text-[10px] text-neutral-400 mb-4 px-2">
                            <span>9:41</span>
                            <div class="flex gap-1">
                                <i data-lucide="wifi" class="w-3 h-3"></i>
                                <i data-lucide="battery" class="w-3.5 h-3.5"></i>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <div class="text-neutral-500 font-semibold text-[9px] uppercase">Online Supermarket</div>
                                <div class="text-sm font-bold">PSGDC Store</div>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-red-500/10 border border-red-500/20 flex items-center justify-center">
                                <i data-lucide="shopping-cart" class="w-4 h-4 text-brandRed-500"></i>
                            </div>
                        </div>
                        
                        <!-- Promo Banner -->
                        <div class="p-3.5 rounded-2xl bg-gradient-to-tr from-brandRed-600/30 to-blue-500/10 border border-brandRed-500/20 mb-4">
                            <span class="text-brandRed-500 text-[9px] font-bold uppercase">Same-Day Delivery</span>
                            <div class="text-xs font-black mt-0.5">Free delivery for orders above ₦20,000</div>
                            <div class="text-[9px] text-neutral-400 font-semibold mt-1">Order groceries, drugs & provisions</div>
                        </div>
                        
                        <!-- Categories Grid -->
                        <span class="text-neutral-400 text-[9px] font-semibold uppercase mb-2">Shop Categories</span>
                        <div class="grid grid-cols-2 gap-2 mb-4">
                            <div class="p-2 bg-neutral-900/60 border border-white/5 rounded-xl flex items-center gap-1.5">
                                <i data-lucide="pill" class="w-4 h-4 text-brandRed-500"></i>
                                <span class="text-[9px] font-bold">Pharmacy</span>
                            </div>
                            <div class="p-2 bg-neutral-900/60 border border-white/5 rounded-xl flex items-center gap-1.5">
                                <i data-lucide="apple" class="w-4 h-4 text-amber-400"></i>
                                <span class="text-[9px] font-bold">Groceries</span>
                            </div>
                            <div class="p-2 bg-neutral-900/60 border border-white/5 rounded-xl flex items-center gap-1.5">
                                <i data-lucide="baby" class="w-4 h-4 text-blue-400"></i>
                                <span class="text-[9px] font-bold">Baby Care</span>
                            </div>
                            <div class="p-2 bg-neutral-900/60 border border-white/5 rounded-xl flex items-center gap-1.5">
                                <i data-lucide="sparkles" class="w-4 h-4 text-pink-400"></i>
                                <span class="text-[9px] font-bold">Cosmetics</span>
                            </div>
                        </div>

                        <!-- Product List Widget -->
                        <div class="p-2.5 rounded-xl bg-neutral-900/60 border border-white/5 flex justify-between items-center mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-white/5 rounded-lg flex items-center justify-center text-xs">💊</div>
                                <div>
                                    <div class="font-bold">Multivitamins Tab</div>
                                    <div class="text-[8px] text-neutral-500">NAFDAC Reg.</div>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-brandRed-500">₦2,500</span>
                        </div>
                        
                        <!-- Nav menu bar -->
                        <div class="flex justify-between pt-2 border-t border-white/5 text-neutral-500 text-[9px] mt-auto">
                            <div class="flex flex-col items-center gap-0.5 text-brandRed-500">
                                <i data-lucide="store" class="w-3.5 h-3.5"></i>
                                <span>Shop</span>
                            </div>
                            <div class="flex flex-col items-center gap-0.5">
                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                <span>Prescription</span>
                            </div>
                            <div class="flex flex-col items-center gap-0.5">
                                <i data-lucide="truck" class="w-3.5 h-3.5"></i>
                                <span>Orders</span>
                            </div>
                            <div class="flex flex-col items-center gap-0.5">
                                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                <span>Account</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Android Mockup (Back-Offset) - Prescription Upload -->
            <div class="absolute z-20 transform rotate-y-[15deg] rotate-x-[5deg] hover:rotate-y-[5deg] hover:scale-[1.03] transition-all duration-700 ease-out translate-x-[25%] translate-y-[8%] opacity-80 hover:opacity-100">
                <div class="w-[230px] h-[460px] sm:w-[270px] sm:h-[540px] rounded-[44px] p-3 bg-neutral-950 border-[6px] border-neutral-800 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.3)] relative overflow-hidden ring-1 ring-white/10">
                    <div class="absolute top-4 left-1/2 -translate-x-1/2 w-3.5 h-3.5 rounded-full bg-neutral-950 z-50 ring-2 ring-neutral-800"></div>
                    <!-- Screen App Content -->
                    <div class="w-full h-full rounded-[34px] bg-[#0A0A0B] overflow-hidden flex flex-col p-4 pt-8 text-left text-xs relative select-none text-white">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-bold">Upload Prescription</span>
                            <div class="w-7 h-7 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center">
                                <i data-lucide="file-plus" class="w-4 h-4 text-brandRed-500"></i>
                            </div>
                        </div>
                        
                        <!-- Upload Container -->
                        <div class="border border-dashed border-white/10 rounded-2xl p-4 text-center mb-4 flex flex-col items-center justify-center bg-white/5">
                            <i data-lucide="image" class="w-8 h-8 text-neutral-400 mb-2"></i>
                            <span class="text-[9px] text-neutral-300 font-semibold">Select doctor note photo</span>
                            <span class="text-[8px] text-neutral-500 mt-1">Supports PNG, JPG, PDF</span>
                        </div>
                        
                        <div class="p-3 rounded-xl bg-neutral-900 border border-white/5 text-[9px] text-neutral-400 space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span>Verified by</span>
                                <span class="text-white font-bold">Licensed Pharmacist</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Dispensing time</span>
                                <span class="text-white font-bold">Under 30 mins</span>
                            </div>
                        </div>

                        <!-- Quick checkout button -->
                        <div class="w-full bg-brandRed-600 py-3 rounded-xl text-center text-[10px] font-bold text-white mt-auto cursor-pointer hover:bg-brandRed-500 transition-colors flex items-center justify-center gap-1.5">
                            <i data-lucide="shield-check" class="w-4 h-4"></i> Submit to Pharmacist
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section id="features" class="relative z-20 max-w-7xl mx-auto px-6 py-24 border-t border-neutral-100">
        <div class="text-center max-w-2xl mx-auto mb-20 section-header">
            <h2 class="text-xs uppercase font-extrabold tracking-widest text-brandRed-600 mb-3">Core App Features</h2>
            <p class="text-3xl md:text-5xl font-extrabold tracking-tight text-neutral-950">A Better Way to Shop Health & Groceries</p>
            <p class="text-neutral-600 text-base md:text-lg mt-4 leading-relaxed">
                Skip long pharmacy lines. Get all your medical essentials and grocery provisions delivered securely using our mobile app.
            </p>
        </div>
        
        <!-- Feature Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
            <!-- 1. Smart Prescription Processing -->
            <div class="md:col-span-7 glass-card rounded-3xl p-8 md:p-10 flex flex-col justify-between overflow-hidden relative group glow-border feature-card shadow-sm">
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-red-100 to-transparent blur-3xl rounded-full pointer-events-none"></div>
                <div class="mb-10">
                    <div class="w-12 h-12 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center mb-6">
                        <i data-lucide="file-plus" class="w-6 h-6 text-brandRed-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 text-neutral-950">Prescription Uploads</h3>
                    <p class="text-neutral-600 leading-relaxed max-w-md">
                        Take a quick photo of your medical prescription. Our licensed in-house pharmacists will verify, dispense, and pack your dosage details safely.
                    </p>
                </div>
                
                <div class="bg-neutral-50 border border-neutral-200/60 rounded-2xl p-6 relative w-full overflow-hidden self-end">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-xs font-semibold text-neutral-700">Submit prescription script</span>
                        <span class="text-xs text-brandRed-650 bg-red-100 px-2 py-0.5 rounded-full font-bold">Pharmacist Active</span>
                    </div>
                    <div class="flex gap-4 items-center">
                        <div class="w-12 h-12 bg-neutral-200 rounded-lg flex items-center justify-center text-xl text-neutral-500">📄</div>
                        <div class="flex-1">
                            <div class="text-xs font-bold text-neutral-800">prescription-note-v2.jpg</div>
                            <div class="text-[10px] text-neutral-500">Uploaded 2 mins ago • 1.4 MB</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Grocery & Provisions Catalog -->
            <div class="md:col-span-5 glass-card rounded-3xl p-8 md:p-10 flex flex-col justify-between overflow-hidden relative group glow-border feature-card shadow-sm">
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-amber-105 to-transparent blur-3xl rounded-full pointer-events-none"></div>
                <div class="mb-10">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center mb-6">
                        <i data-lucide="apple" class="w-6 h-6 text-amber-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 text-neutral-950">20,000+ Supermarket items</h3>
                    <p class="text-neutral-600 leading-relaxed">
                        Access an extensive catalog including imported UK groceries, baby care items, home provisions, beverages, and personal cosmetics.
                    </p>
                </div>
                <div class="bg-neutral-50 border border-neutral-200/60 rounded-2xl p-4 w-full mt-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🍞</span>
                        <span class="text-xs font-bold text-neutral-800">Premium sliced bread</span>
                    </div>
                    <span class="text-xs font-bold text-brandRed-600">In Stock</span>
                </div>
            </div>

            <!-- 3. Cold Chain & Medical Storage -->
            <div class="md:col-span-5 glass-card rounded-3xl p-8 md:p-10 flex flex-col justify-between overflow-hidden relative group glow-border feature-card shadow-sm">
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-red-50 to-transparent blur-3xl rounded-full pointer-events-none"></div>
                <div class="mb-10">
                    <div class="w-12 h-12 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center mb-6">
                        <i data-lucide="thermometer" class="w-6 h-6 text-brandRed-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 text-neutral-950">Cold Chain Storage</h3>
                    <p class="text-neutral-600 leading-relaxed">
                        Injectables and heat-sensitive vaccines are stored and shipped using strictly monitored cooling systems to ensure maximum efficacy.
                    </p>
                </div>
            </div>

            <!-- 4. Same-Day Home Delivery -->
            <div class="md:col-span-7 glass-card rounded-3xl p-8 md:p-10 flex flex-col justify-between overflow-hidden relative group glow-border feature-card shadow-sm">
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-red-100 to-transparent blur-3xl rounded-full pointer-events-none"></div>
                <div class="mb-10">
                    <div class="w-12 h-12 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center mb-6">
                        <i data-lucide="truck" class="w-6 h-6 text-brandRed-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 text-neutral-950">Same-Day Courier Delivery</h3>
                    <p class="text-neutral-600 leading-relaxed max-w-md">
                        Get your orders delivered directly across Ilorin and other locations in Kwara state in a matter of hours. Fully trackable on the map.
                    </p>
                </div>
                <div class="bg-neutral-50 border border-neutral-200/60 rounded-2xl p-4 flex justify-between items-center w-full">
                    <div class="flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-4.5 h-4.5 text-brandRed-600"></i>
                        <span class="text-xs font-bold text-neutral-800">Lajorin junction delivery point</span>
                    </div>
                    <span class="text-[10px] bg-red-100 text-brandRed-605 px-2 py-0.5 rounded-full font-bold">Dispatched</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT US SECTION -->
    <section id="about-us" class="relative z-20 max-w-7xl mx-auto px-6 py-24 border-t border-neutral-100">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            <!-- Left Column: Rich editorial details -->
            <div class="lg:col-span-7 space-y-6">
                <h2 class="text-xs uppercase font-extrabold tracking-widest text-brandRed-600">About Our Company</h2>
                <h3 class="text-3xl md:text-5xl font-extrabold tracking-tight text-neutral-950 leading-tight">
                    Providing Access to Quality & Affordable Medicines
                </h3>
                <p class="text-neutral-600 text-base leading-relaxed">
                    PSGDC Pharmacy (PS General Drugs Centre) was established and incorporated in 2020. Our core store is situated at 1 Lajorin junction, Muritala Mohammed way, Ilorin, Kwara state.
                </p>
                <p class="text-neutral-600 text-base leading-relaxed">
                    We manage a dedicated team of over 50 professionals, including registered pharmacists, trained warehouse logistics personnel, and assistants. We specialize in the bulk and retail distribution of ethical drugs, OTCs, injectables, cooling chain items, surgical equipment, and imported UK groceries.
                </p>
                <div class="p-6 rounded-3xl bg-neutral-50 border border-neutral-200/60 space-y-4">
                    <div class="flex items-center gap-3">
                        <i data-lucide="shield-check" class="w-6 h-6 text-brandRed-600 flex-shrink-0"></i>
                        <span class="text-sm font-bold text-neutral-900">NAFDAC Certified Products</span>
                    </div>
                    <p class="text-xs text-neutral-500 leading-relaxed pl-9">
                        Our inventory holds over 3,000 NAFDAC registered and verified pharmaceutical products to guarantee authentic medical outcomes.
                    </p>
                </div>
            </div>
            
            <!-- Right Column: Visual Connection -->
            <div class="lg:col-span-5 space-y-6">
                <div class="glass-card rounded-[32px] p-8 space-y-6 glow-border shadow-sm bg-gradient-to-br from-red-50/30 to-rose-50/20">
                    <h4 class="text-xs uppercase font-extrabold tracking-wider text-neutral-500">Manufacturing Connection</h4>
                    <div class="flex gap-4 items-center">
                        <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center text-brandRed-600">
                            <i data-lucide="factory" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h5 class="text-base font-bold text-neutral-950">Peace Standard Pharm.</h5>
                            <span class="text-xs text-neutral-500">Our Parent Manufacturing Arm</span>
                        </div>
                    </div>
                    <p class="text-xs text-neutral-605 leading-relaxed">
                        PSGDC operates as a direct distribution subsidiary of Peace Standard Pharmaceutical Industries Ltd. This connection links our inventory directly to a state-of-the-art manufacturing arm formulating over 50 quality pharmaceutical products.
                    </p>
                </div>
                
                <div class="glass-card rounded-[32px] p-8 space-y-4 glow-border shadow-sm">
                    <h4 class="text-xs uppercase font-extrabold tracking-wider text-neutral-500">Our Objective</h4>
                    <p class="text-xs text-neutral-605 leading-relaxed">
                        Promoting healthcare accessibility, security, and affordability across Kwara state and Nigeria at large.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT US SECTION -->
    <section id="contact-us" class="relative z-20 max-w-7xl mx-auto px-6 py-24 border-t border-neutral-100">
        <div class="text-center max-w-2xl mx-auto mb-20 section-header">
            <h2 class="text-xs uppercase font-extrabold tracking-widest text-brandRed-600 mb-3">Contact Support</h2>
            <p class="text-3xl md:text-5xl font-extrabold tracking-tight text-neutral-950">Get In Touch With Us</p>
            <p class="text-neutral-600 text-base md:text-lg mt-4 leading-relaxed">
                Have questions about prescriptions, deliveries, or wholesales? Reach our support team.
            </p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">
            <!-- Contact info card -->
            <div class="lg:col-span-5 space-y-6">
                <div class="glass-card rounded-3xl p-8 space-y-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-brandRed-600 flex-shrink-0">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase text-neutral-500 tracking-wider">Address</h4>
                            <p class="text-sm text-neutral-750 font-medium mt-1">
                                1, Lajorin Junction, Muritala Muhammed Way, Ilorin, Kwara State, Nigeria.
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-brandRed-600 flex-shrink-0">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase text-neutral-500 tracking-wider">Phone Number</h4>
                            <p class="text-sm text-neutral-750 font-medium mt-1">
                                0909 595 0088
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-brandRed-600 flex-shrink-0">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase text-neutral-500 tracking-wider">Email Address</h4>
                            <p class="text-sm text-neutral-750 font-medium mt-1">
                                info@generaldrugcentre.com
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-brandRed-600 flex-shrink-0">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase text-neutral-500 tracking-wider">Opening Hours</h4>
                            <p class="text-sm text-neutral-750 font-medium mt-1">
                                Monday to Saturday: 8:00 AM – 6:00 PM
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact email submission form -->
            <div class="lg:col-span-7 glass-card rounded-3xl p-8 md:p-10 shadow-sm border border-neutral-200">
                <form class="space-y-6" @submit.prevent="alert('Thank you for contacting PSGDC Pharmacy!')">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase text-neutral-500 mb-2">First Name</label>
                            <input type="text" required class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:outline-none focus:border-brandRed-600 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-neutral-500 mb-2">Last Name</label>
                            <input type="text" required class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:outline-none focus:border-brandRed-600 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-neutral-500 mb-2">Email Address</label>
                        <input type="email" required class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:outline-none focus:border-brandRed-600 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-neutral-500 mb-2">Message</label>
                        <textarea rows="4" required class="w-full px-4 py-3 rounded-xl border border-neutral-200 focus:outline-none focus:border-brandRed-600 text-sm resize-none"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-brandRed-600 hover:bg-brandRed-700 text-white text-xs font-bold uppercase tracking-wider py-4 rounded-xl shadow-lg shadow-brandRed-600/20 transition-all">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- PRIVACY POLICY SECTION -->
    <section id="privacy-policy" class="relative z-20 max-w-4xl mx-auto px-6 py-24 border-t border-neutral-100">
        <div class="text-center mb-16 section-header">
            <h2 class="text-xs uppercase font-extrabold tracking-widest text-brandRed-600 mb-3">Legal Guidelines</h2>
            <p class="text-3xl md:text-5xl font-extrabold tracking-tight text-neutral-950">Privacy Policy</p>
        </div>
        
        <div class="glass-card rounded-3xl p-8 md:p-10 shadow-sm border border-neutral-200 space-y-6 text-sm text-neutral-600 leading-relaxed">
            <p class="font-bold text-neutral-800 text-xs uppercase tracking-wide">Last Updated: June 10, 2026</p>
            <p>
                At PSGDC Pharmacy, we respect your privacy. This policy outlines how the PSGDC mobile multi-ecommerce app gathers, secures, and handles user account information and order histories.
            </p>
            
            <details class="group border-b border-neutral-200 pb-4">
                <summary class="flex justify-between items-center font-bold text-neutral-900 cursor-pointer select-none list-none">
                    <span>1. Information We Collect</span>
                    <i data-lucide="plus" class="w-4 h-4 text-neutral-500 group-open:rotate-45 transition-transform"></i>
                </summary>
                <p class="text-neutral-550 mt-2 pl-4 text-xs">
                    We collect your basic profile details (name, delivery address, phone) and photos of doctor prescriptions submitted for dispensing. Diagnostic logs are tracked anonymously to prevent crash issues.
                </p>
            </details>
            
            <details class="group border-b border-neutral-200 pb-4">
                <summary class="flex justify-between items-center font-bold text-neutral-900 cursor-pointer select-none list-none">
                    <span>2. Offline Storage & Synchronization</span>
                    <i data-lucide="plus" class="w-4 h-4 text-neutral-500 group-open:rotate-45 transition-transform"></i>
                </summary>
                <p class="text-neutral-550 mt-2 pl-4 text-xs">
                    Draft order items and prescription records are stored securely in internal storage on your device. These details sync to our secure cloud pharmacy vault once cellular networks are online.
                </p>
            </details>
            
            <details class="group border-b border-neutral-200 pb-4">
                <summary class="flex justify-between items-center font-bold text-neutral-900 cursor-pointer select-none list-none">
                    <span>3. Security Protocols</span>
                    <i data-lucide="plus" class="w-4 h-4 text-neutral-500 group-open:rotate-45 transition-transform"></i>
                </summary>
                <p class="text-neutral-550 mt-2 pl-4 text-xs">
                    Communication channels use TLS/SSL encryption. We do not distribute patient diagnostics or transaction data to external marketing services.
                </p>
            </details>
        </div>
    </section>

    <!-- CALL TO ACTION (CTA) SECTION -->
    <section id="download" class="relative z-20 max-w-6xl mx-auto px-6 py-24 cta-section">
        <div class="glass-card rounded-[40px] p-8 md:p-20 text-center relative overflow-hidden glow-border shadow-sm bg-gradient-to-tr from-brandRed-50/50 to-rose-50/50">
            <h2 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6 text-neutral-950">
                Order Medicines & Groceries. <br/>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-brandRed-600 via-rose-500 to-red-700">
                    Grow Faster Tomorrow.
                </span>
            </h2>
            
            <p class="text-neutral-605 text-base md:text-lg max-w-lg mx-auto mb-12 leading-relaxed">
                Available now on iPhone and Android. Create your store account and get started in less than two minutes.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 max-w-md mx-auto">
                <a href="#" class="w-full sm:w-52 py-3.5 px-6 rounded-2xl bg-black text-white font-extrabold flex items-center justify-center gap-3 shadow-md hover:scale-[1.03] active:scale-[0.98] transition-transform duration-200">
                    <i data-lucide="apple" class="w-6 h-6 fill-white"></i>
                    <span>App Store</span>
                </a>
                <a href="#" class="w-full sm:w-52 py-3.5 px-6 rounded-2xl bg-neutral-900 text-white font-extrabold flex items-center justify-center gap-3 shadow-md hover:scale-[1.03] active:scale-[0.98] transition-all duration-200">
                    <i data-lucide="play" class="w-5 h-5 fill-white text-white"></i>
                    <span>Google Play</span>
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="relative z-20 border-t border-neutral-200 bg-neutral-50">
        <div class="max-w-7xl mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-4 gap-10">
            <div class="flex flex-col items-start gap-4">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('logo/logo.png') }}" alt="PSGDC Logo" class="h-16 w-auto object-contain">
                </div>
                <p class="text-neutral-500 text-xs leading-relaxed max-w-[200px]">
                    Manage medicines, operations, and orders from the palm of your hand.
                </p>
            </div>
            
            <div>
                <h5 class="text-xs uppercase font-extrabold tracking-wider text-neutral-500 mb-4">Product</h5>
                <ul class="space-y-2.5 text-xs text-neutral-605">
                    <li><a href="#features" class="hover:text-black transition-colors duration-200">Features</a></li>
                    <li><a href="#download" class="hover:text-black transition-colors duration-200">Download app</a></li>
                </ul>
            </div>
            
            <div>
                <h5 class="text-xs uppercase font-extrabold tracking-wider text-neutral-500 mb-4">Company</h5>
                <ul class="space-y-2.5 text-xs text-neutral-605">
                    <li><a href="#about-us" class="hover:text-black transition-colors duration-200 font-medium">About Us</a></li>
                    <li><a href="#contact-us" class="hover:text-black transition-colors duration-200 font-medium">Contact Us</a></li>
                </ul>
            </div>
            
            <div>
                <h5 class="text-xs uppercase font-extrabold tracking-wider text-neutral-500 mb-4">Legal</h5>
                <ul class="space-y-2.5 text-xs text-neutral-605 mb-6">
                    <li><a href="/coupon-terms" class="hover:text-black transition-colors duration-200">Terms of Service</a></li>
                    <li><a href="#privacy-policy" class="hover:text-black transition-colors duration-200">Privacy Policy</a></li>
                </ul>
                <div class="flex gap-4">
                    <a href="#" class="w-8 h-8 rounded-lg bg-neutral-200/50 hover:bg-neutral-200 flex items-center justify-center text-neutral-500 hover:text-black transition-colors">
                        <i data-lucide="twitter" class="w-4 h-4"></i>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-lg bg-neutral-200/50 hover:bg-neutral-200 flex items-center justify-center text-neutral-500 hover:text-black transition-colors">
                        <i data-lucide="instagram" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto px-6 py-6 border-t border-neutral-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-[10px] text-neutral-500">
            <span>&copy; 2026 PS GENERAL DRUGS CENTRE PHARMACY. All rights reserved.</span>
            <span>Made with precision for pharmacy multi-ecommerce excellence.</span>
        </div>
    </footer>
</div>

<!-- Alpine.js Interactivity Controller -->
<script>
    function landingPage() {
        return {
            mouseX: 0,
            mouseY: 0,
            
            // Statistics counters
            stats: {
                downloads: 0,
                products: 0,
                uptime: 0.0,
                satisfaction: 0
            },
            
            updateGlow(e) {
                const rect = e.currentTarget.getBoundingClientRect();
                this.mouseX = e.clientX - rect.left;
                this.mouseY = e.clientY - rect.top;
            },
            
            init() {
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
                    this.stats.products = Math.floor(progress * 20);
                    this.stats.uptime = (progress * 4.9).toFixed(1);
                    this.stats.satisfaction = Math.floor(progress * 99);
                    
                    if (start >= steps) {
                        clearInterval(timer);
                        this.stats.downloads = 50;
                        this.stats.products = 20;
                        this.stats.uptime = 4.9;
                        this.stats.satisfaction = 99;
                    }
                }, stepTime);
            },
            
            initGSAP() {
                if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
                
                gsap.registerPlugin(ScrollTrigger);
                
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
            }
        };
    }
</script>
