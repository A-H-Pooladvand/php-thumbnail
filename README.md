# PHP Thumbnail

PHP Thumbnail is a **PHP image manipulation** library providing an easier and expressive way to create thumbnails to improve **SEO** of your website.

## What it can do?
- Cropping image
- Resizing image
- Fitting image
- Changing quality of image
- Change width and height of image

## Getting started
- [Requirements](#requirements)
- [Installation](#installation)
- [Parameters](#parameters)
- [Examples](#examples)
- [License](#license)

## Requirements

- PHP >=7.1
- intervention/image ^2.4@dev

## Installation
`composer require ahp/thumbnail`  

Once installed you can use it  
`// Via helper function`
`img($path, $width, $height, $mode, $quality)`  
OR  
`// Via class`
`\Thumb\Thumb::make($path, $width, $height, $mode, $quality)`


## Parameters
- path : Path of image `string|required`
- width : Desired width `int|null`
- height : Desired height `int|null`
- mode: one of `fit` or `resize` or `crop`
- quality : Quality of image `int|null = 100`

## Examples

```php
// Using img() helper function
<img src="<?= img($path, $width, $height, $mode, $quality) ?>">

// Full example
<img src="<?= img('files/animals/koala.jpg', 500, 200, 'crop', 50) ?>">

// we can ommit width or height but one of them is required to calculate it from other one
<img src="<?= img('files/animals/koala.jpg', 500) ?>">

// OR
<img src="<?= img('files/animals/koala.jpg', null, 200) ?>">
```

## License

PHP Thumbnail is licensed under the [MIT License](http://opensource.org/licenses/MIT).

Copyright 2019 **Amirhossein Pooladvand**
