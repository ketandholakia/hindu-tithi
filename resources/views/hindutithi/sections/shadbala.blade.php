@extends('layouts.app')

@section('title', 'Shadbala — Planetary Strength Calculator | Hindutithi')
@section('meta_description', 'Shadbala planetary strength breakdown (Sthana, Dik, Kala, Chesta, Naisargika, Drik) computed from your birth data.')

@section('content')
    <h1>Shadbala (Planetary Strength)</h1>
    <table class="table table-sm">
        <thead><tr><th>Planet</th><th>StS</th><th>Dik</th><th>Kala</th><th>Chesta</th><th>Naisargika</th><th>Drik</th><th>Total (Rupas)</th></tr></thead>
        <tbody>
        @foreach($shadbala->planets as $vals)
            <tr>
                <td>{{ $vals->body->name }}</td>
                <td>{{ $vals->sthanaBala }}</td>
                <td>{{ $vals->dikBala }}</td>
                <td>{{ $vals->kalaBala }}</td>
                <td>{{ $vals->chestaBala }}</td>
                <td>{{ $vals->naisargikaBala }}</td>
                <td>{{ $vals->drikBala }}</td>
                <td>{{ number_format($vals->getTotalRupas(), 3) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
