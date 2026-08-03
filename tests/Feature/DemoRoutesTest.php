<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoRoutesTest extends TestCase
{
    public function test_root_redirects_to_home()
    {
        $this->get('/')
             ->assertRedirect('/home');
    }

    public function test_all_demo_routes_return_200()
    {
        $routes = [
            '/home', '/day', '/moment', '/janmarashi', '/ascendant',
            '/kundali', '/varga', '/vimshottari', '/shadbala', '/yogas',
            '/calendar', '/festivals', '/muhurta', '/electional',
        ];

        foreach ($routes as $route) {
            $response = $this->withSession([
                'date' => date('Y-m-d'),
                'time' => '06:00',
                'tz'   => 'Asia/Kolkata',
                'lat'  => 23.0225,
                'lon'  => 72.5714,
                'elev' => 0,
                'lang' => 'en',
            ])->get($route);

            $response->assertStatus(200, "Route $route did not return 200");
            $this->assertNotEmpty((string) $response->getContent(), "Route $route returned empty body");
        }
    }
}
