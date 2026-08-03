<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hindutithi API Docs</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        body {
            margin: 0;
            background: #f6f7fb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .topbar {
            padding: 16px 24px;
            background: #111827;
            color: #fff;
        }
        .topbar h1 {
            margin: 0;
            font-size: 20px;
        }
        .topbar p {
            margin: 4px 0 0;
            color: #cbd5e1;
            font-size: 14px;
        }
        .authbar {
            display: flex;
            gap: 8px;
            align-items: center;
            padding: 12px 24px 0;
            background: #111827;
            color: #fff;
            flex-wrap: wrap;
        }
        .authbar label {
            font-size: 13px;
            color: #cbd5e1;
        }
        .authbar input {
            min-width: 280px;
            padding: 10px 12px;
            border: 1px solid #334155;
            border-radius: 8px;
            background: #0f172a;
            color: #fff;
        }
        .authbar button {
            padding: 10px 14px;
            border: 0;
            border-radius: 8px;
            background: #2563eb;
            color: #fff;
            cursor: pointer;
        }
        #swagger-ui {
            padding: 16px 0 40px;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>Hindutithi API Docs</h1>
        <p>Swagger UI for <code>/openapi.yaml</code></p>
        <div class="authbar">
            <label for="api-key">X-API-KEY</label>
            <input id="api-key" type="password" placeholder="Enter API key">
            <button id="save-key" type="button">Authorize</button>
            <button id="clear-key" type="button">Clear</button>
        </div>
    </div>
    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script>
        window.onload = function () {
            const ui = SwaggerUIBundle({
                url: '/openapi.yaml',
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [SwaggerUIBundle.presets.apis],
                layout: 'BaseLayout'
            });

            const input = document.getElementById('api-key');
            const saveButton = document.getElementById('save-key');
            const clearButton = document.getElementById('clear-key');
            const storageKey = 'hindutithi_api_key';

            const stored = localStorage.getItem(storageKey);
            if (stored) {
                input.value = stored;
                ui.preauthorizeApiKey('ApiKeyAuth', stored);
            }

            saveButton.addEventListener('click', function () {
                const value = input.value.trim();
                if (!value) return;
                localStorage.setItem(storageKey, value);
                ui.preauthorizeApiKey('ApiKeyAuth', value);
            });

            clearButton.addEventListener('click', function () {
                input.value = '';
                localStorage.removeItem(storageKey);
                ui.authActions.logout('ApiKeyAuth');
            });
        };
    </script>
</body>
</html>
