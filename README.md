Yii2 GeoIP
==========
Yii2 Component to allow for easy usage of the MaxMind Free dbs.

Based on package phiphi1992/Yii2-GeoIP by Phi Hoang Xuan.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require dpodium/yii2-geoip "*"
```

or add

```
"dpodium/yii2-geoip": "*"
```

to the require section of your `composer.json` file.


Component Setup
-----
Once the extension is installed, simply modify your application configuration as follows:
```php
return [
    'components' => [
    ...
        'geoip' => [
                   'class' => 'dpodium\yii2\geoip\components\CGeoIP',
                   'mode' => 'STANDARD',  // Choose MEMORY_CACHE or STANDARD mode
               ],
        ...
    ],
    ...
];
```

Usage
_____
All methods accept an IP address as an argument. If no argument is supplied Yii::$app->getRequest()->getUserIP() is used.

    //Along with free DB
    $location = Yii::$app->geoip->lookupLocation();
    $countryCode = Yii::$app->geoip->lookupCountryCode();
    $countryName = Yii::$app->geoip->lookupCountryName();

    //Required Paid DB
    $org = Yii::$app->geoip->lookupOrg();
    $regionCode = Yii::$app->geoip->lookupRegion();

Location attributes:

    $location->countryCode
    $location->countryCode3
    $location->countryName
    $location->region
    $location->regionName
    $location->city
    $location->postalCode
    $location->latitude
    $location->longitude
    $location->areaCode
    $location->dmaCode
    $location->timeZone


