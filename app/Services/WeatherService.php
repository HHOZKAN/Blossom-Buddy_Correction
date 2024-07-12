<?php

namespace App\Services;

use App\Interfaces\WeatherServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherService implements WeatherServiceInterface
{
    protected $apiUrl = 'https://api.weatherapi.com/v1/forecast.json';
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('API_WEATHERAPI_KEY');
    }

    public function getWeatherForecast(string $city, int $daysForWeatherService): array
    {
        $cacheKey = "weather_forecast_{$city}";

        // Check if the data is in cache
        if (Cache::has($cacheKey)) {
            // Log::info("Weather data for {$city} retrieved from cache.");
            return Cache::get($cacheKey);
        }

        // If not, fetch from the API
        // Log::info("Weather data for {$city} retrieved from API.");
        $response = Http::get($this->apiUrl, [
            'key' => $this->apiKey,
            'q' => $city,
            'days' => $daysForWeatherService,
            'aqi' => 'no',
            'alerts' => 'no'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $forecastDays = $data['forecast']['forecastday'];

            // Extract only the data we need
            $reorganizedData = [];
            foreach ($forecastDays as $day) {
                $reorganizedData[] = [
                    'avghumidity' => $day['day']['avghumidity'],
                ];
            }

            // Cache the data for future requests
            Cache::put($cacheKey, $reorganizedData, now()->addHours(2));
            // Log::info("Weather data for {$city} cached.");
            return $reorganizedData;
        }

        // Log::error("Failed to retrieve weather data for {$city}.");
        return [];
    }
}