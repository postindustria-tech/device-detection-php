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
 * @example cloud/configurefromfile.php
 * 
 * In this example we create a 51Degrees device detection pipeline from a 
 * JSON configuration file and use it to test if a device is a mobile device.
 *
**/

// First we include the deviceDetectionPipelineBuilder and other 
// required engines

require(__DIR__ . "/../../vendor/autoload.php");

use fiftyone\pipeline\devicedetection\deviceDetectionCloud;
use fiftyone\pipeline\core\pipelineBuilder;
use fiftyone\pipeline\cloudrequestengine\cloudRequestEngine;

// We create a simple pipeline to access the engine in a JSON file
$builder = new pipelineBuilder();
$pipeline = $builder->buildFromConfig("./pipeline.json");

// The JSON file used here is

// {
//     "PipelineOptions": {
//       "Elements": [
//         {
//           "BuilderName": "fiftyone\\pipeline\\cloudrequestengine\\cloudRequestEngine",
//           "BuildParameters": {
//             "resourceKey": "Obtain a resource key from https://configure.51degrees.com"
//           }
//         },
//         {
//           "BuilderName": "fiftyone\\pipeline\\devicedetection\\deviceDetectionCloud"
//         },
//         {
//           "BuilderName": "fiftyone\\pipeline\\javascriptbundler\\javaScriptBundlerElement"
//         }
//       ]
//     }
//   }

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

// First we check if the property we're looking for has a meaningful result

if($result->device->ismobile->hasValue){

    var_dump($result->device->ismobile->value);

} else {

    // If it doesn't have a meaningful result, we echo out the reason why 
    // it wasn't meaningful

    echo($result->device->ismobile->noValueMessage);

}

// We get any JavaScript that should be placed in the page and run it, 
// this will set cookies and other information allowing us to access extra 
// properties such as device->screenpixelwidth.

echo "<script>" . $flowData->javascriptbundler->javascript . "</script>";



