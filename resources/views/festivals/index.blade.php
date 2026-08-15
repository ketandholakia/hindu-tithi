@extends('layouts.app')

@section('title', 'Hindu Festivals & Parva - Hindutithi')
@section('meta_description', 'Explore Hindu festivals according to Panchang.')

@section('content')
<div class="-mx-4 -my-8 px-4 py-8 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
    <div class="max-w-6xl mx-auto">
        
        <!-- Hero & Month Navigation -->
        <x-festival.month-navigation :month="$month" :year="$year" />

        <!-- Upcoming Festivals Row -->
        <x-festival.upcoming :upcoming="$upcoming" />

        <!-- Main Content Area -->
        <div class="mb-12">
            <h3 class="text-xl font-bold text-[var(--color-festival-secondary)] mb-4">All Festivals</h3>
            
            <!-- Category Filter -->
            <x-festival.category-filter :categories="$categories" :active="$activeCategory" />

            <!-- Festival Grid -->
            @php
                $hasFestivals = false;
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($daysWithFestivals as $day)
                    @foreach($day['festivals'] as $festival)
                        @if($activeCategory === 'All' || $festival['category'] === $activeCategory)
                            @php $hasFestivals = true; @endphp
                            <x-festival.card 
                                :festival="$festival"
                                :date="$day['date']"
                                :weekday="$day['weekday']"
                                :dayData="$day['dayData']"
                            />
                        @endif
                    @endforeach
                @endforeach
            </div>

            @if(!$hasFestivals)
                <div class="text-center py-16 bg-[var(--color-bg-surface)] rounded-2xl border border-[var(--color-border-subtle)] mt-4">
                    <div class="text-4xl mb-4">🪔</div>
                    <h4 class="text-lg font-bold text-[var(--color-text-primary)]">No festivals found</h4>
                    <p class="text-[var(--color-text-muted)] mt-1">There are no {{ $activeCategory !== 'All' ? $activeCategory : '' }} festivals in this month.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
