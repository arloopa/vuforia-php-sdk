# Vuforia PHP SDK (unofficial)

[![Latest Stable Version](https://poser.pugx.org/arloopa/vuforia-php-sdk/v/stable.png)](https://packagist.org/packages/arloopa/vuforia-php-sdk)
[![Total Downloads](https://poser.pugx.org/arloopa/vuforia-php-sdk/downloads.png)](https://packagist.org/packages/arloopa/vuforia-php-sdk)
[![Build Status](https://travis-ci.org/arloopa/vuforia-php-sdk.svg?branch=master)](https://travis-ci.org/arloopa/vuforia-php-sdk)
[![StyleCI](https://styleci.io/repos/74761645/shield?style=flat)](https://styleci.io/repos/74761645)
[![codecov.io](https://codecov.io/github/arloopa/vuforia-php-sdk/coverage.svg?branch=master)](https://codecov.io/github/arloopa/vuforia-php-sdk?branch=master)
[![PHP-Eye](https://php-eye.com/badge/arloopa/vuforia-php-sdk/tested.svg?style=flat)](https://php-eye.com/package/arloopa/vuforia-php-sdk)

An unofficial SDK for [Vuforia](https://vuforia.com).

```php
use Vuforia\Vuforia;
use Vuforia\Models\Target;

// Here is the database access and secret keys. You can
// find them on Vuforia's database page
$access_key = '';
$secret_key = '';

Vuforia::config($access_key, $secret_key);

// To get all targets
$targets = Target::all();

// To get specified target
$target = Target::find('target_id');

// To create a new target
Target::create('marker image path (local or remote)', 'name of target', $width, 'metadata', $is_active);

// To update an existing target
// Note that each method bellow makes an API call to Vuforia
$target->changeName('new target name');
$target->changeMarker('new marker image path');
$target->changeMetadata('new metadata');
$target->changeWidth($new_width);
$target->makeInactive();
$target->makeActive();
// or
$target->activate();

// To delete the target
$target->delete();
```

## Installation

```
$ composer require arloopa/vuforia-php-sdk
```
