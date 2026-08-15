<?php

namespace App\Services\Festival;

use App\Services\Festival\Contracts\PanchangProvider;

class MockPanchangProvider implements PanchangProvider
{
    public function getSunriseSunset(string $date, float $lat, float $lon): array
    {
        return [
            'sunrise' => $date . ' 06:00:00',
            'sunset'  => $date . ' 18:00:00',
        ];
    }

    public function findTithiTimings(int $year, string $month, string $paksha, int $tithi): array
    {
        // Return dummy data for testing purposes
        return [
            [
                'start' => $year . '-04-15 05:30:00',
                'end'   => $year . '-04-16 04:45:00',
            ]
        ];
    }

    public function findNakshatraTimings(int $year, string $nakshatra): array
    {
        return [
            [
                'start' => $year . '-08-15 10:00:00',
                'end'   => $year . '-08-16 11:30:00',
            ]
        ];
    }

    public function findSankrantiTimings(int $year, string $rashi): array
    {
        return [
            [
                'start' => $year . '-01-14 14:00:00',
            ]
        ];
    }
}
