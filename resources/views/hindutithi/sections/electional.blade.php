@extends('layouts.app')

@section('title', 'Electional Astrology Checks | Hindutithi')
@section('meta_description', 'Electional astrology checks including Amrit Siddhi, Sarvartha Siddhi, Guru Pushya and Panchak Dosha.')

@section('content')
    <h1>Electional Checks</h1>
    <p>Moment: {{ $moment->format(DATE_ATOM) }}</p>
    <p>Electional evaluator instance available.</p>
    <div class="card">
        <div class="card-body">
            <p><strong>Evaluator:</strong> {{ get_class($e) }}</p>
            <p><strong>Available checks:</strong></p>
            <ul class="mb-0">
                <li>Amrit Siddhi Yoga</li>
                <li>Sarvartha Siddhi Yoga</li>
                <li>Guru Pushya Yoga</li>
                <li>Ravi Pushya Yoga</li>
                <li>Disha Shool</li>
                <li>Panchak Dosha</li>
                <li>Bhadra active</li>
            </ul>
        </div>
    </div>
@endsection
