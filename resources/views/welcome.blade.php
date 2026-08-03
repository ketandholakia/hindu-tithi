<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hindutithi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(255, 180, 75, 0.18), transparent 35%),
                radial-gradient(circle at bottom right, rgba(30, 64, 175, 0.12), transparent 30%),
                linear-gradient(180deg, #fffdf7 0%, #f6f4ee 100%);
        }
        .hero {
            max-width: 960px;
            margin: 8vh auto;
            padding: 3rem;
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(10px);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
        }
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.8rem;
            border-radius: 999px;
            background: #fff7ed;
            color: #9a3412;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            line-height: 1;
            letter-spacing: -0.04em;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }
        .hero p {
            color: #475569;
            font-size: 1.05rem;
            max-width: 58ch;
        }
        .actions a {
            min-width: 180px;
        }
        .feature {
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 18px;
            padding: 1rem 1.1rem;
            background: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body>
    <main class="hero">
        <div class="pill">Hindutithi Panchang demo</div>
        <h1>Daily Panchang, calendar, and API testing in one place.</h1>
        <p>
            Explore sunrise-based day calculations, moment-based astrology output, calendar summaries,
            muhurta calculations, and developer APIs backed by the Vittix Panchang package.
        </p>

        <div class="d-flex flex-wrap gap-3 actions mt-4">
            <a class="btn btn-primary btn-lg" href="{{ route('hindutithi.home') }}">Open app</a>
            <a class="btn btn-outline-secondary btn-lg" href="{{ route('hindutithi.help') }}">Help</a>
            <a class="btn btn-outline-secondary btn-lg" href="{{ route('api.docs') }}">API docs</a>
        </div>

        <div class="row g-3 mt-5">
            <div class="col-md-4">
                <div class="feature h-100">
                    <strong>Human-friendly views</strong>
                    <p class="mb-0 mt-2">Use the app pages to inspect day, moment, calendar, and muhurta output.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature h-100">
                    <strong>User API keys</strong>
                    <p class="mb-0 mt-2">Signed-in users can create and revoke personal keys for API testing.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature h-100">
                    <strong>Swagger UI</strong>
                    <p class="mb-0 mt-2">Browse the OpenAPI spec directly in the browser and authorize with a key.</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
