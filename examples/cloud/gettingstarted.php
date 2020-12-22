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
 * @example cloud/gettingStarted.php
 *
 * This example shows how a simple device detection pipeline can be built
 * that checks if a provided User-Agent is a mobile device
 *
 * This example is available in full on [GitHub](https://github.com/51Degrees/device-detection-php/blob/master/examples/cloud/gettingStarted.php).
 *
 * To run this example, you will need to create a **resource key**.
 * The resource key is used as short-hand to store the particular set of
 * properties you are interested in as well as any associated license keys
 * that entitle you to increased request limits and/or paid-for properties.
 * You can create a resource key using the 51Degrees [Configurator](https://configure.51degrees.com).
 *
 * Expected output:
 *
 * ```
 * Is User-Agent 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36' a mobile device?:
 * No
 * 
 * Is User-Agent 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_2 like Mac OS X) AppleWebKit/604.4.7 (KHTML, like Gecko) Mobile/15C114' a mobile device?:
 * Yes
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
    echo "Make sure to include the ismobile property " .
        "as it is used by this example.\n<br />";
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

$serializedPipelineFile = __DIR__ . "getting_started_pipeline.pipeline";
if(!file_exists($serializedPipelineFile)){
    $pipeline = $builder->build();
    file_put_contents($serializedPipelineFile, serialize($pipeline));
} else {
    $pipeline = unserialize(file_get_contents($serializedPipelineFile));
}

// Here we create a function that checks if a supplied User-Agent is a
// mobile device
function gettingstarted_checkifmobile($pipeline, $userAgent = "")
{

    // We create the flowData object that is used to add evidence to and read data from
    $flowData = $pipeline->createFlowData();

    // Add the User-Agent as evidence
    $flowData->evidence->set("header.user-agent", $userAgent);

    // Now we process the flowData
    $result = $flowData->process();

    // First we check if the property we're looking for has a meaningful result
    print("Is User-Agent '<b>" . $userAgent . "</b>' a mobile device?:");
    print("</br>\n");

    if ($result->device->ismobile->hasValue) {
        if ($result->device->ismobile->value) {
            print("Yes");
        } else {
            print("No");
        }
    } else {

        // If it doesn't have a meaningful result, we echo out the reason why
        // it wasn't meaningful
        print($result->device->ismobile->noValueMessage);
    }

    print("</br>\n");
    print("</br>\n");
}

// Some example User-Agents to test

$desktopUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36';
$iPhoneUA = 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_2 like Mac OS X) AppleWebKit/604.4.7 (KHTML, like Gecko) Mobile/15C114';

gettingstarted_checkifmobile($pipeline, $desktopUA);
gettingstarted_checkifmobile($pipeline, $iPhoneUA);
