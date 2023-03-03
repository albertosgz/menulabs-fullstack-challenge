<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Get forecast using The National Weather Service
 * https://www.weather.gov/documentation/services-web-api
 */
class Weather
{
    const API_BASE = 'https://api.weather.gov/';
    const TIMEOUT = 500;

    function getForecast(float $latitude, float $longitude) {

        try {
            // INFO Code ready to support hourly forecast
            $urls = $this->getGridForecastUrls($latitude, $longitude);
            return [
                'status' => true,
                'periods' => $this->getForecastByUrl($urls['default'])
            ];
        } catch (\Illuminate\Http\Client\ConnectionException) {
            return [
                'status' => false,
                'error' => 'Connection timeout',
            ];
        } catch (NotFoundHttpException) {
            return [
                'status' => false,
                'error' => 'Invalid coordinates',
            ];
        }
    }

    private function getGridForecastUrls(float $latitude, float $longitude) {
        $response = Http::timeout(self::TIMEOUT)->get(self::API_BASE . "points/${latitude},${longitude}");
        if (Arr::get($response, 'status') === 404) {
            throw new NotFoundHttpException;
        }
        return [
            'default' => Arr::get($response, 'properties.forecast'),
            'hourly' => Arr::get($response, 'properties.forecastHourly'),
        ];
    }

    private function getForecastByUrl(string $url) {
        $response = Http::timeout(self::TIMEOUT)->get($url);
        return Arr::get($response, 'properties.periods', []);
    }

}
