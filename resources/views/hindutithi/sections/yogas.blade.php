@extends('layouts.app')

@section('content')
    <h2>Detected Yogas</h2>
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
