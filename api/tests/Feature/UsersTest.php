<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @group user
     */
    public function test_get_users()
    {
        $users = User::factory(20)->create();

        $this->getJson('/api/users')
            ->assertStatus(200)
            ->assertJson([
            'data' => [
                ['id' => $users[0]->id],
                ['id' => $users[1]->id],
                ['id' => $users[2]->id],
                ['id' => $users[3]->id],
                ['id' => $users[4]->id],
                ['id' => $users[5]->id],
                ['id' => $users[6]->id],
                ['id' => $users[7]->id],
                ['id' => $users[8]->id],
                ['id' => $users[9]->id],
            ],
        ]);

        $this->getJson('/api/users?page=1')
            ->assertStatus(200)
            ->assertJson([
            'data' => [
                ['id' => $users[0]->id],
                ['id' => $users[1]->id],
                ['id' => $users[2]->id],
                ['id' => $users[3]->id],
                ['id' => $users[4]->id],
                ['id' => $users[5]->id],
                ['id' => $users[6]->id],
                ['id' => $users[7]->id],
                ['id' => $users[8]->id],
                ['id' => $users[9]->id],
            ],
        ]);

        $this->getJson('/api/users?page=2')
            ->assertStatus(200)
            ->assertJson([
            'data' => [
                ['id' => $users[10]->id],
                ['id' => $users[11]->id],
                ['id' => $users[12]->id],
                ['id' => $users[13]->id],
                ['id' => $users[14]->id],
                ['id' => $users[15]->id],
                ['id' => $users[16]->id],
                ['id' => $users[17]->id],
                ['id' => $users[18]->id],
                ['id' => $users[19]->id],
            ],
        ]);
    }

    /**
     * @group user
     */
    public function test_get_user()
    {
        $latitude = 39.7456;
        $longitude = -97.0892;
        $user = User::factory()->create([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        $periods = [
            [
                'number' => 1,
                'name' => "This Afternoon",
                'temperature' => 43,
                'temperatureUnit' => "F",
                'probabilityOfPrecipitation' => [
                    'unitCode: "wmoUnit:percent',
                    'value' => 4
                ],
                'windSpeed' =>"10 mph",
                'windDirection' =>"NE",
                'icon' => "https://api.weather.gov/icons/land/day/bkn?size=medium",
                'shortForecast' => "Partly Sunny",
                'detailedForecast' => "Partly sunny, with a high near 43. Northeast wind around 10 mph."
            ],
            [
                'number' => 2,
                'name' => "Tonight",
                'temperature' => 27,
                'temperatureUnit' => "F",
                'probabilityOfPrecipitation' => [
                    'unitCode: "wmoUnit:percent',
                    'value' => null,
                ],
                'windSpeed' => "10 mph",
                'windDirection' => "NE",
                'icon' => "https' =>//api.weather.gov/icons/land/night/bkn?size=medium",
                'shortForecast' => "Mostly Cloudy",
                'detailedForecast' => "Mostly cloudy, with a low around 27. Northeast wind around 10 mph."
            ],
        ];

        Http::fake([
            "api.weather.gov/points/$latitude,$longitude" => Http::response([
                'properties' => [
                    'forecast' => "api.weather.gov/gridpoints/TOP/31,80/forecast",
                    'forecastHourly' => "api.weather.gov/gridpoints/TOP/31,80/forecast/hourly",
                ],
            ], 200),
            'api.weather.gov/gridpoints/TOP/31,80/forecast' => Http::response([
                'properties' => [
                    'periods' => $periods
                ]
            ], 200),
            'api.weather.gov/gridpoints/TOP/31,80/forecast/hourly' => Http::response([
                'properties' => [
                    'periods' => $periods
                ]
            ], 200),
        ]);

        $this->getJson('/api/users/' . $user->id)
            ->assertStatus(200)
            ->assertJson([
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'forecast' => [
                'status' => true,
                'periods' => $periods
            ]
        ]);
    }

    /**
     * @group user
     */
    public function test_forecast_coordinates_not_found()
    {
        $latitude = 39.7456;
        $longitude = -97.0892;
        $user = User::factory()->create([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        Http::fake([
            "api.weather.gov/points/$latitude,$longitude" => Http::response([
                'status' => 404,
                'type' => 'https://api.weather.gov/problems/InvalidPoint',
            ], 200),
        ]);

        $this->getJson('/api/users/' . $user->id)
            ->assertStatus(200)
            ->assertJson([
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'forecast' => [
                'status' => false,
                'error' => 'Invalid coordinates'
            ]
        ]);
    }

    /**
     * @group user
     */
    public function test_use_forecast_cache()
    {
        $forecast = [
            'status' => true,
            'periods' => [
                ['foo' => 'bar',],
            ],
        ];

        $latitude = 19.0001;
        $longitude = -23.9999;
        $user = User::factory()->create([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
        $cacheKey = $latitude . '_' . $longitude;

        Cache::shouldReceive('get')
            ->once()
            ->with($cacheKey)
            ->andReturn($forecast);

        $this->getJson('/api/users/' . $user->id)
            ->assertStatus(200)
            ->assertJson([
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'forecast' => $forecast
        ]);
    }
}
