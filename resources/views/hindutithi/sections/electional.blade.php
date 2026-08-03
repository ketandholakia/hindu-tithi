@extends('layouts.app')

@section('content')
    <h2>Electional Checks</h2>
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
