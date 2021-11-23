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
            'support_ipv6' => false, //Default value
        ],
        ...
    ],
    ...
];
```

If querying full City data is required, yii2-geoip-city-db must be required as well, see [dpodium/yii2-geoip-city-db](https://github.com/dpodium/yii2-geoip-city-db).

For more information on the data availability, see below.

Usage
-----
All methods accept an IP address as an argument. If no argument is supplied Yii::$app->getRequest()->getUserIP() is used.

    //Along with free DB
    $location = Yii::$app->geoip->lookupLocation();
    $countryCode = Yii::$app->geoip->lookupCountryCode();
    $countryName = Yii::$app->geoip->lookupCountryName();

Location attributes:

    $location->countryCode //Available in both Country and City DB
    $location->countryName //Available in both Country and City DB
    $location->continentCode //Available in both Country and City DB
    $location->continentName //Available in both Country and City DB
    $location->city //Available in only City DB
    $location->postalCode //Available in only City DB
    $location->latitude //Available in only City DB
    $location->longitude //Available in only City DB
    $location->timeZone //Available in only City DB
