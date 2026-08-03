@extends('layouts.app')

@section('content')
    <h2>Kundali (Whole-sign houses)</h2>

    <div class="mb-3">
        <svg width="420" height="420" viewBox="0 0 420 420">
            <circle cx="210" cy="210" r="200" fill="#fff" stroke="#333" stroke-width="2"/>
            @php
                $angleStep = 360/12;
            @endphp
            @for($i=1;$i<=12;$i++)
                @php
                    $angle = -90 + ($i-1) * $angleStep; // start at top
                    $rad = deg2rad($angle);
                    $x = 210 + cos($rad) * 140;
                    $y = 210 + sin($rad) * 140;
                    $label = implode(', ', $houses[$i] ?? []);
                @endphp
                <text x="{{ $x }}" y="{{ $y }}" font-size="12" text-anchor="middle" fill="#111">{{ $i }}: {{ $label }}</text>
            @endfor
        </svg>
    </div>

    <div>
        <h4>Placements</h4>
        <table class="table table-sm">
            <thead><tr><th>Planet</th><th>Rashi</th><th>House</th><th>Longitude</th><th>Dignity</th></tr></thead>
            <tbody>
            @foreach($kundali->placements as $p)
                <tr>
                    <td>{{ $p->body->name }}</td>
                    <td>{{ $p->rashi->name }}</td>
                    <td>{{ $p->house }}</td>
                    <td>{{ number_format($p->longitude, 4) }}</td>
                    <td>{{ $p->dignity?->name ?? '' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
