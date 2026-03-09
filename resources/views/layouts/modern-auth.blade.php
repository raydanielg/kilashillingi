<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Primary Meta Tags -->
    <title>{{ $title ?? 'KilaShillingi - Simamia Fedha zako kwa Usahihi' }}</title>
    <meta name="title" content="{{ $title ?? 'KilaShillingi - Mfumo wa Usimamizi wa Fedha Tanzania' }}">
    <meta name="description" content="KilaShillingi ni mfumo bora wa kusimamia mapato, matumizi, bajeti na madeni kwa Watanzania. Rekodi kila shillingi na imarisha uchumi wako leo.">
    <meta name="keywords" content="fedha, usimamizi wa fedha, bajeti, mhasibu, Tanzania, matumizi, mapato, madeni, KilaShillingi, akiba">
    <meta name="author" content="KilaShillingi">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'KilaShillingi - Simamia Fedha zako kwa Usahihi' }}">
    <meta property="og:description" content="Mfumo rahisi uliotengenezwa mahususi kwa ajili ya usimamizi wa fedha za kila siku nchini Tanzania.">
    <meta property="og:image" content="{{ asset('xing_5968847.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $title ?? 'KilaShillingi - Simamia Fedha zako kwa Usahihi' }}">
    <meta property="twitter:description" content="Mfumo rahisi uliotengenezwa mahususi kwa ajili ya usimamizi wa fedha za kila siku nchini Tanzania.">
    <meta property="twitter:image" content="{{ asset('xing_5968847.png') }}">

    <link rel="icon" type="image/png" href="{{ asset('xing_5968847.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-white min-h-screen">
    <div class="w-screen min-h-screen bg-white overflow-hidden flex flex-col md:flex-row">
        <!-- Left Side: Login Form -->
        <div class="w-full md:w-1/2 bg-gray-50 px-6 py-10 md:px-16 md:py-0 flex flex-col justify-center">
            <div class="w-full max-w-md mx-auto bg-white rounded-3xl shadow-xl shadow-black/5 border border-emerald-100 overflow-hidden">
                <div class="h-1 w-full bg-emerald-600"></div>
                <div class="p-8 md:p-10">
                    @yield('auth_header')

                    @php($showSocial = $showSocial ?? false)
                    @if ($showSocial)
                        <!-- Social Login Mockup -->
                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <button class="flex items-center justify-center gap-2 py-3 px-4 border border-emerald-100 rounded-xl hover:bg-emerald-50 transition font-semibold text-gray-800">
                                <img src="https://www.svgrepo.com/show/355037/google.svg" class="w-5 h-5" alt="Google">
                                Google
                            </button>
                            <button class="flex items-center justify-center gap-2 py-3 px-4 border border-emerald-100 rounded-xl hover:bg-emerald-50 transition font-semibold text-gray-800">
                                <i class="fa-brands fa-apple text-xl"></i>
                                Apple
                            </button>
                        </div>

                        <div class="relative mb-8 text-center">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
                            <span class="relative px-4 bg-white text-xs text-gray-400 uppercase tracking-widest font-semibold">AU ENDELEA HAPA</span>
                        </div>
                    @endif

                    @yield('content')

                    @yield('auth_footer')
                </div>
            </div>
        </div>

        <!-- Right Side: Marketing/Feature Section -->
        <div class="hidden md:flex w-1/2 bg-[#059669] px-16 py-0 flex-col justify-center relative overflow-hidden">
            <!-- Background Decorative Circles -->
            <div class="absolute top-[-10%] right-[-10%] w-[400px] h-[400px] bg-white/5 rounded-full"></div>
            <div class="absolute bottom-[-5%] left-[-5%] w-[200px] h-[200px] bg-white/5 rounded-full"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-12">
                    <div class="w-2 h-2 bg-white rounded-full"></div>
                    <span class="text-white/80 text-xs font-bold uppercase tracking-widest">KILASHILLINGI PLATFORM</span>
                </div>
                
                <h2 class="text-5xl font-extrabold text-white leading-tight mb-8">
                    Simamia Fedha, <br>
                    Imarisha Maisha.
                </h2>
                
                <p class="text-white/80 text-lg leading-relaxed mb-12 max-w-[400px]">
                    Mfumo rahisi uliotengenezwa mahususi kwa ajili ya usimamizi wa fedha za kila siku. Rekodi kila shillingi, fuatilia madeni, na panga bajeti yako kwa urahisi.
                </p>

                <div class="flex flex-col gap-6 mb-12">
                    <div class="flex items-center -space-x-3 overflow-hidden">
                        @foreach($displayUsers as $u)
                            <img class="inline-block h-12 w-12 rounded-full ring-2 ring-[#059669] bg-gray-200" 
                                 src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background=random&color=fff" 
                                 alt="{{ $u->name }}">
                        @endforeach
                        @if($usersCount > $displayUsers->count())
                            <div class="flex items-center justify-center h-12 w-12 rounded-full ring-2 ring-[#059669] bg-emerald-800 text-white text-xs font-bold">
                                +{{ $usersCount - $displayUsers->count() }}
                            </div>
                        @endif
                    </div>
                    <p class="text-white font-semibold text-lg">
                        @if($usersCount > 0)
                            Zaidi ya watumiaji <span class="text-emerald-200 font-extrabold">{{ $usersCount }}+</span> kutoka Tanzania 🇹🇿 wamejiunga.
                        @else
                            Kuwa wa kwanza kujiunga kutoka Tanzania 🇹🇿.
                        @endif
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-white font-bold text-xl mb-2">100% Salama</h4>
                        <p class="text-white/60 text-sm">Data zako zote zimehifadhiwa kwa usalama wa hali ya juu na faragha ya kutosha.</p>
                    </div>
                    <div>
                        <h4 class="text-white font-bold text-xl mb-2">Muda Halisi</h4>
                        <p class="text-white/60 text-sm">Pata muhtasari wa miamala yako papo hapo ukiwa popote na kifaa chochote.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
