<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\PanchangExtendedApiController;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WebAdvancedPanchangController
{
    private function input(Request $request): array
    {
        $session = $request->session();
        return array_merge([
            'date' => $session->get('date', now()->toDateString()),
            'time' => $session->get('time', '06:00'),
            'tz' => $session->get('tz', 'Asia/Kolkata'),
            'lat' => $session->get('lat', 23.0225),
            'lon' => $session->get('lon', 72.5714),
            'elev' => $session->get('elev', 0),
            'lang' => $session->get('lang', 'en'),
        ], $request->only(['date', 'time', 'tz', 'lat', 'lon', 'elev', 'lang', 'end_date', 'fields', 'year']));
    }

    private function api(array $query, string $method): array
    {
        $request = Request::create('/api/' . $method, 'GET', $query);
        return app(PanchangExtendedApiController::class)->{$method}($request)->getData(true);
    }

    private function validateCommon(array $input, bool $needsTime = false, bool $needsLocation = true): void
    {
        Validator::make($input, array_merge([
            'date' => ['required', 'date'],
            'tz' => ['required', 'timezone'],
        ], $needsTime ? ['time' => ['required', 'date_format:H:i']] : [], $needsLocation ? [
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lon' => ['required', 'numeric', 'between:-180,180'],
            'elev' => ['nullable', 'numeric', 'between:-500,9000'],
        ] : []))->validate();
    }

    public function timeline(Request $request)
    {
        $input = $this->input($request);
        $this->validateCommon($input, false, true);
        $input['end_date'] ??= (new DateTimeImmutable($input['date']))->modify('+1 day')->format('Y-m-d');
        Validator::make($input, ['date' => ['required', 'date'], 'end_date' => ['required', 'date', 'after:date'], 'fields' => ['nullable', 'string']])->validate();
        $result = $this->api($input, 'timeline');
        $windows = [];
        foreach (($result['data'] ?? []) as $field => $rows) {
            foreach ($rows as $row) $windows[] = ['field' => ucfirst($field), ...$row];
        }
        usort($windows, fn ($a, $b) => strcmp($a['start'] ?? '', $b['start'] ?? ''));
        return view('hindutithi.advanced.timeline', compact('input', 'result', 'windows'));
    }

    public function sankranti(Request $request)
    {
        $input = $this->input($request);
        $year = (int) $request->query('year', (int) date('Y'));
        $request->validate(['year' => ['nullable', 'integer', 'between:1800,2200']]);
        $query = ['year' => $year, 'tz' => $input['tz'], 'lang' => $input['lang']];
        Validator::make($query, ['tz' => ['required', 'timezone']])->validate();
        $result = $this->api($query, 'sankranti');
        return view('hindutithi.advanced.sankranti', compact('input', 'result', 'year'));
    }

    public function astronomy(Request $request)
    {
        $input = $this->input($request);
        $this->validateCommon($input, false, true);
        $result = $this->api($input, 'astronomy');
        return view('hindutithi.advanced.astronomy', compact('input', 'result'));
    }

    public function moonSign(Request $request)
    {
        $input = $this->input($request);
        $this->validateCommon($input, true, false);
        $result = $this->api($input, 'moonSign');
        return view('hindutithi.advanced.moon_sign', compact('input', 'result'));
    }

    public function electional(Request $request)
    {
        $input = $this->input($request);
        $this->validateCommon($input, true, true);
        $result = $this->api($input, 'evaluateElectional');
        return view('hindutithi.sections.electional', compact('input', 'result'));
    }
}
