<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="gk7KxUFY7DbwpkZAMoNmIhGwZFR9e7TUde28BwJa">

        <title>Vittix Vedic Panchang — Professional Panchang &amp; Astrology Engine for PHP, Laravel &amp; REST APIs</title>
        <meta name="description" content="Generate Panchang, Kundli, planetary positions, muhurta, festivals and Hindu calendar data with one modern open-source PHP package. Official demo of the vittix/panchang engine.">
        
        <link rel="canonical" href="http://hindutithi:8080">

        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Hindutithi">
        <meta property="og:title" content="Vittix Vedic Panchang — Professional Panchang &amp; Astrology Engine for PHP, Laravel &amp; REST APIs">
        <meta property="og:description" content="Generate Panchang, Kundli, planetary positions, muhurta, festivals and Hindu calendar data with one modern open-source PHP package. Official demo of the vittix/panchang engine.">
        <meta property="og:url" content="http://hindutithi:8080">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="Vittix Vedic Panchang — Professional Panchang &amp; Astrology Engine for PHP, Laravel &amp; REST APIs">
        <meta name="twitter:description" content="Generate Panchang, Kundli, planetary positions, muhurta, festivals and Hindu calendar data with one modern open-source PHP package. Official demo of the vittix/panchang engine.">

        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "Hindutithi",
            "url": "http://hindutithi:8080",
            "description": "Vittix Vedic Panchang provides accurate Panchang and Vedic astrology (Kundli) calculations for PHP and Laravel with timezone-aware REST APIs and developer tools."
        }
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <link rel="preload" as="style" href="http://hindutithi:8080/build/assets/app-5tU792A0.css" /><link rel="modulepreload" as="script" href="http://hindutithi:8080/build/assets/app-zqGFmKvW.js" /><link rel="stylesheet" href="http://hindutithi:8080/build/assets/app-5tU792A0.css" /><script type="module" src="http://hindutithi:8080/build/assets/app-zqGFmKvW.js"></script>    </head>
    <body class="antialiased min-h-screen">

        
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none bg-[var(--color-bg-base)]">
            <div class="absolute -top-40 -right-40 h-[800px] w-[800px] rounded-full bg-[var(--color-brand-saffron)]/5 blur-[120px]"></div>
            <div class="absolute top-1/3 -left-40 h-[600px] w-[600px] rounded-full bg-[var(--color-brand-warm)]/5 blur-[100px]"></div>
            <div class="absolute bottom-0 right-1/3 h-[500px] w-[500px] rounded-full bg-[var(--color-brand-gold)]/5 blur-[120px]"></div>
        </div>

        <div class="flex min-h-screen flex-col">
            <nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-[var(--color-border-subtle)] bg-[var(--color-bg-base)]/80 backdrop-blur-md">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">

            
            <div class="flex items-center gap-6">
                <a href="http://hindutithi:8080" class="flex items-center gap-2.5 group">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[var(--color-brand-saffron)]/10 text-[var(--color-brand-saffron)] text-sm font-bold ring-1 ring-[var(--color-brand-saffron)]/20 group-hover:bg-[var(--color-brand-saffron)]/20 transition">☀</span>
                    <span class="text-base font-semibold text-[var(--color-text-primary)] tracking-wide">Vittix Panchang</span>
                </a>

                
                <div class="hidden sm:flex sm:items-center sm:gap-6 ml-4">
                    <a href="http://hindutithi:8080" class="text-sm font-medium transition text-[var(--color-text-primary)]">Product</a>
                    <a href="http://hindutithi:8080/day" class="text-sm font-medium transition text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]">Panchang</a>
                    <a href="http://hindutithi:8080/astrology" class="text-sm font-medium transition text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]">Astrology</a>
                    <a href="http://hindutithi:8080/festivals" class="text-sm font-medium transition text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]">Festivals</a>
                    <a href="http://hindutithi:8080/api/docs" class="text-sm font-medium transition text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]">API Docs</a>
                    <a href="http://hindutithi:8080/accuracy" class="text-sm font-medium transition text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]">Accuracy</a>
                </div>
            </div>

            
            <div class="hidden sm:flex sm:items-center sm:gap-5">
                <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang" target="_blank" class="text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] transition">GitHub</a>
                
                                    <a href="http://hindutithi:8080/login" class="text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] transition">Login</a>
                    <a href="http://hindutithi:8080#get-started" class="rounded-full bg-[var(--color-brand-saffron)] px-4 py-1.5 text-sm font-semibold text-[#030817] transition hover:bg-[var(--color-brand-gold)]">Get Started</a>
                            </div>

            
            <div class="flex sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center rounded-lg p-2 text-[var(--color-text-muted)] hover:bg-[var(--color-bg-surface)] hover:text-[var(--color-text-primary)] transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path :class="{'hidden': open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open}" class="hidden"   stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    
    <div :class="{'block': open, 'hidden': !open}" class="hidden border-t border-[var(--color-border-subtle)] sm:hidden bg-[var(--color-bg-surface)]">
        <div class="space-y-1 px-4 py-3">
                            <a href="http://hindutithi:8080"
                   class="block rounded-lg px-3 py-2 text-sm font-medium transition
                          bg-[var(--color-bg-elevated)] text-[var(--color-text-primary)]">
                    Product
                </a>
                            <a href="http://hindutithi:8080/day"
                   class="block rounded-lg px-3 py-2 text-sm font-medium transition
                          text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-elevated)]">
                    Panchang
                </a>
                            <a href="http://hindutithi:8080/astrology"
                   class="block rounded-lg px-3 py-2 text-sm font-medium transition
                          text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-elevated)]">
                    Astrology
                </a>
                            <a href="http://hindutithi:8080/festivals"
                   class="block rounded-lg px-3 py-2 text-sm font-medium transition
                          text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-elevated)]">
                    Festivals
                </a>
                            <a href="http://hindutithi:8080/api/docs"
                   class="block rounded-lg px-3 py-2 text-sm font-medium transition
                          text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-elevated)]">
                    API Docs
                </a>
                            <a href="http://hindutithi:8080/accuracy"
                   class="block rounded-lg px-3 py-2 text-sm font-medium transition
                          text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-elevated)]">
                    Accuracy
                </a>
                        <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang" target="_blank" class="block rounded-lg px-3 py-2 text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-elevated)]">GitHub</a>
        </div>
        <div class="border-t border-[var(--color-border-subtle)] px-4 py-3">
                            <div class="flex flex-col gap-2 mt-2">
                    <a href="http://hindutithi:8080/login" class="block text-center rounded-lg px-3 py-2 text-sm font-medium border border-[var(--color-border-subtle)] text-[var(--color-text-primary)] hover:bg-[var(--color-bg-elevated)]">Login</a>
                    <a href="http://hindutithi:8080#get-started" class="block text-center rounded-lg px-3 py-2 text-sm font-semibold bg-[var(--color-brand-saffron)] text-[#030817] hover:bg-[var(--color-brand-gold)]">Get Started</a>
                </div>
                    </div>
    </div>
