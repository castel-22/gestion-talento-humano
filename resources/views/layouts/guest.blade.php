<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SGTH') }} - Acceso Institucional</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --pc-blue: #0B3B5E;
            --pc-blue-light: #1E5A7D;
            --pc-orange: #F97316;
            --pc-red: #E63946;
            --pc-light: #f8fafc;
            --pc-dark-text: #1e293b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--pc-light);
            background-image: 
                radial-gradient(at 0% 0%, rgba(11, 59, 94, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(249, 115, 22, 0.05) 0px, transparent 50%);
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .tech-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: linear-gradient(rgba(0,0,0,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: 0;
            pointer-events: none;
        }

        .auth-container {
            width: 100%;
            max-width: 900px;
            padding: 0.5rem;
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .outfit-font { font-weight: 800; }

        .auth-card {
            width: 100%;
            max-width: 550px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            border-top: 4px solid var(--pc-orange);
            border-radius: 24px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.05) 0%, transparent 100%);
            pointer-events: none;
        }

        @media (min-width: 640px) {
            .auth-card { padding: 2rem; }
        }



        .motto-text {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.4em;
            text-transform: uppercase;
            color: var(--pc-orange);
            font-style: italic;
            margin-bottom: 1.5rem;
            text-align: center;
            opacity: 0.9;
        }

        .header-title {
            font-size: 2.25rem;
            color: var(--pc-blue);
            text-transform: uppercase;
            letter-spacing: -0.05em;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .floating-object {
            position: absolute;
            pointer-events: none;
            opacity: 0.05;
            z-index: 1;
            filter: blur(1px);
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(-30px) rotate(15deg); }
        }

        .animate-float { animation: float 6s infinite alternate ease-in-out; }

        .input-auth-pc {
            display: block;
            width: 100%;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            padding-right: 1.25rem;
            /* padding-left is handled by Tailwind's pl-12 utility */
            background-color: rgba(255, 255, 255, 1);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--pc-dark-text);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);
        }
        
        .input-auth-pc::placeholder {
            color: rgba(0, 0, 0, 0.3);
            font-weight: 500;
        }
        
        .input-auth-pc:focus {
            background-color: rgba(255, 255, 255, 0.07);
            border-color: var(--pc-orange);
            outline: none;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
            transform: translateY(-2px);
        }

        .btn-auth-pc {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(90deg, var(--pc-blue) 0%, var(--pc-blue-light) 100%);
            color: white;
            font-weight: 900;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.25em;
            border: none;
            border-radius: 1rem;
            cursor: pointer;
            transition: all 0.4s;
            box-shadow: 0 10px 20px -5px rgba(11, 59, 94, 0.5);
        }
        
        .btn-auth-pc:hover { 
            background: linear-gradient(90deg, var(--pc-orange) 0%, #fb923c 100%);
            box-shadow: 0 15px 25px -5px rgba(249, 115, 22, 0.4);
            transform: translateY(-2px);
        }
        /* Dark Mode overrides */
        body.dark {
            background-color: #020617 !important;
            background-image: 
                radial-gradient(at 0% 0%, rgba(11, 59, 94, 0.4) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(249, 115, 22, 0.2) 0px, transparent 50%) !important;
            color: #f8fafc;
        }
        .dark .tech-grid {
            background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
        }
        .dark .auth-card {
            background: rgba(15, 23, 42, 0.8) !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
            box-shadow: 0 25px 70px -10px rgba(0, 0, 0, 0.7) !important;
        }
        .dark .header-title { color: white; }
        .dark .input-auth-pc {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            box-shadow: none;
        }
        .dark .input-auth-pc::placeholder { color: rgba(255, 255, 255, 0.2); }
        
        /* Fix Chrome Autofill background in Dark Mode */
        .dark .input-auth-pc:-webkit-autofill,
        .dark .input-auth-pc:-webkit-autofill:hover, 
        .dark .input-auth-pc:-webkit-autofill:focus, 
        .dark .input-auth-pc:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 30px #0f172a inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        
        /* Custom right column fix for dark mode */
        .dark .auth-right-col {
            background-color: rgba(0, 0, 0, 0.3) !important;
            border-color: rgba(255, 255, 255, 0.05) !important;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.5) !important;
        }
    </style>
