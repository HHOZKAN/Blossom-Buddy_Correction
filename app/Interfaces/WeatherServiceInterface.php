<?php

namespace App\Interfaces;

interface WeatherServiceInterface
{
    public function getWeatherForecast(string $city, int $daysForWeatherService): array;
}