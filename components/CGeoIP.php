<?php

namespace dpodium\yii2\geoip\components;

use dpodium\yii2\geoip\models\Location;
use GeoIp2\Database\Reader;
use Yii;
use yii\base\Component;

class CGeoIP extends Component {

    public $cityDbPath;

    public function init() {
        parent::init();
        if (!empty($this->cityDbPath)) {
            $this->cityDbPath = Yii::getAlias($this->cityDbPath);
            if (empty($this->cityDbPath)) {
                throw new \yii\base\InvalidConfigException('City DB does not exist at ' . $this->cityDbPath);
            }
        }
    }

    public function lookupLocation($ip = null) {
        $ip = $this->_getIP($ip);
        if (!empty($this->cityDbPath)) {
            return $this->lookupLocationWithCityDb($ip);
        } else {
            return $this->lookupLocationWithCountryDb($ip);
        }
    }

    protected function lookupLocationWithCityDb($ip) {
        $reader = new Reader($this->cityDbPath);
        $record = $reader->city($ip);
        return $this->populateRecord($record);
    }

    protected function lookupLocationWithCountryDb($ip) {
        $reader = new Reader(Yii::getAlias('@vendor/dpodium/yii2-geoip/components/GeoIP/GeoLite2-Country.mmdb'));
        $record = $reader->country($ip);
        return $this->populateRecord($record);
    }
    
    protected function populateRecord($record) {
        if (isset($record)) {
            $location = new Location([
                'countryCode' => $record->country->isoCode,
                'countryName' => $record->country->name,
                'continentCode' => $record->continent->code,
                'continentName' => $record->continent->name,
                'city' => isset($record->city->name) ? $record->city->name : null,
                'postalCode' => isset($record->postal->code) ? $record->postal->code : null,
                'latitude' => isset($record->location->latitude) ? $record->location->latitude : null,
                'longitude' => isset($record->location->longitude) ? $record->location->longitude : null,
                'timeZone' => isset($record->location->timeZone) ? $record->location->timeZone : null,
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
                    if (self::_validateIp($ip)) {
                        return $ip;
                    }
                }
            }

            return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
        } else {
            $ip = getenv('HTTP_TRUE_CLIENT_IP');
            if (self::_validateIp($ip)) {
                return $ip;
            }
            return getenv('REMOTE_ADDR');
        }
    }

    private static function _validateIp($ip) {
        if ($ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return false;
            }
            return true;
        }
        return false;
    }

}
