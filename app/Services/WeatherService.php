<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = env('WEATHER_API_URL');
        $this->apiKey = env('WEATHER_API_KEY');
    }

    public function fetchWeatherData($city)
    {
        try {
            // Check the cache for existing weather data
            $cachedData = Cache::get("weather_{$city}");
            if ($cachedData) {
                return $cachedData;
            }

            // Make the API call
            $response = Http::get("{$this->apiUrl}?access_key={$this->apiKey}&query={$city}");

            if ($response->failed()) {
                Log::error("Weather API request failed for city: {$city}", ['error' => $response->body()]);
                return null;
            }

            $data = $response->json();

            if (!isset($data['current']['temperature'])) {
                return null;
            }

            $weatherData = [
                'location' => $city,
                'temperature_celsius' => $data['current']['temperature'],
                'temperature_fahrenheit' => $this->celsiusToFahrenheit($data['current']['temperature']),
                'retrieved_at' => now(),
            ];

            // Cache the result for 5 minutes to avoid API rate limits
            Cache::put("weather_{$city}", $weatherData, 300); // Cache for 5 minutes

            return $weatherData;
        } catch (\Exception $e) {
            Log::error("Error fetching weather data for city: {$city}", ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function celsiusToFahrenheit($celsius)
    {
        return ($celsius * 9 / 5) + 32;
    }
}
