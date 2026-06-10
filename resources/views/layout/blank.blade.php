<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="https://generaldrugcentre.com/assets/logo.png">
    <title>Take Your Business Anywhere | PSGDC Mobile App</title>
    
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
                        darkBg: '#0A0A0A',
                        purple: {
                            500: '#8B5CF6',
                            600: '#7C3AED',
                            700: '#6D28D9'
                        },
                        blue: {
                            500: '#3B82F6',
                            600: '#2563EB',
                            700: '#1D4ED8'
                        },
                        cyan: {
                            400: '#22D3EE',
                            500: '#06B6D4',
                            600: '#0891B2'
                        }
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 8s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'orb-float-1': 'floatOrb1 20s infinite alternate ease-in-out',
                        'orb-float-2': 'floatOrb2 25s infinite alternate ease-in-out',
                        'orb-float-3': 'floatOrb3 18s infinite alternate ease-in-out',
                        'marquee': 'marquee 25s linear infinite',
                    },
                    keyframes: {
                        floatOrb1: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '50%': { transform: 'translate(50px, -80px) scale(1.2)' },
                            '100%': { transform: 'translate(-30px, 40px) scale(0.9)' },
                        },
                        floatOrb2: {
                            '0%': { transform: 'translate(0px, 0px) scale(1.1)' },
                            '50%': { transform: 'translate(-60px, 60px) scale(0.8)' },
                            '100%': { transform: 'translate(40px, -50px) scale(1.2)' },
                        },
                        floatOrb3: {
                            '0%': { transform: 'translate(0px, 0px) scale(0.9)' },
                            '50%': { transform: 'translate(80px, 40px) scale(1.1)' },
                            '100%': { transform: 'translate(-40px, -60px) scale(1)' },
                        },
                        marquee: {
                            '0%': { transform: 'translateX(0%)' },
                            '100%': { transform: 'translateX(-50%)' }
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- GSAP & ScrollTrigger -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    @livewireStyles
    
    <style>
        /* Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0A0A0A;
        }
        ::-webkit-scrollbar-thumb {
            background: #262626;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #404040;
        }
        
        /* Glassmorphism custom classes */
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        
        .glass-card:hover {
            border-color: rgba(124, 58, 237, 0.3);
            background: rgba(255, 255, 255, 0.05);
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
            background: linear-gradient(to bottom right, rgba(124, 58, 237, 0.4), rgba(37, 99, 235, 0.4), transparent);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
            opacity: 0.5;
            transition: opacity 0.3s ease;
        }
        .glow-border:hover::before {
            opacity: 1;
        }
    </style>
</head>
<body class="bg-darkBg text-white antialiased overflow-x-hidden font-sans selection:bg-purple-600 selection:text-white">
    {{ $slot }}
    
    @livewireScripts
    
    <script>
        // Initialize Lucide Icons
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
