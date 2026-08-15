<?php

namespace App\Services\Festival\Contracts;

interface PanchangProvider
{
    /**
     * Get sunrise and sunset times for a specific date and location.
     */
    public function getSunriseSunset(string $date, float $lat, float $lon): array;

    /**
     * Find the precise start and end times of a specific lunar tithi in a given year.
     * Returns an array of occurrences if it happens multiple times.
     */
    public function findTithiTimings(int $year, string $month, string $paksha, int $tithi): array;

    /**
     * Find the precise start and end times of a specific nakshatra.
     */
    public function findNakshatraTimings(int $year, string $nakshatra): array;

    /**
     * Find the exact time the sun enters a specific rashi (Sankranti).
     */
    public function findSankrantiTimings(int $year, string $rashi): array;
}
