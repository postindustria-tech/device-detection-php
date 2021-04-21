# 51Degrees PHP Device Detection

![51Degrees](https://51degrees.com/DesktopModules/FiftyOne/Distributor/Logo.ashx?utm_source=github&utm_medium=repository&utm_content=readme_main&utm_campaign=php-open-source "Data rewards the curious") **PHP Device Detection**

[Developer Documentation](https://51degrees.com/device-detection-php/index.html?utm_source=github&utm_medium=repository&utm_content=documentation&utm_campaign=php-open-source "developer documentation")

## Introduction
This project contains the source code for the PHP implementation of 51Degrees' 
cloud-based device detection engine for use with the 
[Pipeline API](https://github.com/51Degrees/pipeline-php-core).

## Examples
To run the examples, you will need PHP and composer installed.
Once these are available, install the dependencies required by the examples. 
Navigate to the repository root and execute:

```
composer install
```

This will create the vendor directory containing autoload.php. 
Now navigate to the examples directory and start a PHP server with the relevant file. 
For example:

```
php -S localhost:3000 cloud/gettingstarted.php
```

This will start a local web server listening on port 3000. 
Open your web browser and browse to http://localhost:3000/ to see the example in action.

## Tests
This repo has tests for the examples. To run the tests, make sure PHPUnit is installed then,
in the root of this repo, call:

```
phpunit --log-junit test-results.xml
```

## On-premise device detection

The on-premise implementation of device detection is much faster but requires more 
memory and processing power than the cloud version. Unfortunately, distributing the 
on-premise package via composer would require the inclusion of binary executables (.so/.dll) 
that are not permitted. If you wish to use the on-premise version then you will need to clone 
the [on-premise repository](https://github.com/51Degrees/device-detection-php-onpremise) and
follow the instructions in the readme to build the required modules. This is
a fairly involved process so feel free to [contact us](mailto:support@51degrees.com) 
if you are having difficulties.

## Development
When making changes to this repository, it may be necessary to link to a local
development version of pipeline dependencies. For information on this, see
 [Composer local path](https://getcomposer.org/doc/05-repositories.md#path).

For exmaple, if a development version of `51degrees/fiftyone.pipeline.core` 
was stored locally, the location would be added with:

```
"repositories": [
	{
		"type": "path",
		"url": "../../path/to/packages/pipeline-php-core"
	}
]
```
then the dependency changed to:

```
"51degrees/fiftyone.pipeline.core": "*"
```
