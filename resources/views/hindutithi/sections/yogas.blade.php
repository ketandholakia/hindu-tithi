@extends('layouts.app')

@section('title', 'Vedic Yogas — Classical Yoga Detector | Hindutithi')
@section('meta_description', 'Classical Vedic yogas detected from your birth chart data.')

@section('content')
    <h1>Detected Yogas</h1>
    @if(empty($yogas))
        <p>No classical yogas detected for this birth data.</p>
    @else
        <ul class="list-group">
            @foreach($yogas as $y)
                <li class="list-group-item"><strong>{{ $y->name }}</strong> — {{ $y->description }}</li>
            @endforeach
        </ul>
    @endif
@endsection
