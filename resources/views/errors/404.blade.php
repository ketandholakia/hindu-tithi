@extends('layouts.app')

@section('title', 'Page Not Found — 404 | Hindutithi')
@section('meta_description', 'The page you are looking for could not be found.')

@section('content')
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="text-7xl font-black text-amber-500">404</div>
        <h1 class="mt-4 text-3xl font-semibold text-white">Page not found</h1>
        <p class="mt-3 max-w-md text-slate-400">The page you're looking for doesn't exist or has been moved.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('hindutithi.home') }}" class="inline-flex items-center justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">Go to Home</a>
            <a href="{{ route('hindutithi.day') }}" class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">View Daily Panchang</a>
            <a href="{{ route('api.docs') }}" class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">API Docs</a>
        </div>
    </div>
@endsection
