@extends('layouts.app')

@section('content')
    <h1>Help</h1>
    <p class="lead">Use this app to explore Panchang calculations for a chosen date, time, timezone, and location.</p>

    <div class="card mb-3">
        <div class="card-body">
            <h5>How to use the form</h5>
            <ul>
                <li><strong>Date</strong> sets the civil date used for calculations.</li>
                <li><strong>Time</strong> is used by moment-based views like <code>/moment</code> and <code>/janmarashi</code>.</li>
                <li><strong>Timezone</strong> controls how sunrise, moon events, and calendar dates are interpreted.</li>
                <li><strong>Latitude</strong>, <strong>longitude</strong>, and <strong>elevation</strong> affect solar and lunar results.</li>
            </ul>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5>Main Pages</h5>
            <ul>
                <li><code>/day</code> - sunrise-based daily Panchang.</li>
                <li><code>/moment</code> - Panchang for one exact instant.</li>
                <li><code>/calendar</code> - Hindu calendar summary.</li>
                <li><code>/muhurta</code> - daytime muhurtas derived from sunrise and sunset.</li>
                <li><code>/electional</code> - electional astrology checks available in the package.</li>
            </ul>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5>API Testing</h5>
            <ul>
                <li><a href="{{ route('api.docs') }}">Open API Docs</a> to browse the test endpoints.</li>
                <li>If you are signed in, open <a href="{{ route('api.keys.index') }}">API Keys</a> to generate a personal key.</li>
                <li><code>/api</code> lists the available API routes.</li>
                <li><code>/api/examples</code> returns sample URLs for the current form values.</li>
                <li>The API expects the <code>X-API-KEY</code> header when protection is enabled.</li>
            </ul>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>Tips</h5>
            <ul class="mb-0">
                <li>If a page shows unavailable data, check whether the package supports that feature in your installed version.</li>
                <li>Use the same date and timezone across pages when comparing outputs.</li>
                <li>For debugging, the API endpoints are easier to inspect than the HTML views.</li>
            </ul>
        </div>
    </div>
@endsection
