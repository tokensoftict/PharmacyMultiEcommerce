<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="https://generaldrugcentre.com/assets/logo.png">
    <title>500 - System Error | PS General Drugs Centre</title>

    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brandRed: {
                            500: '#EF4444',
                            600: '#DC2626',
                            700: '#B91C1C'
                        },
                        brandBlue: {
                            500: '#3B82F6',
                            600: '#2563EB',
                            700: '#1D4ED8'
                        }
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    animation: {
                        'orb-float-1': 'floatOrb1 18s infinite alternate ease-in-out',
                        'orb-float-2': 'floatOrb2 22s infinite alternate ease-in-out',
                        'orb-float-3': 'floatOrb3 15s infinite alternate ease-in-out',
                    },
                    keyframes: {
                        floatOrb1: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '50%': { transform: 'translate(40px, -40px) scale(1.1)' },
                            '100%': { transform: 'translate(-30px, 20px) scale(0.9)' },
                        },
                        floatOrb2: {
                            '0%': { transform: 'translate(0px, 0px) scale(1.05)' },
                            '50%': { transform: 'translate(-30px, 50px) scale(0.95)' },
                            '100%': { transform: 'translate(30px, -20px) scale(1.15)' },
                        },
                        floatOrb3: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '50%': { transform: 'translate(20px, 40px) scale(1.05)' },
                            '100%': { transform: 'translate(-40px, -30px) scale(0.95)' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(220, 38, 38, 0.08);
        }

        .glow-border {
            position: relative;
        }

        .glow-border::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(to bottom right, rgba(220, 38, 38, 0.25), rgba(37, 99, 235, 0.25), transparent);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .glow-border:hover::before {
            opacity: 1;
        }
    </style>
</head>

<body class="bg-gray-50 text-neutral-900 antialiased overflow-hidden font-sans min-h-screen flex items-center justify-center relative">
    
    <!-- Decorative Floating Orbs (Safe background) -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-brandRed-500/10 rounded-full blur-3xl animate-orb-float-1"></div>
        <div class="absolute bottom-1/3 right-1/4 w-96 h-96 bg-brandBlue-500/10 rounded-full blur-3xl animate-orb-float-2"></div>
        <div class="absolute top-1/2 right-1/3 w-80 h-80 bg-red-400/5 rounded-full blur-3xl animate-orb-float-3"></div>
    </div>

    <!-- Main Content Container -->
    <div class="z-10 w-full max-w-xl px-6">
        <div class="glass-card glow-border rounded-3xl p-8 md:p-12 shadow-2xl text-center flex flex-col items-center">
            
            <!-- Logo / Brand Header -->
            <div class="mb-8 flex items-center gap-2">
                <img src="https://generaldrugcentre.com/assets/logo.png" alt="PSGDC Logo" class="h-10 w-auto">
                <span class="font-bold text-lg text-neutral-800 tracking-wide">PSGDC MYSTORE</span>
            </div>

            <!-- Error Code / Visual Representation -->
            <div class="relative mb-6">
                <h1 class="text-9xl font-black text-transparent bg-clip-text bg-gradient-to-br from-brandRed-600 to-red-400 leading-none select-none">500</h1>
                <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-neutral-900 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-widest shadow">
                    Server Error
                </div>
            </div>

            <!-- Descriptive Text -->
            <h2 class="text-2xl font-bold text-neutral-800 mb-3 mt-4">Oops! Something went wrong</h2>
            <p class="text-neutral-500 mb-8 max-w-md text-sm md:text-base leading-relaxed">
                Our servers are having a momentary hiccup. Rest assured, we have been notified and are on it. Please try reloading the page or head back home.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 w-full justify-center">
                <button onclick="window.location.reload()" class="flex items-center justify-center gap-2 px-6 py-3 bg-brandRed-600 hover:bg-brandRed-700 text-white font-medium rounded-xl transition duration-200 shadow-lg shadow-brandRed-600/20 active:scale-95">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    Reload Page
                </button>
                <a href="/" class="flex items-center justify-center gap-2 px-6 py-3 bg-white hover:bg-neutral-50 text-neutral-700 border border-neutral-200 font-medium rounded-xl transition duration-200 active:scale-95">
                    <i data-lucide="home" class="w-4 h-4"></i>
                    Back to Home
                </a>
            </div>

            <!-- Quick Links -->
            <div class="mt-10 pt-8 border-t border-neutral-100 w-full">
                <span class="text-xs text-neutral-400 uppercase tracking-wider block mb-4">Need immediate help?</span>
                <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm">
                    <a href="/contact" class="text-brandBlue-600 hover:text-brandBlue-700 font-medium hover:underline flex items-center gap-1">
                        <i data-lucide="phone-call" class="w-3.5 h-3.5"></i> Contact Support
                    </a>
                    <a href="https://generaldrugcentre.com" class="text-neutral-600 hover:text-neutral-900 font-medium hover:underline flex items-center gap-1">
                        <i data-lucide="globe" class="w-3.5 h-3.5"></i> Main Website
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Initialize Lucide Icons
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>

</html>