</nav>

            
            <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">
                    <div class="space-y-16">
        
        
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-8 flex justify-center">
            <a href="/whats-new" class="group flex items-center gap-3 rounded-full border border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)]/50 px-4 py-1.5 text-xs font-medium text-[var(--color-text-secondary)] transition hover:bg-[var(--color-bg-elevated)] backdrop-blur-md">
                <span class="text-[var(--color-brand-saffron)]">✦</span>
                <span>Version 2.4.0 is now available — Multilingual support + expanded festival calculations</span>
                <span class="text-[var(--color-text-muted)] group-hover:text-[var(--color-text-primary)] transition">Read release notes &rarr;</span>
            </a>
        </div>

        
        <section class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                
                
                <div class="space-y-8 relative z-10">
                    <span class="inline-flex items-center text-[11px] font-bold uppercase tracking-[0.15em] text-[var(--color-brand-saffron)]">
                        Vittix Panchang Engine
                    </span>
                    <div class="space-y-6">
                        <h1 class="text-4xl font-semibold tracking-tight text-[var(--color-text-primary)] sm:text-5xl lg:text-[64px] lg:leading-[1.1]">
                            The Vedic Panchang Engine for Developers
                        </h1>
                        <p class="max-w-2xl text-lg leading-relaxed text-[var(--color-text-secondary)]">
                            Panchang, Kundli, planetary positions, Muhurta, festivals and Hindu calendar calculations &mdash; built for PHP, Laravel and REST APIs.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="#get-started" class="inline-flex h-12 items-center justify-center rounded-full bg-[var(--color-brand-saffron)] px-8 text-sm font-semibold text-[#030817] transition hover:bg-[var(--color-brand-gold)]">
                            Get Started &rarr;
                        </a>
                        <a href="http://hindutithi:8080/api/docs" class="inline-flex h-12 items-center justify-center rounded-full border border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)] px-8 text-sm font-semibold text-[var(--color-text-primary)] transition hover:bg-[var(--color-bg-elevated)]">
                            Read Documentation
                        </a>
                    </div>
                    <div class="pt-4">
                        <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang" target="_blank" class="text-sm font-medium text-[var(--color-text-muted)] hover:text-[var(--color-text-secondary)] transition">
                            ★ Star on GitHub
                        </a>
                    </div>
                </div>

                
                <div class="relative z-10 hidden md:block">
                    
                    <div class="absolute -inset-4 rounded-[2rem] bg-gradient-to-tr from-[var(--color-brand-saffron)]/10 to-transparent blur-2xl -z-10"></div>
                    
                    <div class="relative overflow-hidden rounded-[24px] border border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)] p-8 shadow-2xl backdrop-blur-xl">
                        
                        <div class="flex items-center justify-between border-b border-[var(--color-border-subtle)] pb-6">
                            <div>
                                <div class="text-[10px] font-bold uppercase tracking-[0.15em] text-[var(--color-text-muted)]">Today</div>
                                <div class="mt-1 text-lg font-semibold text-[var(--color-text-primary)]">15 August 2026</div>
                                <div class="text-sm text-[var(--color-text-secondary)]">Saturday</div>
                            </div>
                            <div class="relative flex h-12 w-12 items-center justify-center rounded-full bg-[var(--color-bg-elevated)] ring-1 ring-[var(--color-border-subtle)]">
                                <span class="text-xl text-[var(--color-brand-saffron)]">☀</span>
                            </div>
                        </div>

                        
                        <div class="space-y-5 border-b border-[var(--color-border-subtle)] py-6">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-semibold uppercase tracking-[0.1em] text-[var(--color-text-muted)]">Tithi</span>
                                <span class="text-sm font-medium text-[var(--color-text-primary)]">Krishna Trayodashi</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-semibold uppercase tracking-[0.1em] text-[var(--color-text-muted)]">Nakshatra</span>
                                <span class="text-sm font-medium text-[var(--color-text-primary)]">Pushya</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-semibold uppercase tracking-[0.1em] text-[var(--color-text-muted)]">Yoga</span>
                                <span class="text-sm font-medium text-[var(--color-text-primary)]">Siddha</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-semibold uppercase tracking-[0.1em] text-[var(--color-text-muted)]">Karana</span>
                                <span class="text-sm font-medium text-[var(--color-text-primary)]">Vanija</span>
                            </div>
                        </div>

                        
                        <div class="pt-6 flex justify-between">
                            <div>
                                <div class="text-xs text-[var(--color-text-muted)]">Sunrise</div>
                                <div class="mt-1 font-mono text-sm text-[var(--color-text-primary)]">06:12</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-[var(--color-text-muted)]">Sunset</div>
                                <div class="mt-1 font-mono text-sm text-[var(--color-text-primary)]">19:04</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-y-8 sm:grid-cols-4 rounded-2xl border border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)] p-6 backdrop-blur-xl divide-x divide-[var(--color-border-subtle)]">
                <div class="px-6 flex flex-col justify-center text-center sm:text-left">
                    <div class="text-2xl font-bold text-[var(--color-brand-gold)]">20+</div>
                    <div class="mt-1 text-[10px] font-bold uppercase tracking-wider text-[var(--color-text-muted)]">Panchang calculations</div>
                </div>
                <div class="px-6 flex flex-col justify-center text-center sm:text-left">
                    <div class="text-2xl font-bold text-[var(--color-brand-gold)]">9</div>
                    <div class="mt-1 text-[10px] font-bold uppercase tracking-wider text-[var(--color-text-muted)]">REST endpoints</div>
                </div>
                <div class="px-6 flex flex-col justify-center text-center sm:text-left border-l-0 sm:border-l sm:border-[var(--color-border-subtle)] mt-8 sm:mt-0 pt-8 sm:pt-0">
                    <div class="text-2xl font-bold text-[var(--color-brand-gold)]">PHP 8.3+</div>
                    <div class="mt-1 text-[10px] font-bold uppercase tracking-wider text-[var(--color-text-muted)]">Modern runtime</div>
                </div>
                <div class="px-6 flex flex-col justify-center text-center sm:text-left mt-8 sm:mt-0 pt-8 sm:pt-0">
                    <div class="text-2xl font-bold text-[var(--color-brand-gold)]">MIT</div>
                    <div class="mt-1 text-[10px] font-bold uppercase tracking-wider text-[var(--color-text-muted)]">Open source</div>
                </div>
            </div>
        </section>

        
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-16">
            <div class="grid gap-12 lg:grid-cols-2 items-center">
                <div class="space-y-6">
                    <span class="text-[11px] font-bold uppercase tracking-[0.15em] text-[var(--color-text-secondary)]">
                        What is Vittix Panchang?
                    </span>
                    <h2 class="text-3xl sm:text-[38px] font-semibold leading-[1.2] tracking-tight text-[var(--color-text-primary)]">
                        A precision Panchang calculation engine built for developers.
                    </h2>
                    <p class="text-base leading-relaxed text-[var(--color-text-secondary)]">
                        Generate tithi, nakshatra, yoga, karana, vara, sunrise, sunset, moonrise, moonset, festivals, muhurta and more with timezone-aware calculations.
                    </p>
                </div>

                
                <div class="relative overflow-hidden rounded-[20px] border border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)] shadow-xl">
                    <div class="flex items-center justify-between border-b border-[var(--color-border-subtle)] bg-[#0c1328] px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="h-3 w-3 rounded-full bg-red-500/20 ring-1 ring-red-500/30"></div>
                            <div class="h-3 w-3 rounded-full bg-amber-500/20 ring-1 ring-amber-500/30"></div>
                            <div class="h-3 w-3 rounded-full bg-green-500/20 ring-1 ring-green-500/30"></div>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-text-muted)]">PHP</span>
                    </div>
                    <div class="p-6 overflow-x-auto text-sm leading-loose">
                        <pre><code class="text-sky-300">use</code> <span class="text-slate-200">Vittix\Panchang\Panchang;</span>

