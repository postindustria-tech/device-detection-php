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
 * @example cloud/gettingstarted.php
 * 
 * In this example we create a 51Degrees device detection pipeline and use 
 * it to test if a provided user agent string is a mobile device
 *
**/

// First we include the deviceDetectionPipelineBuilder

require(__DIR__ . "/../../vendor/autoload.php");
use fiftyone\pipeline\devicedetection\deviceDetectionPipelineBuilder;

// We then create a pipeline with the builder. To access properties other than 
// IsMobile, create your own recource key for free at https://configure.51degrees.com. 
// To access paid-for properties, you will also need to enter your license key.

$builder = new deviceDetectionPipelineBuilder(array(
    "resourceKey" => "AQS5HKcyqliVnYhx10g",
    "restrictedProperties" => array() // All properties by default
));

// Next we build the pipeline. We could additionally add extra engines 
// and/or flowElements here before building.
$pipeline = $builder->build();

// Here we create a function that checks if a supplied user agent is a mobile
// device, the pipeline can be reused multiple times but a flowData objects 
// it creates can only be processed once
function checkIfMobile($userAgent, $pipeline) {

    // We create the flowData object that is used to add evidence to and 
    // read data from 
    $flowData = $pipeline->createFlowData();

    // We set the user agent
    $flowData->evidence->set("header.user-agent", $userAgent);

    // Now we process the flowData
    $result = $flowData->process();

    echo "Is user agent " . $userAgent . " a mobile device? \n";

    // First we check if the property we're looking for has a meaningful result

    if($result->device->ismobile->hasValue){

        var_dump($result->device->ismobile->value);

    } else {

        // If it doesn't have a meaningful result, we echo out the reason 
        //why it wasn't meaningful

        echo($result->device->ismobile->noValueMessage);

    }


};

// Some example User Agents to test

$desktopUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36';
$iPhoneUA = 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_2 like Mac OS X) AppleWebKit/604.4.7 (KHTML, like Gecko) Mobile/15C114';

checkIfMobile($desktopUA, $pipeline);
checkIfMobile($iPhoneUA, $pipeline);
