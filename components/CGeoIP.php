<?php

namespace dpodium\yii2\geoip\components;

use dpodium\yii2\geoip\models\Location;
use GeoIp2\Database\Reader;
use Yii;
use yii\base\Component;

class CGeoIP extends Component {
    
    public $cityDbPath = '@vendor/dpodium/yii2-geoip-city-db/db/GeoLite2-City.mmdb';
    
    public $countryDbPath = '@vendor/dpodium/yii2-geoip/components/db/GeoLite2-Country.mmdb';

    public $support_ipv6 = false;
    
    protected $previous_exception = null;
    
    public function init() {
        parent::init();
        $this->cityDbPath = Yii::getAlias($this->cityDbPath);
        $this->countryDbPath = Yii::getAlias($this->countryDbPath);
    }

    public function lookupLocation($ip = null) {
        $ip = $this->_getIP($ip);
        if (file_exists($this->cityDbPath)) {
            return $this->lookupLocationWithCityDb($ip);
        } else {
            return $this->lookupLocationWithCountryDb($ip);
        }
    }

    protected function lookupLocationWithCityDb($ip) {
        $reader = new Reader($this->cityDbPath);
        try {
            $record = $reader->city($ip);
            return $this->populateRecord($record);
        } catch (\Exception $ex) {
            $this->previous_exception = $ex;
            return null;
        }
    }

    protected function lookupLocationWithCountryDb($ip) {
        $reader = new Reader($this->countryDbPath);
        try {
            $record = $reader->country($ip);
            return $this->populateRecord($record);
        } catch (\Exception $ex) {
            $this->previous_exception = $ex;
            return null;
        }
    }
    
    protected function populateRecord($record) {
        if (isset($record)) {
            $location = new Location([
                'countryCode' => $record->country->isoCode,
                'countryName' => $record->country->name,
                'continentCode' => $record->continent->code,
                'continentName' => $record->continent->name,
                'city' => isset($record->city) ? $record->city->name : null,
                'postalCode' => isset($record->postal) ? $record->postal->code : null,
                'latitude' => isset($record->location) ? $record->location->latitude : null,
                'longitude' => isset($record->location) ? $record->location->longitude : null,
                'timeZone' => isset($record->location) ? $record->location->timeZone : null,
            ]);
            return $location;
        } else {
            return $record;
        }
    }

    public function lookupCountryCode($ip = null) {
        $ip = $this->_getIP($ip);
        $location = $this->lookupLocationWithCountryDb($ip);
        return isset($location) ? $location->countryCode : null;
    }

    public function lookupCountryName($ip = null) {
        $ip = $this->_getIP($ip);
        $location = $this->lookupLocationWithCountryDb($ip);
        return isset($location) ? $location->countryName : null;
    }

    protected function _getIP($ip = null) {
        if ($ip === null) {
            $ip = $this->getIPAddress();
        }
        return $ip;
    }

    protected function getIPAddress() {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_TRUE_CLIENT_IP'])) {
                return $_SERVER['HTTP_TRUE_CLIENT_IP'];
            }

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ($_SERVER['HTTP_X_FORWARDED_FOR'] != '')) {
                //get IP start from last element
                if (isset($_SERVER['HTTP_X_AMZ_CF_ID']) && ($_SERVER['HTTP_X_AMZ_CF_ID'] != '')) {
                    //if detect cloudfront CDN in use.
                    $ip_array = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                } else {
                    $ip_array = array_reverse(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
                }
                foreach ($ip_array as $ip) {
                    // trim for safety measures
                    $ip = trim($ip);
                    // attempt to validate IP
                    if ($this->_validateIp($ip)) {
                        return $ip;
                    }
                }
            }

            return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
        } else {
            $ip = getenv('HTTP_TRUE_CLIENT_IP');
            if ($this->_validateIp($ip)) {
                return $ip;
            }
            return getenv('REMOTE_ADDR');
        }
    }

    protected function _validateIp($ip) {
        $filter_var_flags = FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
        if ($this->support_ipv6) {
            $filter_var_flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
        }
        if ($ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, $filter_var_flags) === false) {
                return false;
            }
            return true;
        }
        return false;
    }

    public function getPreviousException() {
        $ex = $this->previous_exception;
        $this->previous_exception = null;
        return $ex;
    }
}
