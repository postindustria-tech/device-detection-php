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

/*
 * @example cloud/webIntegration.php
 *
 * This example demonstrates the evidence->setFromWebRequest() method
 * and client side JavaScript overrides by creating a web server,
 * serving JavaScript created by the device detection engine
 * This JavaScript is then used on the client side to get a more accurate reading
 * for properties by fetching the json response using the overrides as evidence.
 *
 * This example is available in full on [GitHub](https://github.com/51Degrees/device-detection-php/blob/master/examples/cloud/webintegration.php).
 *
 * To run this example, you will need to create a **resource key**.
 * The resource key is used as short-hand to store the particular set of
 * properties you are interested in as well as any associated license keys
 * that entitle you to increased request limits and/or paid-for properties.
 * You can create a resource key using the 51Degrees [Configurator](https://configure.51degrees.com/czdCZr9S).
 *
 * This example uses the 'HardwareName', 'HardwareVendor' and 
 * 'ScreenPixelsWidth' properties, which are pre-populated when creating 
 * a key using the link above.
*/

// First we include the DeviceDetectionPipelineBuilder

require(__DIR__ . "/../../vendor/autoload.php");

use fiftyone\pipeline\devicedetection\DeviceDetectionPipelineBuilder;

// Check if there is a resource key in the environment variable and use
// it if there is one. (This is used for automated testing)
if (isset($_ENV["RESOURCEKEY"])) {
    $resourceKey = $_ENV["RESOURCEKEY"];
} else {
    $resourceKey = "AQQNX4pFKLImbC0U2Eg";
}

if (substr($resourceKey, 0, 2) === "!!") {
    echo "You need to create a resource key at " .
        "https://configure.51degrees.com/czdCZr9S and paste it into " .
        "the code, replacing !!YOUR_RESOURCE_KEY!!.";
    echo "\n<br/>";
    echo "Make sure to include the HardwareName, HardwareVendor and " .
        "ScreenPixelsWidth properties as they are used by this example.\n<br />";
    return;
}

// We create some settings for the JavaScriptBuilder,
// in this case an endpoint to call back to to retrieve additional
// properties populated by client side evidence
// this ?json endpoint is used later to serve results from a special
// json engine automatically included in the pipeline
$javascriptBuilderSettings = array(
    "endpoint" => "/?json"
);

// Make the Device Detection Pipeline and add the JavaScript settings
$builder = new DeviceDetectionPipelineBuilder(array(
    "resourceKey" => $resourceKey,
    "javascriptBuilderSettings" => $javascriptBuilderSettings
));

// Next we build the pipeline. We could additionally add extra engines and/or
// flowElements here before building.
$pipeline = $builder->build();

// We create the flowData object that is used to add evidence to and read
// data from
$flowData = $pipeline->createFlowData();

// We set headers, cookies and more information from the web request
$flowData->evidence->setFromWebRequest();

// Now we process the flowData
$result = $flowData->process();

// Following the JavaScript bundler settings where we created an endpoint called
// ?json, we now use this endpoint (if called) to send back JSON to the client side

if (isset($_GET["json"])) {
    header('Content-Type: application/json');

    echo json_encode($flowData->jsonbundler->json);
    
    return;
}

// In the standard call we check if the hardware name, hardware vendor
// and screenpixelswidth properties exist

$properties = $pipeline->getElement("device")->getProperties();

if (!isset($properties["hardwarename"]) || !isset($properties["hardwarevendor"]) || !isset($properties["screenpixelswidth"])) {
    echo "Make sure to include the HardwareName, HardwareVendor and " .
        "ScreenPixelsWidth properties as they are used by this example.\n<br />";
    return;
}

echo "<p>The following values are determined by sever-side device detection on the first request:</p>";

echo "<dl>";

echo "<dt>";
echo "Hardware name";
echo "</dt>";
echo "<dd>";

if ($result->device->hardwarename->hasValue) {
    echo implode(",", $result->device->hardwarename->value);
} else {
    echo $result->device->hardwarename->noValueMessage;
}

echo "</dd>";

echo "<dt>";
echo "Hardware vendor";
echo "</dt>";
echo "<dd>";
if ($result->device->hardwarevendor->hasValue) {
    echo $result->device->hardwarevendor->value;
} else {
    echo $result->device->hardwarevendor->noValueMessage;
}
echo "</dd>";

echo "<dt>";
echo "Screenpixelswidth";
echo "</dt>";
echo "<dd>";
if ($result->device->screenpixelswidth->hasValue) {
    echo $result->device->screenpixelswidth->value;
} else {
    echo $result->device->screenpixelswidth->noValueMessage;
}
echo "</dd>";

echo "</dl>";

echo "
<p>The information shown below is determined from JavaScript running on the client-side that is able to obtain additional evidence.<br />If no additional information appears then it may indicate an external problem such as JavaScript being disabled in your browser.<br />

<p>Note that the 'Hardware Name' field is intended to illustrate detection of Apple device models as this cannot be determined server-side. This can be tested to some extent using most emulators such as those in the 'developer tools' menu in Google Chrome. However, using real devices will result in more precise model numbers.</p>";

echo "<dl>";

echo "<dt>";
echo "Hardware name";
echo "</dt>";
echo "<dd id='hardwarenameclient'>";
echo "</dd>";

echo "<dt>";
echo "Hardware vendor";
echo "</dt>";
echo "<dd id='hardwarevendorclient'>";
echo "<dd>";

echo "<dt>";
echo "Screenpixelswidth";
echo "</dt>";
echo "<dd id='screenpixelswidthclient'>";
echo "<dd>";

// We get any JavaScript that should be placed in the page and run it.
// This will help provide client side evidence and make a request to the JSON
// endpoint to update extra properties using this evidence.

echo "<script>" . $flowData->javascriptbuilder->javascript . "</script>";

?>

<!-- 
Now we add some additional JavaScript 
that will update the client side properties above if values exist for them 
in the JSON endpoint provided data 
-->

<script>
 window.onload = function(){
   fod.complete(function (data) {
            document.getElementById('hardwarenameclient').innerHTML = data.device["hardwarename"];
            document.getElementById('hardwarevendorclient').innerHTML = data.device.hardwarevendor
            document.getElementById('screenpixelswidthclient').innerHTML = data.device.screenpixelswidth
       });
 }
 </script>
