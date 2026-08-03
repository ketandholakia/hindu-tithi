@extends('layouts.app')

@section('content')
    <h2>Upcoming Festivals (next ~120 days)</h2>
    @if(empty($list))
        <p>No festivals found in the next 120 days for this location.</p>
    @else
        <ul class="list-group">
            @foreach($list as $item)
                <li class="list-group-item"><strong>{{ $item['date']->format('Y-m-d') }}</strong>: {{ implode(', ', $item['names']) }}</li>
            @endforeach
        </ul>
    @endif
@endsection
