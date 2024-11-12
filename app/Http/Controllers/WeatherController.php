<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use App\Models\WeatherData;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
        $this->middleware('auth');
    }

    public function fetchWeather(Request $request)
    {
        $city = $request->input('city');

        if (empty($city)) {
            return response()->json(['message' => 'City is required'], 400);
        }

        $weatherData = $this->weatherService->fetchWeatherData($city);

        if (!$weatherData) {
            return response()->json(['message' => 'Failed to fetch weather data'], 500);
        }

        // Save weather data to the database
        WeatherData::updateOrCreate(
            ['location' => $city],
            $weatherData
        );

        return response()->json(['message' => 'Weather data updated successfully']);
    }

    public function weatherDataTable()
    {
        $weatherData = WeatherData::latest()->get();

        return DataTables::of($weatherData)
            ->addColumn('action', function ($data) {
                return '<button class="btn btn-primary fetch-weather" data-city="' . $data->location . '">Fetch Weather</button>';
            })
            ->make(true);
    }

    public function dashboard()
    {
        $weatherData = WeatherData::latest()->get();
        return view('dashboard', compact('weatherData'));
    }
}