<span class="text-purple-300">$panchang</span> <span class="text-slate-400">=</span> <span class="text-amber-200">Panchang</span><span class="text-slate-400">::</span><span class="text-blue-300">today</span><span class="text-slate-400">(</span><span class="text-green-300">'Mumbai'</span><span class="text-slate-400">);</span>

<span class="text-sky-300">echo</span> <span class="text-purple-300">$panchang</span><span class="text-slate-400">-&gt;</span><span class="text-slate-200">tithi</span><span class="text-slate-400">()-&gt;</span><span class="text-slate-200">name;</span>
<span class="text-sky-300">echo</span> <span class="text-purple-300">$panchang</span><span class="text-slate-400">-&gt;</span><span class="text-slate-200">nakshatra</span><span class="text-slate-400">()-&gt;</span><span class="text-slate-200">name;</span>
<span class="text-sky-300">echo</span> <span class="text-purple-300">$panchang</span><span class="text-slate-400">-&gt;</span><span class="text-blue-300">sunrise</span><span class="text-slate-400">();</span></pre>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-24">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-semibold tracking-tight text-[var(--color-text-primary)]">Comprehensive Calculation Engine</h2>
                <p class="mt-4 text-[var(--color-text-secondary)]">Everything you need to build Vedic astronomical applications.</p>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                
                <div class="rounded-2xl border border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)] p-6">
                    <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-[var(--color-brand-saffron)]/10 text-[var(--color-brand-saffron)] mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[var(--color-text-primary)]">Panchang & Time</h3>
                    <p class="mt-2 text-sm text-[var(--color-text-secondary)]">Tithi, Nakshatra, Yoga, Karana, Vara, Sunrise, Sunset, Moonrise, Moonset.</p>
                </div>
                
                <div class="rounded-2xl border border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)] p-6">
                    <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-[var(--color-brand-saffron)]/10 text-[var(--color-brand-saffron)] mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[var(--color-text-primary)]">Astrology & Kundli</h3>
                    <p class="mt-2 text-sm text-[var(--color-text-secondary)]">Planetary positions, Dashas, Ascendant, Rashi, and Navamsha charts.</p>
                </div>
                
                <div class="rounded-2xl border border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)] p-6">
                    <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-[var(--color-brand-saffron)]/10 text-[var(--color-brand-saffron)] mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[var(--color-text-primary)]">Muhurta & Electional</h3>
                    <p class="mt-2 text-sm text-[var(--color-text-secondary)]">Rahu Kalam, Yamaganda, Abhijit, Brahma Muhurta, and Choghadiya.</p>
                </div>
                
                <div class="rounded-2xl border border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)] p-6">
                    <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-[var(--color-brand-saffron)]/10 text-[var(--color-brand-saffron)] mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[var(--color-text-primary)]">Hindu Calendar</h3>
                    <p class="mt-2 text-sm text-[var(--color-text-secondary)]">Festivals, Ekadashi, Purnima, Amavasya, Sankranti, and fasts.</p>
                </div>
            </div>
        </section>

        
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-24">
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-[var(--color-border-subtle)] bg-gradient-to-b from-[#111A31] to-[#0B1226] p-8">
                    <h3 class="text-xl font-semibold text-[var(--color-text-primary)]">Native PHP</h3>
                    <p class="mt-3 text-[var(--color-text-secondary)] leading-relaxed">Drop-in Composer package for any PHP 8.3+ application. Zero external API dependencies required.</p>
                </div>
                <div class="rounded-2xl border border-[var(--color-border-subtle)] bg-gradient-to-b from-[#111A31] to-[#0B1226] p-8">
                    <h3 class="text-xl font-semibold text-[var(--color-text-primary)]">Laravel Ready</h3>
                    <p class="mt-3 text-[var(--color-text-secondary)] leading-relaxed">Seamless integration with Laravel via service providers, facades, and Artisan commands.</p>
                </div>
                <div class="rounded-2xl border border-[var(--color-border-subtle)] bg-gradient-to-b from-[#111A31] to-[#0B1226] p-8">
                    <h3 class="text-xl font-semibold text-[var(--color-text-primary)]">REST APIs</h3>
                    <p class="mt-3 text-[var(--color-text-secondary)] leading-relaxed">Expose JSON endpoints effortlessly to power mobile apps, SPAs, and microservices.</p>
                </div>
            </div>
        </section>

        
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-24 mb-16">
            <div class="rounded-[24px] border border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)] p-8 md:p-12 shadow-xl">
                <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-[0.15em] text-[var(--color-brand-saffron)]">
                            JSON Responses
                        </span>
                        <h2 class="mt-4 text-3xl font-semibold tracking-tight text-[var(--color-text-primary)]">
                            RESTful by design
                        </h2>
                        <p class="mt-4 text-base leading-relaxed text-[var(--color-text-secondary)]">
                            Every calculation can be accessed via standardized JSON APIs. Perfect for building headless architecture, mobile apps, or JavaScript frontends.
                        </p>
                        <ul class="mt-8 space-y-4">
                            <li class="flex items-center gap-3 text-sm text-[var(--color-text-secondary)]">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[var(--color-bg-elevated)] text-[var(--color-text-primary)] font-mono text-[10px]">GET</span>
                                <code class="text-[var(--color-text-primary)]">/api/day</code> &mdash; Daily Panchang
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[var(--color-text-secondary)]">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[var(--color-bg-elevated)] text-[var(--color-text-primary)] font-mono text-[10px]">GET</span>
                                <code class="text-[var(--color-text-primary)]">/api/moment</code> &mdash; Exact instant calculation
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[var(--color-text-secondary)]">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[var(--color-bg-elevated)] text-[var(--color-text-primary)] font-mono text-[10px]">GET</span>
                                <code class="text-[var(--color-text-primary)]">/api/kundli</code> &mdash; Birth chart generation
                            </li>
                            <li class="flex items-center gap-3 text-sm text-[var(--color-text-secondary)]">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[var(--color-bg-elevated)] text-[var(--color-text-primary)] font-mono text-[10px]">GET</span>
                                <code class="text-[var(--color-text-primary)]">/api/calendar</code> &mdash; Festival data
                            </li>
                        </ul>
                        <div class="mt-8">
                            <a href="http://hindutithi:8080/api/docs" class="text-sm font-medium text-[var(--color-brand-saffron)] hover:text-[var(--color-brand-gold)] transition">
                                View full API documentation &rarr;
                            </a>
                        </div>
                    </div>
                    <div class="rounded-2xl bg-[#030817] p-6 border border-[var(--color-border-subtle)] overflow-x-auto text-xs font-mono leading-loose text-slate-300 shadow-inner">
<pre>{
  <span class="text-sky-300">"status"</span>: <span class="text-green-300">"success"</span>,
  <span class="text-sky-300">"data"</span>: {
    <span class="text-sky-300">"date"</span>: <span class="text-green-300">"2026-08-15"</span>,
    <span class="text-sky-300">"location"</span>: <span class="text-green-300">"Mumbai"</span>,
    <span class="text-sky-300">"tithi"</span>: {
      <span class="text-sky-300">"name"</span>: <span class="text-green-300">"Krishna Trayodashi"</span>,
      <span class="text-sky-300">"end_time"</span>: <span class="text-green-300">"18:45:00"</span>
    },
    <span class="text-sky-300">"nakshatra"</span>: {
      <span class="text-sky-300">"name"</span>: <span class="text-green-300">"Pushya"</span>,
      <span class="text-sky-300">"end_time"</span>: <span class="text-green-300">"20:12:00"</span>
    }
  }
}</pre>
                    </div>
                </div>
            </div>
        </section>
        
        <section id="get-started" class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 mt-24 mb-24 text-center scroll-mt-24">
            <h2 class="text-3xl font-semibold tracking-tight text-[var(--color-text-primary)] sm:text-4xl">Ready to build your Vedic app?</h2>
            <p class="mt-4 text-lg text-[var(--color-text-secondary)]">Join the open-source community building the modern standard for Hindu calendrical computations.</p>
            
            <div class="mt-10 mb-8 flex justify-center">
                <div class="inline-flex items-center gap-4 rounded-xl border border-[var(--color-border-subtle)] bg-[#030817] px-6 py-4 shadow-inner">
                    <span class="text-[var(--color-text-muted)] font-mono">$</span>
                    <code class="text-sm font-mono text-slate-300">composer require vittix/panchang</code>
                </div>
            </div>

            <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
                <a href="http://hindutithi:8080/api/docs" class="inline-flex h-12 items-center justify-center rounded-full bg-[var(--color-brand-saffron)] px-8 text-sm font-semibold text-[#030817] transition hover:bg-[var(--color-brand-gold)]">
                    Read Documentation
                </a>
                <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang" target="_blank" class="inline-flex h-12 items-center justify-center rounded-full border border-[var(--color-border-subtle)] bg-[var(--color-bg-surface)] px-8 text-sm font-semibold text-[var(--color-text-primary)] transition hover:bg-[var(--color-bg-elevated)]">
                    View on GitHub
                </a>
            </div>
        </section>

    </div>
            </main>

            <footer class="border-t border-[var(--color-border-subtle)] py-12 mt-auto" style="background-color: var(--color-bg-base)">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                        <div class="space-y-2 text-sm text-[var(--color-text-muted)]">
                            <div class="flex items-center gap-2">
                                <span class="text-[var(--color-brand-saffron)] text-lg leading-none">☀</span>
                                <span class="font-semibold text-[var(--color-text-primary)] tracking-wide">Vittix Panchang</span>
                            </div>
                            <div class="mt-2">Professional Vedic Panchang engine for PHP, Laravel, and REST APIs.</div>
                            <div class="mt-4 text-xs">© 2026 Vittix. MIT Licensed.</div>
                        </div>
                        <div class="flex flex-wrap justify-center md:justify-end gap-x-8 gap-y-4 text-sm font-medium">
                            <a href="http://hindutithi:8080/day" class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] transition">Product</a>
                            <a href="http://hindutithi:8080/api/docs" class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] transition">Documentation</a>
                            <a href="http://hindutithi:8080/accuracy" class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] transition">Accuracy</a>
                            <a href="https://github.com/ketandholakia/Vittix-Vedic-Panchang" target="_blank" class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] transition">GitHub</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
