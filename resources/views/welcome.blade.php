<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vittix Panchang · Hindutithi</title>
    <meta name="description" content="Vittix Panchang is a PHP and Laravel Panchang library with daily tithi, nakshatra, yoga, muhurta, festivals, and timezone-aware API support.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(255, 180, 75, 0.18), transparent 35%),
                radial-gradient(circle at bottom right, rgba(30, 64, 175, 0.12), transparent 30%),
                linear-gradient(180deg, #fffdf7 0%, #f6f4ee 100%);
            color: #1f2937;
        }
        .hero {
            padding: 3rem 2rem;
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(10px);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
        }
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.9rem;
            border-radius: 999px;
            background: #fff7ed;
            color: #9a3412;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4rem);
            line-height: 1.05;
            margin-top: 1rem;
            margin-bottom: 1rem;
            letter-spacing: -0.04em;
        }
        .hero p {
            color: #475569;
            font-size: 1.05rem;
            max-width: 68ch;
        }
        .section-heading {
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .code-block {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
            font-size: 0.96rem;
            background: #0f172a;
            color: #f8fafc;
            padding: 1rem;
            border-radius: 16px;
            overflow-x: auto;
            line-height: 1.6;
            border: 1px solid rgba(148, 163, 184, 0.18);
        }
        .feature-card {
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 18px;
            padding: 1.3rem;
            background: rgba(255, 255, 255, 0.82);
            min-height: 100px;
        }
        .feature-card strong {
            display: block;
            margin-bottom: 0.65rem;
        }
        .badge-soft {
            background: rgba(59, 130, 246, 0.12);
            color: #1d4ed8;
            font-weight: 700;
            border-radius: 999px;
            padding: 0.3rem 0.7rem;
            font-size: 0.78rem;
        }
    </style>
</head>
<body>
    <main class="container py-5">
        <section class="hero">
            <div class="pill">Vittix Panchang</div>
            <div class="row align-items-center gy-4">
                <div class="col-lg-7">
                    <h1>Fast, accurate Panchang for PHP, Laravel, and REST APIs.</h1>
                    <p>
                        Deliver daily tithi, nakshatra, yoga, karana, sunrise, sunset, festival calendars, and muhurta output with timezone-aware support.
                        This demo surface shows the same calculation engine used by the `vittix/panchang` package.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a class="btn btn-primary btn-lg" href="{{ route('hindutithi.home') }}">Live Demo</a>
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('api.docs') }}">Documentation</a>
                        <a class="btn btn-outline-secondary btn-lg" href="https://github.com/vittix/panchang" target="_blank">Star on GitHub</a>
                    </div>
                    <div class="mt-4 small text-muted">
                        <a class="me-3" href="https://github.com/vittix/panchang/issues" target="_blank">Report Issue</a>
                        <a class="me-3" href="{{ route('api.docs') }}">API reference</a>
                        <a href="{{ route('hindutithi.home') }}">Demo</a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="feature-card">
                        <span class="badge-soft">Install</span>
                        <pre class="code-block mb-0">composer require vittix/panchang</pre>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <p class="text-uppercase text-muted fs-7 mb-1">What is Vittix Panchang?</p>
                    <h2 class="section-heading">A PHP Panchang calculation engine for developers.</h2>
                </div>
                <span class="badge-soft">Laravel ready</span>
            </div>
            <p class="text-muted mb-4">
                The package provides a clean API for daily and moment-based Panchang results, plus a REST API layer for apps, widgets, and external services.
                It is designed to help PHP developers evaluate sunrise-based Panchang, astronomical events, festival dates, and muhurta windows in seconds.
            </p>

            <div class="row row-cols-1 row-cols-md-2 g-3">
                <div class="col">
                    <div class="feature-card">
                        <strong>Package type</strong>
                        <p class="mb-0">PHP library, Laravel package, and REST API demo in one repository.</p>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-card">
                        <strong>Use cases</strong>
                        <p class="mb-0">Developer integrations, Hindu calendar apps, festival schedules, astrology tools, and daily Panchang widgets.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-5">
            <h2 class="section-heading">Supported calculations</h2>
            <p class="text-muted">Every supported Panchang element is listed so developers know what the package can deliver.</p>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 mt-3">
                <div class="col"><div class="feature-card">✔ Tithi</div></div>
                <div class="col"><div class="feature-card">✔ Nakshatra</div></div>
                <div class="col"><div class="feature-card">✔ Yoga</div></div>
                <div class="col"><div class="feature-card">✔ Karana</div></div>
                <div class="col"><div class="feature-card">✔ Vara</div></div>
                <div class="col"><div class="feature-card">✔ Sunrise</div></div>
                <div class="col"><div class="feature-card">✔ Sunset</div></div>
                <div class="col"><div class="feature-card">✔ Moonrise</div></div>
                <div class="col"><div class="feature-card">✔ Moonset</div></div>
                <div class="col"><div class="feature-card">✔ Rahu Kalam</div></div>
                <div class="col"><div class="feature-card">✔ Gulika</div></div>
                <div class="col"><div class="feature-card">✔ Yamaganda</div></div>
                <div class="col"><div class="feature-card">✔ Abhijit Muhurta</div></div>
                <div class="col"><div class="feature-card">✔ Brahma Muhurta</div></div>
                <div class="col"><div class="feature-card">✔ Festivals</div></div>
                <div class="col"><div class="feature-card">✔ Ekadashi</div></div>
                <div class="col"><div class="feature-card">✔ Chaturthi</div></div>
                <div class="col"><div class="feature-card">✔ Amavasya</div></div>
                <div class="col"><div class="feature-card">✔ Purnima</div></div>
                <div class="col"><div class="feature-card">✔ Sankranti</div></div>
            </div>
        </section>

        <section class="mt-5">
            <div class="row g-4">
                <div class="col-lg-7">
                    <h2 class="section-heading">API Example</h2>
                    <p class="text-muted">A clean PHP footprint helps developers decide quickly whether the library fits their project.</p>
                    <div class="code-block">
                        &lt;?php
                        <br>$panchang = Panchang::today('Mumbai');
                        <br>echo $panchang->tithi->name;    // e.g. Shukla Paksha Tritiya
                        <br>echo $panchang->nakshatra->name; // e.g. Ashwini
                        <br>echo $panchang->sunrise;         // ISO datetime or formatted time
                    </div>
                </div>
                <div class="col-lg-5">
                    <h2 class="section-heading">REST API</h2>
                    <p class="text-muted">Available endpoints for line-of-business apps and external integrations.</p>
                    <ul class="list-unstyled">
                        <li>GET <code>/api/day</code></li>
                        <li>GET <code>/api/moment</code></li>
                        <li>GET <code>/api/calendar</code></li>
                        <li>GET <code>/api/muhurta</code></li>
                        <li>GET <code>/api/electional</code></li>
                        <li>GET <code>/api/examples</code></li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="mt-5">
            <div class="row g-4">
                <div class="col-md-6">
                    <h2 class="section-heading">Composer installation</h2>
                    <p class="text-muted">Developers can install the package immediately and begin using it in any PHP or Laravel project.</p>
                    <div class="code-block">composer require vittix/panchang</div>
                </div>
                <div class="col-md-6">
                    <h2 class="section-heading">Documentation & resources</h2>
                    <p class="text-muted">The homepage should link directly to docs, API specs, demo pages, and the GitHub repository.</p>
                    <ul class="list-unstyled">
                        <li>Documentation: <a href="{{ route('api.docs') }}">/api/docs</a></li>
                        <li>Demo: <a href="{{ route('hindutithi.home') }}">/home</a></li>
                        <li>GitHub: <a href="https://github.com/vittix/panchang" target="_blank">vittix/panchang</a></li>
                        <li>Report issue: <a href="https://github.com/vittix/panchang/issues" target="_blank">/issues</a></li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="mt-5 mb-5">
            <h2 class="section-heading">Roadmap</h2>
            <p class="text-muted">The next steps focus on documentation, accuracy transparency, API reference, and ecosystem expansion.</p>
            <div class="row row-cols-1 row-cols-md-2 g-3">
                <div class="col"><div class="feature-card">Publish package on Packagist and version as v1.0</div></div>
                <div class="col"><div class="feature-card">Add an Accuracy page for ephemeris, ayanamsa, timezone, DST, and historical support</div></div>
                <div class="col"><div class="feature-card">Build dedicated pages for API, documentation, examples, and changelog</div></div>
                <div class="col"><div class="feature-card">Expand integrations: PHP library, Laravel package, REST API, JavaScript SDK, Flutter, WordPress</div></div>
            </div>
        </section>
    </main>
</body>
</html>
