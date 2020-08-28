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
 * @example cloud/nativeModelLookup.php
 *
 * This example finds the details of devices from the 'native model name'.
 * The native model name can be retrieved by code running on the device (For example, a mobile app).
 * For Android devices, see https://developer.android.com/reference/android/os/Build#MODEL
 * For iOS devices, see https://gist.github.com/soapyigu/c99e1f45553070726f14c1bb0a54053b#file-machinename-swift
 *
 * This example is available in full on [GitHub](https://github.com/51Degrees/device-detection-php/blob/master/examples/cloud/nativeModelLookup.php).
 *
 * To run this example, you will need to create a **resource key**.
 * The resource key is used as short-hand to store the particular set of
 * properties you are interested in as well as any associated license keys
 * that entitle you to increased request limits and/or paid-for properties.
 * You can create a resource key using the 51Degrees [Configurator](https://configure.51degrees.com).
 *
 * Expected output:
 *
 * Which devices are associated with the native model: SC-03L?: Samsung Galaxy S10 SC-03L
 *
*/

require(__DIR__ . "/../../vendor/autoload.php");

use fiftyone\pipeline\cloudrequestengine\CloudRequestEngine;
use fiftyone\pipeline\core\PipelineBuilder;
use fiftyone\pipeline\devicedetection\HardwareProfileCloud;

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

// First we make an instance of the PipelineBuilder which we will add our elements too

$builder = new PipelineBuilder();

// The first element needed is the CloudRequestEngine which makes the call to the cloud service
// This is then used by the HardwareProfileCloud engine.
// To construct this, pass in a settings array with your resourcekey

$builder->add(new CloudRequestEngine(["resourceKey" => $resourceKey]));

// Now we add the HardwareProfileCloud engine. This needs to sit after the CloudRequestEngine. 

$builder->add(new HardwareProfileCloud());

// The pipeline is now ready so we can build it

$pipeline = $builder->build();

// Now we get a native model name to test

$nativeModel = 'SC-03L';

// We create a FlowData object from the pipeline
// this is used to add evidence to and then process

$flowData = $pipeline->createFlowData();

// After creating a flowdata instance, add the native model name as evidence.

$flowData->evidence->set('query.nativemodel', $nativeModel);

// Now we process the FlowData to get results

$flowData->process();

// The result is an array containing the details of any devices that match
// the specified tac.
// The code in this example iterates through this array, outputting the
// vendor and model of each matching device.
    
print("Which devices are associated with the native model: " . $nativeModel . "?: ");

forEach($flowData->hardware->profiles as $profile){

    $hardwareVendor = $profile["hardwarevendor"];
    $hardwareName = $profile["hardwarename"];
    $hardwareModel = $profile["hardwaremodel"];

    if ($hardwareVendor->hasValue && $hardwareName->hasValue and $hardwareModel->hasValue){

        print($hardwareVendor->value . " " . implode(",", $hardwareName->value) . " " . $hardwareModel->value);
    } else {
        print($hardwareVendor->noValueMessage);
    }
}
