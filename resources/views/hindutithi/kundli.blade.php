@extends('layouts.app')

@section('title', 'Kundli / Birth Chart Generator Demo | Hindutithi')
@section('meta_description', 'Generate a Kundli birth chart with planetary positions, ascendant, houses and Rashi information from your birth date, time and location.')

@section('content')
    <div class="space-y-8">
        <section class="overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-800 to-amber-600 p-8 shadow-2xl">
            <div class="max-w-4xl">
                <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-100">Kundli</span>
                <h1 class="mt-4 text-4xl font-semibold tracking-tight text-white">Generate a birth chart (Kundli)</h1>
                <p class="mt-4 text-base leading-7 text-slate-300">Enter birth details to generate a Kundli, planetary positions, ascendant, houses and Rashi information.</p>
                <div class="mt-6">
                    <div class="rounded-3xl border border-white/5 bg-slate-950/70 p-6">
                        <p class="text-sm text-slate-300">Example usage (PHP):</p>
                        <pre class="mt-3 whitespace-pre-wrap text-sm text-slate-100">&lt;?php
use Vittix\Panchang\Kundli;

$kundli = Kundli::fromBirthDetails([
    'date' => '1990-01-01',
    'time' => '10:30',
    'lat' => 19.0760,
    'lon' => 72.8777,
    'tz' => 'Asia/Kolkata',
]);

echo $kundli->ascendant->name;
echo $kundli->planets->get('sun')->longitude;
</pre>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