</head>
<body class="antialiased transition-colors duration-500" x-data="{ theme: localStorage.getItem('theme') || 'light' }" x-init="$watch('theme', val => localStorage.setItem('theme', val))" :class="theme">
    
    <!-- Theme Toggle -->
    <button @click="theme = theme === 'dark' ? 'light' : 'dark'" 
            class="fixed top-6 right-6 z-50 w-12 h-12 rounded-full backdrop-blur-xl border flex items-center justify-center transition-all shadow-lg hover:scale-110"
            :class="theme === 'dark' ? 'bg-white/10 border-white/20 text-yellow-400 hover:bg-white/20' : 'bg-white/80 border-gray-200 text-pc-blue hover:bg-white'">
        <i class="fas fa-sun text-xl" x-show="theme === 'dark'" x-cloak></i>
        <i class="fas fa-moon text-xl" x-show="theme === 'light'"></i>
    </button>
    <div class="tech-grid"></div>
    
    <div class="floating-object" style="top: 10%; left: 10%; font-size: 150px; color: var(--pc-orange);">
        <i class="fas fa-shield-alt animate-float"></i>
    </div>
    <div class="floating-object" style="bottom: 10%; right: 10%; font-size: 180px; color: var(--pc-blue);">
        <i class="fas fa-helmet-safety animate-float" style="animation-delay: -2s;"></i>
    </div>

    <div class="auth-container">
        <div class="w-full flex flex-col md:flex-row items-center justify-between gap-6 md:gap-8 lg:gap-12 mb-10 mt-4 md:mt-8">
            <!-- Left Logo -->
            <a href="/" class="hidden md:block transition-transform hover:scale-110 duration-500 hover:-rotate-2 origin-center">
                <img src="{{ asset('images/logo_pc.png') }}" alt="Protección Civil" class="h-28 lg:h-36 xl:h-40 w-auto filter drop-shadow-2xl"
                     :class="theme === 'dark' ? 'drop-shadow-[0_0_30px_rgba(249,115,22,0.6)]' : 'drop-shadow-[0_4px_25px_rgba(249,115,22,0.3)]'">
            </a>
            
            <!-- Center Text -->
            <div class="flex flex-col items-center text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tighter mb-4 transition-colors"
                    :class="theme === 'dark' ? 'text-white drop-shadow-[0_2px_10px_rgba(0,0,0,0.8)]' : 'text-pc-blue drop-shadow-[0_2px_10px_rgba(0,0,0,0.05)]'">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-pc-orange to-orange-300">PLATAFORMA</span> SGTH
                </h1>
                
                <div class="inline-flex items-center gap-2 border px-6 py-2.5 rounded-full backdrop-blur-md transition-colors"
                     :class="theme === 'dark' ? 'bg-white/5 border-white/10 shadow-lg' : 'bg-white/70 border-gray-200 shadow-sm'">
                    <i class="fas fa-heartbeat text-pc-orange text-[14px] animate-pulse"></i>
                    <span class="text-[11px] font-black uppercase tracking-[0.25em]"
                          :class="theme === 'dark' ? 'text-blue-100' : 'text-pc-blue'">"Solo queremos salvar vidas"</span>
                </div>

                <!-- Logos for Mobile (hidden on md and up) -->
                <div class="flex md:hidden items-center justify-center gap-8 mt-8">
                    <a href="/"><img src="{{ asset('images/logo_pc.png') }}" class="h-24 w-auto filter drop-shadow-xl" :class="theme === 'dark' ? 'drop-shadow-[0_0_20px_rgba(249,115,22,0.5)]' : 'drop-shadow-[0_2px_10px_rgba(249,115,22,0.3)]'"></a>
                    <div class="bg-white p-3 rounded-2xl shadow-sm">
                        <img src="{{ asset('images/logo_ciudad_bolivar.png') }}" class="h-16 w-auto">
                    </div>
                </div>
            </div>

            <!-- Right Logo -->
            <a href="/" class="hidden md:block transition-transform hover:scale-105 duration-500">
                <div class="bg-white p-4 lg:p-5 rounded-[1.5rem] flex items-center justify-center relative overflow-hidden"
                     :class="theme === 'dark' ? 'shadow-[inset_0_0_15px_rgba(0,0,0,0.1),0_0_30px_rgba(255,255,255,0.1)]' : 'shadow-[0_4px_25px_rgba(0,0,0,0.08)]'">
                    <div class="absolute inset-0 bg-gradient-to-br from-white via-transparent to-gray-100 opacity-50"></div>
                    <img src="{{ asset('images/logo_ciudad_bolivar.png') }}" alt="Gobernación Bolívar" class="h-20 lg:h-24 xl:h-28 w-auto object-contain relative z-10">
                </div>
            </a>
        </div>

        <div class="auth-card">
            {{ $slot }}
        </div>

        <div style="margin-top: 1.5rem; text-align: center;">
            <p style="font-size: 9px; font-weight: 900; color: #6b7280; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem;">
                &copy; {{ date('Y') }} Protección Civil Bolívar — S.O.S 911
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('mousemove', (e) => {
            const moveX = (e.clientX - window.innerWidth / 2) / 60;
            const moveY = (e.clientY - window.innerHeight / 2) / 60;
            document.querySelectorAll('.floating-object i').forEach(obj => {
                obj.style.transform = `translate(${moveX}px, ${moveY}px)`;
            });
        });

        // Prevención global de doble-clic (Double Submit)
        document.addEventListener('submit', function(e) {
            if (e.target.tagName === 'FORM' && !e.target.classList.contains('no-double-click')) {
                if (e.target.dataset.submitting) {
                    e.preventDefault();
                    return;
                }
                e.target.dataset.submitting = 'true';
                const btn = e.target.querySelector('button[type="submit"], input[type="submit"]');
                if (btn) {
                    setTimeout(() => {
                        btn.disabled = true;
                        btn.style.opacity = '0.5';
                        btn.style.cursor = 'not-allowed';
                    }, 10);
                }
            }
        });
    </script>
</body>
</html>