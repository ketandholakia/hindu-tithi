<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PanchangReminderController
{
    public function edit(Request $request)
    {
        $settings = $request->user()->panchangReminderSetting;
        return view('hindutithi.reminders', ['settings' => $settings]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'send_time' => ['required', 'date_format:H:i'],
            'timezone' => ['required', 'timezone'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'elevation_meters' => ['nullable', 'numeric', 'between:-500,9000'],
            'notify_ekadashi' => ['nullable', 'boolean'],
            'notify_purnima' => ['nullable', 'boolean'],
            'notify_amavasya' => ['nullable', 'boolean'],
            'notify_festivals' => ['nullable', 'boolean'],
        ]);

        $data['enabled'] = $request->boolean('enabled');
        foreach (['notify_ekadashi', 'notify_purnima', 'notify_amavasya', 'notify_festivals'] as $field) {
            $data[$field] = $request->boolean($field);
        }
        $localNow = now($data['timezone']);
        $data['last_sent_on'] = $localNow->format('H:i') >= $data['send_time'] ? $localNow->toDateString() : null;

        $request->user()->panchangReminderSetting()->updateOrCreate([], $data);

        return back()->with('status', 'Reminder preferences saved.');
    }
}
