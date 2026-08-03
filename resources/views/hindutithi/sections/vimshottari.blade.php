@extends('layouts.app')

@section('content')
    <h2>Vimshottari Dasha</h2>
    <div>
        <ul class="list-group">
            @foreach($dashas as $d)
                <li class="list-group-item">
                    <strong>{{ $d->body->name }}</strong>
                    <div>{{ $d->start->format('Y-m-d') }} — {{ $d->end->format('Y-m-d') }}</div>
                    @if(!empty($d->subPeriods))
                        <ul>
                            @foreach($d->subPeriods as $s)
                                <li>{{ $s->body->name }}: {{ $s->start->format('Y-m-d') }} → {{ $s->end->format('Y-m-d') }}</li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endsection
