@extends('layouts.app')

@section('title', 'Ascendant (Lagna) Calculator | Hindutithi')
@section('meta_description', 'Calculate your ascendant (Lagna) with tropical and sidereal longitudes for the selected birth details.')

@section('content')
    <h1>Ascendant (Lagna)</h1>
    <p><strong>Tropical Longitude:</strong> {{ $ascendant->tropicalLongitude }}</p>
    <p><strong>Sidereal Longitude:</strong> {{ $ascendant->siderealLongitude }}</p>
    <p><strong>Rashi:</strong> {{ $ascendant->rashi->name ?? $ascendant->rashi }}</p>
    <p><strong>Degree in sign:</strong> {{ $ascendant->degreeInSign }}</p>
@endsection
