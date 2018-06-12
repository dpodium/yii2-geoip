<?php

namespace dpodium\yii2\geoip\models;

class Location extends \yii\base\Model {
    
    public $countryCode;
    public $countryName;
    public $continentCode;
    public $continentName;
    public $city;
    public $postalCode;
    public $latitude;
    public $longitude;
    public $timeZone;
}
