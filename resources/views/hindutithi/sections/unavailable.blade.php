@extends('layouts.app')

@section('content')
    <div class="alert alert-warning">
        <h4 class="alert-heading">Feature not available</h4>
        <p>The requested feature <strong>{{ $feature }}</strong> is not available in the installed `vittix/panchang` version.</p>
        <p>If you need the full API, consider installing a different package version or consult the package README.</p>
    </div>
@endsection
