@extends('layouts.app')

@section('content')
    <h2>Muhurta</h2>
    <h5>Day Muhurtas</h5>
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
