<?php
/* *********************************************************************
 * This Original Work is copyright of 51 Degrees Mobile Experts Limited.
 * Copyright 2019 51 Degrees Mobile Experts Limited, 5 Charlotte Close,
 * Caversham, Reading, Berkshire, United Kingdom RG4 7BY.
 *
 * This Original Work is licensed under the European Union Public Licence (EUPL)
 * v.1.2 and is subject to its terms as set out below.
 *
 * If a copy of the EUPL was not distributed with this file, You can obtain
 * one at https://opensource.org/licenses/EUPL-1.2.
 *
 * The 'Compatible Licences' set out in the Appendix to the EUPL (as may be
 * amended by the European Commission) shall be deemed incompatible for
 * the purposes of the Work and the provisions of the compatibility
 * clause in Article 5 of the EUPL shall not apply.
 *
 * If using the Work as, or as part of, a network application, by
 * including the attribution notice(s) required under Article 5 of the EUPL
 * in the end user terms of the application under an appropriate heading,
 * such notice(s) shall fulfill the requirements of that article.
 * ********************************************************************* */

/**
 * @example cloud/metadata.php
 * @include{doc} example-metadata-hash.txt
 *
 * This example is available in full on [GitHub](https://github.com/51Degrees/device-detection-php/blob/master/examples/cloud/matchMetrics.php).
 *
 * @include{doc} example-require-datafile.txt
 *
 * Expected output
 * (Note that the exact list of properties will depend on the resource key)
 *
 * ```
 * Properties returned when making requests with this resource key:
 * Svg (Boolean) of category Supported Media
 * Video (Boolean) of category Supported Media
 * SupportsTls/Ssl (Boolean) of category Supported Media
 * SupportsWebGL (Boolean) of category Supported Media
 * WebP (Boolean) of category Supported Media
 * Jpeg2000 (Boolean) of category Supported Media
 * 
 * Checking support for User-Agent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36'
 * svg : Yes
 * video : Yes
 * supportstls/ssl : Yes
 * supportswebgl : Yes
 * supportswebp: Yes
 * supportsJpeg2000: No
 * ```
*/

// First we include the deviceDetectionPipelineBuilder

require(__DIR__ . "/../../vendor/autoload.php");

use fiftyone\pipeline\devicedetection\DeviceDetectionPipelineBuilder;

// We then create a pipeline with the builder. Create your own resource key for free at https://configure.51degrees.com.

// Check if there is a resource key in the environment variable and use
// it if there is one. You will need to switch this for your own resource key.

if (isset($_ENV["RESOURCEKEY"])) {
    $resourceKey = $_ENV["RESOURCEKEY"];
} else {
    $resourceKey = "!!YOUR_RESOURCE_KEY!!";
}

if ($resourceKey === "!!YOUR_RESOURCE_KEY!!") {
    echo "You need to create a resource key at " .
        "https://configure.51degrees.com and paste it into the code, " .
        "replacing !!YOUR_RESOURCE_KEY!!.";
    echo "\n<br/>";
    echo "Make sure to include the all properties in the " +
        "Supported Media category as they are used by this " +
        "example.\n<br />";
    return;
}

$builder = new DeviceDetectionPipelineBuilder(array(
    "resourceKey" => $resourceKey
));

// Next we build the pipeline. We could additionally add extra engines and/or
// flowElements here before building.

// To stop having to construct the pipeline
// and re-make cloud API requests used during construction on every page load,
// we recommend caching the serialized pipeline to a database or disk.
// Below we are using PHP's serialize and writing to a file if it doesn't exist

$serializedPipelineFile = __DIR__ . "metadata_pipeline.pipeline";
if(!file_exists($serializedPipelineFile)){
    $pipeline = $builder->build();
    file_put_contents($serializedPipelineFile, serialize($pipeline));
} else {
    $pipeline = unserialize(file_get_contents($serializedPipelineFile));
}

// Show all the properties in the device element
echo "Properties returned when making requests with this resource key:";
echo "<dl>";
foreach ($pipeline->getElement("device")->properties as $property) {
    echo("<b>" . $property["name"] . "</b> (" . $property["type"] . ") of category " . $property["category"]);
    echo "</br>\n";
};
echo "</dl>";

echo "<br />";

function check_browser_support($userAgent, $pipeline)
{

    // We create the flowData object that is used to add evidence to and
    // read data from
    $flowData = $pipeline->createFlowData();

    // We set the User-Agent
    $flowData->evidence->set("header.user-agent", $userAgent);

    // Now we process the flowData
    $result = $flowData->process();

    // We use getWhere to find all the properties of a certain category
    // and fetch their values
    $supported = $flowData->getWhere("category", "Supported Media");

    echo "Checking support for User-Agent: '<b>" . $userAgent . "</b>'";

    echo "</br>\n";

    foreach ($supported as $key => $value) {
        echo $key . " : ";

        // First we check if the property we're looking for has a meaningful
        // result (see the failureToMatch example for more information)

        if ($result->device->{$key}->hasValue) {
            if ($result->device->{$key}->value) {
                print("Yes");
            } else {
                print("No");
            }
        } else {
            print($result->device->{$key}->noValueMessage);
        }

        echo "</br>\n";
    }
    
    echo "</br>\n";
    echo "</br>\n";
}

// Test a user agent
$desktopUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36';

check_browser_support($desktopUA, $pipeline);
