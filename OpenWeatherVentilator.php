<?php
namespace ulrischa;

include_once('ICellarVentilator.php');

class OpenWeatherVentilator implements ICellarVentilator
{
    private $apiKey;
    public $latLong;
    public $humidityIndoor;
    public $humidityOutdoor;
    public $windSpeed;
    public $rainsNextHour;
    public $params = array('wind_max'=>8.3, 'pop_min' =>0.5);
    
    private $contentsNowForcast;
    

    function __construct($apiKey, $latLong, $humidityIndoor, $humidityOutdoor = null, $windSpeed = null, $rainsNextHour = null) {
        $this->apiKey = $apiKey;
        $this->latLong = $latLong;
        $this->humidityIndoor = $humidityIndoor;
        
        $this->humidityOutdoor = $humidityOutdoor;
        $this->windSpeed = $windSpeed;
        
        $this->setContentsNowForcast();
    }

    protected function urlNowForcast()
    {
        return "http://api.openweathermap.org/data/2.5/onecall?lat=" . $this->latLong['lat'] . "&lon=" . $this->latLong['lon'] . "&exclude=minutely,daily,alerts&appid=" . $this->apiKey;
    }

    protected function setContentsNowForcast()
    {
        $contents_now_forcast = file_get_contents($this->urlNowForcast());
        $this->contentsNowForcast = json_decode($contents_now_forcast, true);
    }
    
    public function getContentsNowForcast()
    {
        if (empty($this->contentsNowForcast)) {
            $this->setContentsNowForcast();
        }
        return $this->contentsNowForcast;
    }
    
    public function getLatLong(): array
    {
        return $this->latLong;
    }
    
    function getHumidityIndoor($weather_arr): float {
        return $this->humidityIndoor;
    }

    function setHumidityIndoor($humidityIndoor) {
        $this->humidityIndoor = $humidityIndoor;
    }
    
    public function rainsNextHour($weather_arr): bool
    {
        if (!empty($this->rainsNextHour)) return $this->rainsNextHour;
        if (empty($weather_arr)) $weather_arr = $this->getContentsNowForcast();
		
        if ($weather_arr['hourly']['0']['pop'] > $this->params['pop_min']) {
            return true;
        }
        return false;
    }
    
     public function getWindSpeed($weather_arr): float
    {
        if (!empty($this->windSpeed)) return $this->windSpeed;
        else {
            if (empty($weather_arr)) $weather_arr = $this->getContentsNowForcast();
            if (empty($weather_arr['current']['wind_speed'])) {
                return 0.0;
            }
            return $weather_arr['current']['wind_speed'];
        }
    }
    
    public function getHumidityOutdoor($weather_arr): float
    {
        if (!empty($this->humidityOutdoor)) return $this->humidityOutdoor;
        else {
            if (empty($weather_arr)) $weather_arr = $this->getContentsNowForcast();
            if (empty($weather_arr['current']['humidity'])) {
                return 0.0;
            }
            return $weather_arr['current']['humidity'];
        }
    }
    
    public function ventilateNow(): bool
    {
        if ($this->rainsNextHour(null) === true) {
            return false;
        }
        if ($this->getWindSpeed(null) > $this->params['wind_max']) {
            return false;
        }
        if ($this->getHumidityIndoor(null) <= $this->getHumidityOutdoor(null)) {
            return false;
        }
        return true;
    }
}


