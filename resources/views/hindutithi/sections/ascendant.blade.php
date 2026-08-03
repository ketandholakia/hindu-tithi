@extends('layouts.app')

@section('content')
    <h2>Ascendant (Lagna)</h2>
    <p><strong>Tropical Longitude:</strong> {{ $ascendant->tropicalLongitude }}</p>
    <p><strong>Sidereal Longitude:</strong> {{ $ascendant->siderealLongitude }}</p>
    <p><strong>Rashi:</strong> {{ $ascendant->rashi->name ?? $ascendant->rashi }}</p>
    <p><strong>Degree in sign:</strong> {{ $ascendant->degreeInSign }}</p>
@endsection
