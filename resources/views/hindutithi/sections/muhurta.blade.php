@extends('layouts.app')

@section('title', 'Muhurta — Auspicious Day Timings | Hindutithi')
@section('meta_description', 'Daytime muhurta windows computed from sunrise and sunset for the selected date and location.')

@section('content')
    <h1>Muhurta</h1>
    <h2>Day Muhurtas</h2>
    @php
        $sunrise = $day['solarEvents']['sunrise'] ? new \DateTimeImmutable($day['solarEvents']['sunrise']) : null;
        $sunset = $day['solarEvents']['sunset'] ? new \DateTimeImmutable($day['solarEvents']['sunset']) : null;
    @endphp
    @if($sunrise && $sunset)
        @php $muh = $muhurta->getDayMuhurtas($sunrise, $sunset); @endphp
        <ul class="list-group">
            @foreach($muh as $w)
                <li class="list-group-item"><strong>{{ $w->name }}</strong> — {{ $w->start->format('H:i') }} → {{ $w->end->format('H:i') }}</li>
            @endforeach
        </ul>
    @else
        <p>Sunrise/sunset not available for this date/location.</p>
    @endif
@endsection
