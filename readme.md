# Undemanding Difference

[![Build Status](http://img.shields.io/travis/undemanding/difference.svg?style=flat-square)](https://travis-ci.org/undemanding/difference)
[![Code Quality](http://img.shields.io/scrutinizer/g/undemanding/difference.svg?style=flat-square)](https://scrutinizer-ci.com/g/undemanding/difference)
[![Code Coverage](http://img.shields.io/scrutinizer/coverage/g/undemanding/difference.svg?style=flat-square)](https://scrutinizer-ci.com/g/undemanding/difference)
[![Version](http://img.shields.io/packagist/v/undemanding/difference.svg?style=flat-square)](https://packagist.org/packages/undemanding/difference)
[![License](http://img.shields.io/packagist/l/undemanding/difference.svg?style=flat-square)](license.md)

Calculate the difference in images.

## Usage

```php
use Undemanding\Difference\Image;
use Undemanding\Difference\Method\EuclideanDistance;

$image1 = new Image("/path/to/image1.png");
$image2 = new Image("/path/to/image2.png");

$difference = $image1->difference($image2, new EuclideanDistance());

$boundary = $difference->boundary(); // → ["left" => ..., "top" => ...]
$percentage = $difference->percentage(); // → 14.03...
```

You can ignore smaller changes by scaling and/or reducing the differences by a standard deviation:

```php
$difference->withScale(10)->withReducedStandardDeviation();
```

## Versioning

This library follows [Semver](http://semver.org). According to Semver, you will be able to upgrade to any minor or patch version of this library without any breaking changes to the public API. Semver also requires that we clearly define the public API for this library.

All methods, with `public` visibility, are part of the public API. All other methods are not part of the public API. Where possible, we'll try to keep `protected` methods backwards-compatible in minor/patch versions, but if you're overriding methods then please test your work before upgrading.

