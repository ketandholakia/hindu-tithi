@extends('layouts.app')

@section('title', 'Feature Unavailable | Hindutithi')
@section('meta_description', 'This Panchang feature is not available in the installed vittix/panchang version.')

@section('content')
    <div class="alert alert-warning">
        <h1 class="alert-heading">Feature not available</h1>
        <p>The requested feature <strong>{{ $feature }}</strong> is not available in the installed `vittix/panchang` version.</p>
        <p>If you need the full API, consider installing a different package version or consult the package README.</p>
    </div>
@endsection
