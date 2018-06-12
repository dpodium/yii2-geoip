Yii2 GeoIP
==========
Yii2 Component to allow for easy usage of the MaxMind Free dbs.

Based on package phiphi1992/Yii2-GeoIP by Phi Hoang Xuan.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require dpodium/yii2-geoip "~2.0.0"
```

or add

```
"dpodium/yii2-geoip": "~2.0.0"
```

to the require section of your `composer.json` file.

Version ~2.0.0 Difference
-----
Version ~2.0.0 uses the new GeoLite2 version of the db instead of the Legacy GeoLite which is now deprecated.

Database can be found here: [https://dev.maxmind.com/geoip/geoip2/geolite2/](https://dev.maxmind.com/geoip/geoip2/geolite2/)


Component Setup
-----
Once the extension is installed, simply modify your application configuration as follows:
```php
return [
    'components' => [
    ...
        'geoip' => [
                   'class' => 'dpodium\yii2\geoip\components\CGeoIP',
                   'cityDbPath' => '/path/to/maxmind/citydb', //Optional, will be parsed with Yii::getAlias
               ],
        ...
    ],
    ...
];
```

If cityDbPath is configured, the City db will be used by the extension to query the IP and get full data. Otherwise, the extension will
use the default Country db which is shipped along with this extension to do the query, but only partial data will be available.

For more information on the data availability, see below.

Usage
_____
All methods accept an IP address as an argument. If no argument is supplied Yii::$app->getRequest()->getUserIP() is used.

    //Along with free DB
    $location = Yii::$app->geoip->lookupLocation();
    $countryCode = Yii::$app->geoip->lookupCountryCode();
    $countryName = Yii::$app->geoip->lookupCountryName();

Location attributes:

    $location->countryCode
    $location->countryName
    $location->continentCode
    $location->continentName
    $location->city
    $location->postalCode
    $location->latitude
    $location->longitude
    $location->timeZone
