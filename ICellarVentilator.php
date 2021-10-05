<?php
namespace ulrischa;

interface ICellarVentilator
{
    public function getLatLong(): array;

    public function rainsNextHour($weather_array): bool;

    public function getWindSpeed($weather_array): float;

    public function getHumidityIndoor($weather_array): float;

    public function getHumidityOutdoor($weather_array): float;

    public function ventilateNow(): bool; 
}
